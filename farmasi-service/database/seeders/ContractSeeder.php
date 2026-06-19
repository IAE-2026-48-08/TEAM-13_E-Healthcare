<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the contract seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $tenants = Tenant::factory()->count(10)->create();
        }

        foreach ($tenants as $tenant) {
            Contract::factory()->count(3)->create(['tenant_id' => $tenant->id]);
        }
    }
}
