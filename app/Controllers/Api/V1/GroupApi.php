<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseResourceApi;
use App\Models\GroupModel;
use App\Models\GroupPermissionModel;
use App\Models\PermissionModel;

class GroupApi extends BaseResourceApi
{

    protected $modelName = GroupModel::class;

    public function index()
    {
        if ($this->isAuthorizeClientPermission('can_get_list_group')) {
            $data = $this->getDataRequest();
            $limit = $data['limit'] ?? 10;
            $offset = $data['offset'] ?? 0;
            $group_where = [];
            $group_like = [];
            if (!empty($data['nama'])) {
                $group_like['nama'] = $data['nama'];
            }
            $orderBy = $data['order_by'] ?? "";
            $ordered = $data['ordered'] ?? "";
            $params = ['where' => $group_where, 'like' => $group_like];
            if (isset($data['deleted'])) {
                $params['withDeleted'] = (int) isset($data['deleted']);
            }
            $groups = $this->model->filter($limit, $offset, $orderBy, $ordered, $params);
            $jumlahGroups = $this->model->count_all($params);
            $jumlahPage = round($jumlahGroups / $limit);
            return $this->respond(["status" => true, "message" => "groups", "data" => [
                'jumlahGroups' => $jumlahGroups,
                'jumlahPage' => $jumlahPage,
                'groups' => $groups,
            ]], 200);
        }
    }
    public function show($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_get_group')) {
            $group = $this->model->withDeleted()->find($id);
            if ($group) {
                return $this->respond(["status" => true, "message" => "group", "data" => $group], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function permission($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_get_group_permission')) {
            $group = $this->model->withDeleted()->find($id);
            if ($group) {
                $permission_model = model(PermissionModel::class);
                $permissions = $permission_model->select("nama,desc")->findAll();
                $group_permission_model = model(GroupPermissionModel::class);
                $group_permissions = $group_permission_model->join("hera_permission", "hera_group_permission.permission_id = hera_permission.id", 'LEFT')->where('group_id', $id)->findColumn('nama') ?? [];
                return $this->respond(["status" => true, "message" => "group Permission", "data" => compact('permissions', 'group_permissions')], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function permissionSave($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_save_group_permission')) {
            $group = $this->model->withDeleted()->find($id);
            if ($group) {
                $data = $this->getDataRequest();
                $permission_model = model(PermissionModel::class);
                $group_permission_model = model(GroupPermissionModel::class);
                foreach ($data['add'] as $nama) {
                    $permission = $permission_model->where('nama', $nama)->first();
                    if ($permission) {
                        $group_permission = $group_permission_model->where(['group_id' => $id, 'permission_id' => $permission['id']])->withDeleted()->first();
                        if($group_permission > 0){
                            $group_permission_model->update($group_permission['id'],['deleted_at' => NULL]);
                        }else{
                            $group_permission_model->save(['group_id' => $id, 'permission_id' => $permission['id']]);
                        }
                    }
                }
                foreach ($data['delete'] as $nama) {
                    $permission = $permission_model->where('nama', $nama)->first();
                    if ($permission) {
                        $group_permission_model->where(['group_id' => $id, 'permission_id' => $permission['id']])->delete();
                    }
                }
                return $this->respond(["status" => true, "message" => "Berhasil memperbarui group Permission", "data" => []], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }

    public function restore($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_restore_group')) {
            $group = $this->model->withDeleted()->find($id);
            if ($group) {
                if ($this->model->withDeleted()->update($id, ['deleted_at' => null])) {
                    return $this->respond(["status" => true, "message" => "Berhasil mengembalikan group", "data" => []], 200);
                } else {
                    return $this->respond(["status" => false, "message" => "Gagal mengembalikan group", "data" => []], 400);
                }
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }

    public function update($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_update_group')) {
            $group = $this->model->where(['nama !=' => 'hera_superadmin'])->find($id);
            if ($group) {
                $rules = [
                    'nama' => [
                        'label'  => "Nama",
                        'rules'  => "required|is_unique_db[hera,hera_group.nama,id,{$id}]",
                        'errors' => []
                    ],
                ];
                $data = $this->getDataRequest();

                if (!$this->validate($rules)) {
                    return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
                }
                if ($this->model->update($id, $data)) {
                    return $this->respond(["status" => true, "message" => "Berhasil mengupdate group", "data" => []], 200);
                } else {
                    return $this->respond(["status" => false, "message" => "Gagal mengupdate group", "data" => []], 400);
                }
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function create($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_create_group')) {
            $rules = [
                'nama' => [
                    'label'  => "Nama",
                    'rules'  => 'required|is_unique_db[hera,hera_group.nama]',
                    'errors' => []
                ],
            ];
            if (!$this->validate($rules)) {
                return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
            }
            $data = $this->getDataRequest();
            if ($this->model->save($data)) {
                return $this->respond(["status" => true, "message" => "Berhasil menambah group", "data" => []], 200);
            } else {
                return $this->respond(["status" => false, "message" => "Gagal menambah group", "data" => []], 400);
            }
        }
    }

    public function delete($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_delete_group')) {
            $group = $this->model->where(['nama !=' => 'hera_superadmin'])->find($id);
            if ($group) {
                if ($this->model->delete($id)) {
                    return $this->respond(["status" => true, "message" => "Berhasil menghapus group", "data" => []], 200);
                } else {
                    return $this->respond(["status" => false, "message" => "Gagal menghapus group", "data" => []], 400);
                }
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
}
