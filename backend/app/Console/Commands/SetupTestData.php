<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\RoleSeeder;

class SetupTestData extends Command
{
    protected $signature = 'test:setup';
    protected $description = 'Setup test data including roles';

    public function handle(): int
    {
        $this->call('migrate:fresh');
        $this->call('db:seed', ['--class' => 'RoleSeeder']);
        $this->info('Test data setup complete!');
        return 0;
    }
}

