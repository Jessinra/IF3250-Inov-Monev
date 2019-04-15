<?php

use App\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $number_of_seeds = 10;
        for ($i = 0; $i < $number_of_seeds; $i++) {
            $role = new Role;
            $number = $i+1;
            $role->name = "This is role $number";
            $role->description = "This is description of role $number";
            $role->save();
            if ($i < 5) {
                $role->addPermissions(1);
            } else {
                $role->addPermissions(10);
            }
        }
    }
}
