<?php

namespace App\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Process\Process;

class WhatsAppServer extends \Illuminate\Console\Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:start
                        {--port=8081 : Port to run the server}
                        {--timezone=Asia/Jakarta : Timezone for the server}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Starts the WhatsApp Web gateway server (e.g. for automated messaging)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $port = $this->option('port') ?? config('whatsapp.port');
        $timezone = $this->option('timezone') ?? config('whatsapp.timezone');

        $this->info('Detecting system and starting WhatsApp server...');

        $os = strtolower(PHP_OS_FAMILY); // 'Darwin', 'Windows', 'Linux'
        $arch = php_uname('m'); // e.g. 'x86_64', 'arm64'

        $this->info("Detected OS: $os, Arch: $arch");

        $this->info('Starting WhatsApp server...');
        $path = $this->getBinary($os, $arch);
        if (!$path) {
            $this->error("Failed to fetch whatsapp server for platform: OS=$os ARCH=$arch");
            return Command::FAILURE;
        }

        if (!str_starts_with($os, 'Windows')) {
            chmod($path, 0755);
        }

        $process = new Process([
            $path,
            "--port=$port",
        ], null, [
            "TZ" => $timezone,
            "WEBHOOK_FORMAT" => "json",
            "SESSION_DEVICE_NAME" => config('app.name'),
            "WUZAPI_ADMIN_TOKEN" => config('whatsapp.token'),
            "WUZAPI_GLOBAL_WEBHOOK" => route("webhook.whatsapp")
        ]);
        $process->setTimeout(null);
        $process->start();

        foreach ($process as $type => $data) {
            echo $data;
        }

        return Command::SUCCESS;
    }

    private function getBinary(string $os, string $arch): string|null
    {
        $ext = "";
        if ($os === "Windows") {
            $ext = ".exe";
        }

        if ($arch === 'x86_64') {
            $arch = "amd64";
        }

        $fileName = "{$os}-{$arch}{$ext}";
        $path =  base_path("app/Binary/whatsapp/$fileName");
        if (file_exists($path)) {
            return $path;
        }

        $this->info('File not found. Downloading from server...');

        $url = "https://github.com/iqbaleff214/iqbaleff214/releases/download/whatsapp/$fileName";
        $readStream = fopen($url, 'rb');
        if (!$readStream) {
            $this->error("Failed to open $url");
            return null;
        }

        $writeStream = fopen($path, 'wb');
        if (!$writeStream) {
            fclose($readStream);
            $this->error("Failed to write $path");
            return null;
        }

        $headers = get_headers($url, 1);
        $totalSize = isset($headers['Content-Length'][1]) ? (int) $headers['Content-Length'][1] : 0;

        $downloaded = 0;
        $chunkSize = 1024 * 8; // 8 KB

        $progress = $this->output->createProgressBar($totalSize);
        $progress->start();

        while (!feof($readStream)) {
            $chunk = fread($readStream, $chunkSize);
            if ($chunk === false) {
                $this->error("Error reading data from stream.");
                fclose($readStream);
                fclose($writeStream);
                unlink($path);
                return null;
            }

            fwrite($writeStream, $chunk);
            $downloaded += strlen($chunk);
            $progress->advance(strlen($chunk));
        }

        $progress->finish();
        echo PHP_EOL;

        fclose($readStream);
        fclose($writeStream);

        if ($totalSize > 0 && filesize($path) !== $totalSize) {
            $this->error("File downloaded incompletely. Expected $totalSize bytes, got " . filesize($path));
            unlink($path);
            return null;
        }

        return $path;
    }
}
