<?php

namespace App\Database\Seeds;

use App\Models\GroupModel;
use CodeIgniter\Database\Seeder;

class GroupSeeder extends Seeder
{
	public function run()
	{
		$group_model = model(GroupModel::class);
		$datas = [
			[
				'nama' => 'hera_superadmin',
				'desc' => 'Superadmin App Hera',
			],
		];
		foreach ($datas as $data) {
			$group_model->save($data);
		}
	}
}
