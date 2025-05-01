<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:invoice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $invoice = Invoice::query()->latest()->first();
        $user = $invoice->user;
        $plan = $invoice->subscription->plan;

        echo $invoice->invoice_number . PHP_EOL;
        echo $user->name . PHP_EOL;
        echo $plan->title . PHP_EOL;

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'user' => $user,
            'plan' => $plan,
        ]);

        $filename = 'invoices/' . $invoice->invoice_number . '.pdf';

        Storage::put($filename, $pdf->output());
    }
}
