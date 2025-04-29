<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::firstOrCreate([
            'name' => 'Electronics',
            'description' => 'Devices and gadgets.',
        ]);

        Category::firstOrCreate([
            'name' => 'Clothing',
            'description' => 'Apparel and accessories.',
        ]);

        Category::firstOrCreate([
            'name' => 'Groceries',
            'description' => 'Everyday consumables.',
        ]);
    }
}
