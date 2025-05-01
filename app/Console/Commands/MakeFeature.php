<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class MakeFeature extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:feature {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new feature to a pricing plans';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $feature = $this->argument('name');
        $feature = str_replace(' ', '_', strtolower($feature));
        $featureName = $feature;
        $variableName = $this->snakeToPascalCase($feature);

        $type = $this->choice('What feature type do you want?', ['boolean', 'number', 'string',], 0);
        $type1 = $type;

        // Feature Enum PHP
        $path = 'app/Enum/Feature.php';
        $lines = file($path);
        foreach ($lines as $index => $line) {
            $current = trim($line);

            if (str_contains($current, $featureName)) {
                $this->error('Feature already exists!');
                exit(1);
            }

            if (str_contains($current, 'max_device_login')) {
                $content = $line;
                $content = str_replace('max_device_login', $featureName, $content);
                $content = str_replace('MaxDeviceLogin', $variableName, $content);
                array_splice($lines, $index + 1, 0, $content);
            }

            if (str_contains($current, 'self::MaxDeviceLogin => 1')) {
                $content = $line;
                $content = str_replace('MaxDeviceLogin', $variableName, $content);
                $content = str_replace('1', match ($type1) {
                    'boolean' => 'false',
                    'number' => '1',
                    'string' => '""',
                }, $content);
                array_splice($lines, $index + 2, 0, $content);
            }

            if (str_contains($current, 'self::MaxDeviceLogin->value => DB::table(\'sessions\')')) {
                $content = $line;
                $content = str_replace('MaxDeviceLogin', $variableName, $content);
                $content = str_replace('DB::table(\'sessions\')', match ($type1) {
                    'boolean' => 'false,',
                    'number' => '1,',
                    'string' => '"",',
                }, $content);
                array_splice($lines, $index + 2, 0, $content);
            }
        }
        file_put_contents($path, implode('', $lines));

        // Feature Enum TS
        $path = 'resources/js/types/enum.ts';
        $lines = file($path);
        foreach ($lines as $index => $line) {
            $current = trim($line);

            if (str_contains($current, 'max_device_login')) {
                $content = $line;
                $content = str_replace('MaxDeviceLogin', $variableName, $content);
                $content = str_replace('max_device_login', $featureName, $content);
                array_splice($lines, $index + 1, 0, $content);
                break;
            }
        }
        file_put_contents($path, implode('', $lines));

        // Feature type TS
        $path = 'resources/js/types/index.d.ts';
        $lines = file($path);
        foreach ($lines as $index => $line) {
            $current = trim($line);

            if (str_contains($current, 'FeatureEnum.MaxDeviceLogin')) {
                $content = $line;
                $content = str_replace('MaxDeviceLogin', $variableName, $content);
                $content = str_replace('number', $type1, $content);
                array_splice($lines, $index + 1, 0, $content);
                break;
            }
        }
        file_put_contents($path, implode('', $lines));

        // Form vue
        $path = 'resources/js/pages/pricing-plan/partials/PricingPlanModal.vue';
        $lines = file($path);
        foreach ($lines as $index => $line) {
            $current = trim($line);

            if (str_contains($current, 'max_device_login: 1')) {
                $content = $line;
                $content = str_replace('max_device_login', $featureName, $content);
                $content = str_replace('1', match ($type1) {
                    'boolean' => 'false',
                    'number' => '1',
                    'string' => '""',
                }, $content);
                array_splice($lines, $index + 1, 0, $content);
            }

            if (str_contains($current, 'form.features.max_device_login = i.features[\'max_device_login\']')) {
                $content = $line;
                $content = str_replace('max_device_login', $featureName, $content);
                $content = str_replace('];', match ($type1) {
                    'boolean' => '] ?? false;',
                    'number' => '] ?? 1;',
                    'string' => '] ?? "";',
                }, $content);
                array_splice($lines, $index + 2, 0, $content);
                break;
            }
        }
        file_put_contents($path, implode('', $lines));
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'name' => ['What should the feature be named?', 'E.g. max_device_login'],
        ];
    }

    private function snakeToPascalCase(string $str): string {
        return implode('', array_map('ucfirst', explode('_', $str)));
    }
}
