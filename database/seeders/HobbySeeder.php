<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Hobby;

class HobbySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hobby = ['Blogging','Reading','Journaling','Gardening','Hiking'];

        foreach ($hobby as $name) {
            Hobby::updateOrCreate([
                'name'=>$name,
            ],[
                'name'=>$name,
            ]);
        }
    }
}
