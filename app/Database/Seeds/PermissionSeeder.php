<?php

namespace App\Database\Seeds;

use App\Models\GroupModel;
use App\Models\GroupPermissionModel;
use App\Models\PermissionModel;
use CodeIgniter\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permission_model = model(PermissionModel::class);
        $appName="hera";
        $datas = [
            [
                "nama" => "{$appName}_can_get_profil",
                "desc" => "{$appName} Get Data Profil (nama & username)",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_login",
                "desc" => "{$appName} Client bisa login",
                "groups" => [
                    "{$appName}_superadmin"
                ]
            ],
            [
                "nama" => "{$appName}_can_update_profil",
                "desc" => "{$appName} Client Bisa update Profil",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_groups",
                "desc" => "{$appName} Client Bisa mengambil group yang sedang aktif",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_list_client",
                "desc" => "{$appName} Client Bisa mendapatkan semua client",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_client",
                "desc" => "{$appName} Client Bisa mendapatkan client sesuai ID",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_client_permission",
                "desc" => "{$appName} Client Bisa mendapatkan semua permission yang ada dan permission dari client sesuai ID",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_save_client_permission",
                "desc" => "{$appName} Client Bisa menyimpan permission client (tambah dan hapus)",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_restore_client",
                "desc" => "{$appName} Client Bisa mengembalikan client yang telah dihapus",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_update_client",
                "desc" => "{$appName} Client Bisa mengupdate data client",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_create_client",
                "desc" => "{$appName} Client Bisa menambah client",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_delete_client",
                "desc" => "{$appName} Client Bisa menghapus client",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_list_group",
                "desc" => "{$appName} Client Bisa mendapatkan semua group",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_group",
                "desc" => "{$appName} Client Bisa mendapatkan data group sesuai ID",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_group_permission",
                "desc" => "{$appName} Client Bisa mendapatkan semua permission group",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_save_group_permission",
                "desc" => "{$appName} Client Bisa menyimpan group permission (tambah & hapus)",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_restore_group",
                "desc" => "{$appName} Client Bisa mengembalikan group yang telah dihapus",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_delete_group",
                "desc" => "{$appName} Client Bisa menghapus group",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_update_group",
                "desc" => "{$appName} Client Bisa memperbaharui data group",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_create_group",
                "desc" => "{$appName} Client Bisa menambah group",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_list_user",
                "desc" => "{$appName} Client Bisa mendapatkan semua user {$appName}",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_user",
                "desc" => "{$appName} Client Bisa mendapatkan data user {$appName}",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_user_group",
                "desc" => "{$appName} Client Bisa mendapatkan semua user group",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_save_user_group",
                "desc" => "{$appName} Client Bisa menyimpan user group {$appName} (tambah & hapus)",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_restore_user",
                "desc" => "{$appName} Client Bisa mengembalikan user {$appName} yang telah dihapus",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_update_user",
                "desc" => "{$appName} Client Bisa memperbaharui data user {$appName}",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_create_user",
                "desc" => "{$appName} Client Bisa menambah user {$appName}",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_delete_user",
                "desc" => "{$appName} Client Bisa menghapus user {$appName}",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_list_user_all_app",
                "desc" => "{$appName} Client Bisa mendapatkan semua user semua app",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_user_all_app",
                "desc" => "{$appName} Client Bisa mendapatkan data user semua app",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_get_user_all_app_group",
                "desc" => "{$appName} Client Bisa mendapatkan semua user group semua app",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
            [
                "nama" => "{$appName}_can_save_user_all_app_group",
                "desc" => "{$appName} Client Bisa menyimpan user group semua app (tambah & hapus)",
                "groups" => [
                    "{$appName}_superadmin",
                ]
            ],
        ];
        $group_model = model(GroupModel::class);
        $group_permission_model = model(GroupPermissionModel::class);
        foreach ($datas as $data) {
            if ($permission_model->save($data)) {
                $permission_id = $permission_model->getInsertID();
                foreach ($data["groups"] as $group) {
                    $group = $group_model->findGroupByName($group);
                    if ($group) {
                        $group_permission_model->save([
                            "group_id" => $group["id"],
                            "permission_id" => $permission_id,
                        ]);
                    }
                }
            }
        }
    }
}
