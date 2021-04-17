<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email','tismenetski@gmail.com')->first();

        if (!$user){
            User::create([
                'name' => 'Stas Tismenetski',
                'email' =>'tismenetski@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin'
            ]);
        }
    }
}
