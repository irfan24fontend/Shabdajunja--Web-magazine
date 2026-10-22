<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@shabdakunja.com'],
            [
                'name' => 'Admin',
                'password' => 'ChangeMe@123',   // model e 'hashed' cast ache, auto hash hobe
                'role' => 'admin',
            ]
        );

        $categories = [
            'গল্প', 'কবিতা', 'প্রবন্ধ', 'গদ্য',
            'সাহিত্য আলোচনা', 'অনুবাদ', 'সাক্ষাৎকার', 'ভ্রমণ সাহিত্য',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}