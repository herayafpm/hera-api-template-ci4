<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseResourceApi;
use App\Entities\AdminEntity;
use App\Models\AdminModel;
use App\Models\GroupModel;
use App\Models\UserGroupModel;

class UsersApi extends BaseResourceApi
{

    protected $modelName = AdminModel::class;

    public function index()
    {
        if ($this->isAuthorizeClientPermission('can_get_list_user')) {
            $data = $this->getDataRequest();
            $limit = $data['limit'] ?? 10;
            $offset = $data['offset'] ?? 0;
            $user_where = [];
            $user_like = [];
            if (!empty($data['nama'])) {
                $user_like['nama'] = $data['nama'];
            }
            $orderBy = $data['order_by'] ?? "";
            $ordered = $data['ordered'] ?? "";
            $params = ['where' => $user_where, 'like' => $user_like];
            if (isset($data['deleted'])) {
                $params['withDeleted'] = (int) isset($data['deleted']);
            }
            $users = $this->model->filter($limit, $offset, $orderBy, $ordered, $params);
            $jumlahUsers = $this->model->count_all($params);
            $jumlahPage = round($jumlahUsers / $limit);
            return $this->respond(["status" => true, "message" => "users", "data" => [
                'jumlahUsers' => $jumlahUsers,
                'jumlahPage' => $jumlahPage,
                'users' => $users,
            ]], 200);
        }
    }
    public function show($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_get_user')) {
            $user = $this->model->withDeleted()->find($id);
            if ($user) {
                return $this->respond(["status" => true, "message" => "user", "data" => $user], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function group($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_get_user_group')) {
            $user = $this->model->withDeleted()->find($id);
            if ($user) {
                $group_model = model(GroupModel::class);
                $groups = $group_model->select("nama,desc")->findAll();
                $user_group_model = model(UserGroupModel::class);
                $user_groups = $user_group_model->join("hera_group", "hera_user_group.group_id = hera_group.id", 'LEFT')->where('username', $user->username)->findColumn('nama') ?? [];
                return $this->respond(["status" => true, "message" => "user group", "data" => compact('groups', 'user_groups')], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function groupSave($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_save_user_group')) {
            $user = $this->model->withDeleted()->find($id);
            if ($user) {
                $data = $this->getDataRequest();
                $group_model = model(GroupModel::class);
                $user_group_model = model(UserGroupModel::class);
                foreach ($data['add'] as $nama) {
                    $group = $group_model->where('nama', $nama)->first();
                    if ($group) {
                        $user_group = $user_group_model->where(['username' => $user->username, 'group_id' => $group['id']])->withDeleted()->first();
                        if($user_group > 0){
                            $user_group_model->update($user_group['id'],['deleted_at' => NULL]);
                        }else{
                            $user_group_model->save(['username' => $user->username, 'group_id' => $group['id']]);
                        }
                    }
                }
                foreach ($data['delete'] as $nama) {
                    $group = $group_model->where('nama', $nama)->first();
                    if ($group) {
                        $user_group_model->where(['username' => $user->username, 'group_id' => $group['id']])->delete();
                    }
                }
                return $this->respond(["status" => true, "message" => "Berhasil memperbarui user group", "data" => []], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }

    public function restore($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_restore_user')) {
            $user = $this->model->withDeleted()->find($id);
            if ($user) {
                if ($this->model->withDeleted()->update($id, ['deleted_at' => null])) {
                    return $this->respond(["status" => true, "message" => "Berhasil mengembalikan user", "data" => []], 200);
                } else {
                    return $this->respond(["status" => false, "message" => "Gagal mengembalikan user", "data" => []], 400);
                }
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }

    public function update($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_update_user')) {
            $user = $this->model->where(['username !=' => 'superadmin'])->find($id);
            if ($user) {
                $rules = [
                    'nama' => [
                        'label'  => "Nama",
                        'rules'  => "required|is_unique_db[hera,hera_admin.nama,id,{$id}]",
                        'errors' => []
                    ],
                ];
                $data = $this->getDataRequest();

                if (!$this->validate($rules)) {
                    return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
                }
                $user->nama = $data['nama'];
                if ($this->model->update($id, $user)) {
                    return $this->respond(["status" => true, "message" => "Berhasil mengupdate user", "data" => []], 200);
                } else {
                    return $this->respond(["status" => false, "message" => "Gagal mengupdate user", "data" => []], 400);
                }
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function create($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_create_user')) {
            $rules = [
                'username' => [
                    'label'  => "Username",
                    'rules'  => 'required|is_unique_db[hera,hera_admin.username]',
                    'errors' => []
                ],
                'nama' => [
                    'label'  => "Nama",
                    'rules'  => 'required',
                    'errors' => []
                ],
                'password' => [
                    'label'  => "Password",
                    'rules'  => "required|min_length[6]",
                    'errors' => []
                ],
            ];
            if (!$this->validate($rules)) {
                return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
            }
            $data = $this->getDataRequest();
            $data_insert = [
                'nama' => $data['nama'],
                'username' => $data['username'],
                'password' => $data['password'],
            ];
            $entity = new AdminEntity($data_insert);
            if ($this->model->save($entity)) {
                return $this->respond(["status" => true, "message" => "Berhasil menambah user", "data" => []], 200);
            } else {
                return $this->respond(["status" => false, "message" => "Gagal menambah user", "data" => []], 400);
            }
        }
    }

    public function delete($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_delete_user')) {
            $user = $this->model->where(['username !=' => 'superadmin'])->find($id);
            if ($user) {
                if ($this->model->delete($id)) {
                    return $this->respond(["status" => true, "message" => "Berhasil menghapus user", "data" => []], 200);
                } else {
                    return $this->respond(["status" => false, "message" => "Gagal menghapus user", "data" => []], 400);
                }
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
}
