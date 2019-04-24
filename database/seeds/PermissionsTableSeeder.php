<?php

use App\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
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
        	$permission = new Permission;
        	$number = $i+1;
	        $permission->name = "This is permission $number";
	        $permission->description = "This is description of permission $number";
	        $permission->save();
        }
    }
}
