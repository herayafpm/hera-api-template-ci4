<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitSeeder extends Seeder
{
	public function run()
	{
		$this->call(GroupSeeder::class);
		$this->call(AdminSeeder::class);
		$this->call(PermissionSeeder::class);
		$this->call(ClientSeeder::class);
	}
}
