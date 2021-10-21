<?php

namespace App\Controllers\Api\V1;

use App\Controllers\Api\BaseResourceApi;
use App\Models\GroupModel;
use App\Models\UserGroupModel;

class UsersAllAppApi extends BaseResourceApi
{

    protected $modelName = UserGroupModel::class;

    public function index()
    {
        if ($this->isAuthorizeClientPermission('can_get_list_user_all_app')) {
            $data = $this->getDataRequest();
            $limit = $data['limit'] ?? 10;
            $offset = $data['offset'] ?? 0;
            $user_where = [];
            $user_like = [];
            if (!empty($data['username'])) {
                $user_like['username'] = $data['username'];
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
    public function show($username = null)
    {
        if ($this->isAuthorizeClientPermission('can_get_user_all_app')) {
            $user = $this->model->where(['username'=>$username])->first();
            if ($user) {
                return $this->respond(["status" => true, "message" => "user", "data" => $user], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function group($username = null)
    {
        if ($this->isAuthorizeClientPermission('can_get_user_all_app_group')) {
            $user = $this->model->withDeleted()->where(['username'=>$username])->first();
            if ($user) {
                $group_model = model(GroupModel::class);
                $groups = $group_model->select("nama,desc")->findAll();
                $user_groups = $this->model->join("hera_group", "hera_user_group.group_id = hera_group.id", 'LEFT')->where('username', $username)->findColumn('nama') ?? [];
                return $this->respond(["status" => true, "message" => "user group", "data" => compact('groups', 'user_groups')], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
    public function groupSave($username = null)
    {
        if ($this->isAuthorizeClientPermission('can_save_user_all_app_group')) {
            $user = $this->model->withDeleted()->where(['username'=>$username])->first();
            if ($user) {
                $data = $this->getDataRequest();
                $group_model = model(GroupModel::class);
                foreach ($data['add'] as $nama) {
                    $group = $group_model->where('nama', $nama)->first();
                    if ($group) {
                        $user_group = $this->model->where(['username' => $username, 'group_id' => $group['id']])->withDeleted()->first();
                        if($user_group > 0){
                            $this->model->update($user_group['id'],['deleted_at' => NULL]);
                        }else{
                            $this->model->save(['username' => $username, 'group_id' => $group['id']]);
                        }
                    }
                }
                foreach ($data['delete'] as $nama) {
                    $group = $group_model->where('nama', $nama)->first();
                    if ($group) {
                        $this->model->where(['username' => $username, 'group_id' => $group['id']])->delete();
                    }
                }
                return $this->respond(["status" => true, "message" => "Berhasil memperbarui user group", "data" => []], 200);
            }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
}
