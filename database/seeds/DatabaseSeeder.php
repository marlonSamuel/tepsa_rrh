<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$this->call(RolSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CarnetSeeder::class);
        $this->call(TurnoSeeder::class);
        $this->call(CargoSeeder::class);
        $this->call(AsignacionSeeder::class);
        $this->call(PrestacionSeeder::class);
    }
}
