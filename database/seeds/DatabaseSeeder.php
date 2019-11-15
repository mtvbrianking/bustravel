<?php
 namespace glorifiedking\BusTravel\Seeds;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       Eloquent::unguard();
        // $this->call(UsersTableSeeder::class);
       $this->call(Permission_Roles_UsersSeeder::class);
    }
}
