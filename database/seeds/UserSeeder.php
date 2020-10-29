<?php

use App\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = new User();
        $data->email = "secret@secret.com";
        $data->password = bcrypt("secret");
        $data->rol_id = 1;

        $data->save();
    }
}
