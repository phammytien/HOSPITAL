<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            UserSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            PurchaseRequestSeeder::class,
            PurchaseRequestItemSeeder::class,
            PurchaseOrderSeeder::class,
            PurchaseOrderItemSeeder::class,
            PurchaseFeedbackSeeder::class,
            FileSeeder::class,
            PurchaseRequestWorkflowSeeder::class,
            NotificationsSeeder::class,
            SupplierSeeder::class,
            WarehouseSeeder::class,
            WarehouseInventorySeeder::class,
            InventorySeeder::class,
            SqlDataSeeder::class,
        ]);
    }
}
