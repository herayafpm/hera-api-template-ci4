<?php

namespace App\Database\Seeds;

use App\Entities\AdminEntity;
use App\Models\AdminModel;
use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
	public function run()
	{
		$admin_model = model(AdminModel::class);
		$password = 'admin';
		$datas = [
			[
				'username' => 'superadmin',
				'nama' => 'Super Admin',
				'password' => $password,
				'groups' => [
					'hera_superadmin',
				],
			],
		];
		$group_model = model(GroupModel::class);
		$user_group_model = model(UserGroupModel::class);
		foreach ($datas as $data) {
			$admin_entity = new AdminEntity($data);
			if ($admin_model->save($admin_entity)) {
				$username = $admin_entity->username;
				foreach ($data['groups'] as $group) {
					$group = $group_model->findGroupByName($group);
					if ($group) {
						$user_group_model->save(['group_id' => $group['id'], 'username' => $username]);
					}
				}
			}
		}
	}
}
