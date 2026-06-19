<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
class GenerateApiKey extends Command
{
    protected $signature = 'apikey:generate';
    protected $description = 'Generate a new API Key for this service';
    public function handle(): void
    {
        $key = bin2hex(random_bytes(32));
        $this->info('Generated API Key:');
        $this->line($key);
    }
}
