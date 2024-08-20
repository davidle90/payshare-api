<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\Payment;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        Group::factory(3)->create();

        $groups = Group::all();
        foreach($groups as $group){
            $group->members()->attach($group->owner_id);
        }

        User::factory()->create([
            'reference_id' => 'asd',
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'is_admin' => true
        ]);
    }
}
