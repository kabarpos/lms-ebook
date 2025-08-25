<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create instructors
        $instructors = [
            [
                'name' => 'John Doe',
                'email' => 'instructor1@example.com',
                'password' => Hash::make('password'),
                'photo' => 'https://via.placeholder.com/200x200/6366f1/ffffff?text=John',
                'occupation' => 'Senior Full Stack Developer',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'instructor2@example.com',
                'password' => Hash::make('password'),
                'photo' => 'https://via.placeholder.com/200x200/ec4899/ffffff?text=Jane',
                'occupation' => 'UI/UX Design Expert',
            ],
            [
                'name' => 'David Wilson',
                'email' => 'instructor3@example.com',
                'password' => Hash::make('password'),
                'photo' => 'https://via.placeholder.com/200x200/10b981/ffffff?text=David',
                'occupation' => 'Data Science Specialist',
            ],
        ];

        foreach ($instructors as $instructorData) {
            $instructor = User::create($instructorData);
            $instructor->assignRole('instructor');
        }

        // Create students using factory
        $students = User::factory(7)->create();
        
        foreach ($students as $student) {
            $student->assignRole('student');
        }
    }
}