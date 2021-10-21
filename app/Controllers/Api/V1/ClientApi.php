<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseResourceApi;
use App\Models\ClientModel;
use App\Models\ClientPermissionModel;
use App\Models\PermissionModel;

class ClientApi extends BaseResourceApi
{

    protected $modelName = ClientModel::class;

    public function index()
    {
        if ($this->isAuthorizeClientPermission('can_get_list_client')) {
            $data = $this->getDataRequest();
            $limit = $data['limit'] ?? 10;
            $offset = $data['offset'] ?? 0;
            $client_where = [];
            $client_like = [];
            if (!empty($data['nama'])) {
                $client_like['nama'] = $data['nama'];
            }
            $orderBy = $data['order_by'] ?? "";
            $ordered = $data['ordered'] ?? "";
            $params = ['where' => $client_where, 'like' => $client_like];
            if (isset($data['deleted'])) {
                $params['withDeleted'] = (int) isset($data['deleted']);
            }
            $clients = $this->model->filter($limit, $offset, $orderBy, $ordered, $params);
            $jumlahClients = $this->model->count_all($params);
            $jumlahPage = round($jumlahClients / $limit);
            return $this->respond(["status" => true, "message" => "Clients", "data" => [
                'jumlahClients' => $jumlahClients,
                'jumlahPage' => $jumlahPage,
                'clients' => $clients,
            ]], 200);
        }
    }
    public function show($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_get_client')) {
            $client = $this->model->withDeleted()->find($id);
            if ($client) {
                return $this->respond(["status" => true, "message" => "Clients", "data" => $client], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function permission($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_get_client_permission')) {
            $client = $this->model->withDeleted()->find($id);
            if ($client) {
                $permission_model = model(PermissionModel::class);
                $permissions = $permission_model->select("nama,desc")->findAll();
                $client_permission_model = model(ClientPermissionModel::class);
                $client_permissions = $client_permission_model->join("hera_permission", "hera_client_permission.permission_id = hera_permission.id")->where('client_id', $id)->findColumn('nama');
                return $this->respond(["status" => true, "message" => "Client Permission", "data" => compact('permissions', 'client_permissions')], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function permissionSave($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_save_client_permission')) {
            $client = $this->model->withDeleted()->find($id);
            if ($client) {
                $data = $this->getDataRequest();
                $permission_model = model(PermissionModel::class);
                $client_permission_model = model(ClientPermissionModel::class);
                foreach ($data['add'] as $nama) {
                    $permission = $permission_model->where('nama', $nama)->first();
                    if ($permission) {
                        $client_permission_model->save(['client_id' => $id, 'permission_id' => $permission['id']]);
                    }
                }
                foreach ($data['delete'] as $nama) {
                    $permission = $permission_model->where('nama', $nama)->first();
                    if ($permission) {
                        $client_permission_model->where(['client_id' => $id, 'permission_id' => $permission['id']])->delete();
                    }
                }
                return $this->respond(["status" => true, "message" => "Berhasil memperbarui Client Permission", "data" => []], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }

    public function restore($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_restore_client')) {
            $client = $this->model->where(['id != ' => $this->request->client_app->id, 'id' => $id])->withDeleted()->first();
            if ($client) {
                if ($this->model->withDeleted()->update($id, ['deleted_at' => null])) {
                    return $this->respond(["status" => true, "message" => "Berhasil mengembalikan client", "data" => []], 200);
                } else {
                    return $this->respond(["status" => false, "message" => "Gagal mengembalikan client", "data" => []], 400);
                }
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }

    public function update($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_update_client')) {
            $client = $this->model->where(['id != ' => $this->request->client_app->id, 'id' => $id])->first();
            if ($client) {
                $rules = [
                    'nama' => [
                        'label'  => "Nama",
                        'rules'  => 'required',
                        'errors' => []
                    ],
                ];
                if (!$this->validate($rules)) {
                    return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
                }
                $data = $this->getDataRequest();
                if ($this->model->updateWithToken($id, $data, $client)) {
                    return $this->respond(["status" => true, "message" => "Berhasil mengupdate client", "data" => []], 200);
                } else {
                    return $this->respond(["status" => false, "message" => "Gagal mengupdate client", "data" => []], 400);
                }
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function create($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_create_client')) {
            $rules = [
                'nama' => [
                    'label'  => "Nama",
                    'rules'  => 'required',
                    'errors' => []
                ],
            ];
            if (!$this->validate($rules)) {
                return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
            }
            $data = $this->getDataRequest();
            if ($this->model->save($data)) {
                return $this->respond(["status" => true, "message" => "Berhasil menambah client", "data" => []], 200);
            } else {
                return $this->respond(["status" => false, "message" => "Gagal menambah client", "data" => []], 400);
            }
        }
    }

    public function delete($id = null)
    {
        if ($this->isAuthorizeClientPermission('can_delete_client')) {
            $client = $this->model->where(['id != ' => $this->request->client_app->id, 'id' => $id])->first();
            if ($client) {
                if ($this->model->delete($id)) {
                    return $this->respond(["status" => true, "message" => "Berhasil menghapus client", "data" => []], 200);
                } else {
                    return $this->respond(["status" => false, "message" => "Gagal menghapus client", "data" => []], 400);
                }
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
}
