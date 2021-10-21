<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseResourceApi;
use App\Models\PermissionModel;

class PermissionApi extends BaseResourceApi
{

    protected $modelName = PermissionModel::class;

    public function index()
    {
        $data = $this->getDataRequest();
        $limit = $data['limit'] ?? 10;
        $offset = $data['offset'] ?? 0;
        $permission_where = [];
        $permission_like = [];
        if(!empty($data['nama'])){
            $permission_like['nama'] = $data['nama'];
        }
        $orderBy = $data['order_by'] ?? "";
        $ordered = $data['ordered'] ?? "";
        $params = ['where' => $permission_where,'like' => $permission_like];
        if(isset($data['deleted'])){
            $params['withDeleted'] = (int) isset($data['deleted']);
        }
        $permissions = $this->model->filter($limit,$offset,$orderBy,$ordered,$params);
        $jumlahPermissions = $this->model->count_all($params);
        $jumlahPage = round($jumlahPermissions/$limit);
        return $this->respond(["status" => true, "message" => "permissions", "data" => [
            'jumlahPermissions'=>$jumlahPermissions,
            'jumlahPage'=>$jumlahPage,
            'permissions'=>$permissions,
        ]], 200);
    }

    public function update($id = null)
    {
        $permission = $this->model->where(['id' => $id])->first();
        if($permission){
            $rules = [
                'nama' => [
                    'label'  => "Nama",
                    'rules'  => 'required',
                    'errors' => []
                ],
                'desc' => [
                    'label'  => "Deskripsi",
                    'rules'  => 'required',
                    'errors' => []
                ],
            ];
            if (!$this->validate($rules)) {
                return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
            }
            $data = $this->getDataRequest();
            if($this->model->update($id,$data)){
                return $this->respond(["status" => true, "message" => "Berhasil mengupdate permission", "data" => []], 200);
            }else{
                return $this->respond(["status" => false, "message" => "Gagal mengupdate permission", "data" => []], 400);
            }
        }
        return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
    }
    public function create()
    {
        $rules = [
            'nama' => [
                'label'  => "Nama",
                'rules'  => 'required',
                'errors' => []
            ],
            'desc' => [
                'label'  => "Deskripsi",
                'rules'  => 'required',
                'errors' => []
            ],
        ];
        if (!$this->validate($rules)) {
            return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
        }
        $data = $this->getDataRequest();
        $data['nama'] = strtolower(str_replace(" ","_",$data['nama']));
        if($this->model->save($data)){
            return $this->respond(["status" => true, "message" => "Berhasil menambah permsission", "data" => []], 200);
        }else{
            return $this->respond(["status" => false, "message" => "Gagal menambah permsission", "data" => []], 400);
        }
    }

    public function restore($id = null)
    {
        $permission = $this->model->where(['id' => $id])->withDeleted()->first();
        if($permission){
            if($this->model->withDeleted()->update($id,['deleted_at' => null])){
                return $this->respond(["status" => true, "message" => "Berhasil mengembalikan permission", "data" => []], 200);
            }else{
                return $this->respond(["status" => false, "message" => "Gagal mengembalikan permission", "data" => []], 400);
            }
        }
        return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
    }

    public function delete($id = null)
    {
        $permission = $this->model->where(['id' => $id])->first();
        if($permission){
            if($this->model->delete($id)){
                return $this->respond(["status" => true, "message" => "Berhasil menghapus permission", "data" => []], 200);
            }else{
                return $this->respond(["status" => false, "message" => "Gagal menghapus permission", "data" => []], 400);
            }
        }
        return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
    }

}
