<?php

namespace App\Database\Seeds;

use App\Models\ClientModel;
use App\Models\ClientPermissionModel;
use App\Models\PermissionModel;
use CodeIgniter\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run()
    {
        $client_model = model(ClientModel::class);
        $datas = [
            [
                'application_id' => "ea420881-6575-4aa3-9970-70f77339e61e",
                'nick_name' => 'hera',
                'nama' => 'Hera App',
                // 'hit_limit' => null,
                'access_token' => "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhcHBsaWNhdGlvbl9pZCI6ImVhNDIwODgxLTY1NzUtNGFhMy05OTcwLTcwZjc3MzM5ZTYxZSJ9.BnwyvK2EzoGZ20tus2hOsYoBdSydkTxQdwIjaDSYD6EAM4simDEBjEhVxrgTwKW03m3mn06T5k7VggzAhHSrZW_Jm1X1TUj4nP08el6voQvUanMmuCf2F9RVPE8VuIW9PUtbgyhQfuxpZF8FpeKasEr5puEW4njCicNSe3_wy9dhlO29YTwN-m2OWp4i-Agt_1NH8eFp83BZyv9L-yllD6zXp-kBWwf580GTM5y4fgQxg3R4maRLWvZPlAvkkhNlnuYPEtLZxz00B-AyWbgc6_F2mteqY_fP_llgPX4gFuHyIBqg5Cjvh1DL08jVhB_rrrkMoPaFQzA9S6gEVXCuEg",
                // 'access_token_expired' => "2021-02-21 23:59:59",
                // 'hit_limit' => null,
                'permissions'              => [
                    'hera_can_login'
                ],
            ],
        ];
        $permission_model = model(PermissionModel::class);
        $client_permission_model = model(ClientPermissionModel::class);
        foreach ($datas as $data) {
            if ($client_model->save($data)) {
                $client_id = $client_model->getInsertID();
                foreach ($data['permissions'] as $permission) {
                    $permission = $permission_model->findPermissionByName($permission);
                    if ($permission) {
                        $client_permission_model->save(['client_id' => $client_id, 'permission_id' => $permission['id']]);
                    }
                }
            }
        }
    }
}
