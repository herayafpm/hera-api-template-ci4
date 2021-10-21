<?php

namespace App\Controllers\Api\V1\User;

use App\Controllers\Api\BaseResourceApi;

class UserApi extends BaseResourceApi
{
    public function index()
    {
        if($this->isAuthorizeClientPermission('can_get_profil')){
            return $this->respond(["status" => true, "message" => lang("User.successGetData"), "data" => ['username' => $this->request->user->username,'nama' => $this->request->user->nama]], 200);
        }
    }

    public function update_profil()
    {
        if ($this->isAuthorizeClientPermission('can_update_profil')) {
            $id = $this->request->user->id;
            $rules = [
                'nama' => [
                    'label'  => "Nama",
                    'rules'  => "required",
                    'errors' => []
                ],
                'password' => [
                    'label'  => "Password",
                    'rules'  => "sometime_len[6]",
                    'errors' => []
                ],
            ];
            if (!$this->validate($rules)) {
                return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
            }
            $data = $this->getDataRequest();
            $this->request->user->nama = $data['nama'];
            if(!empty($data['password'])){
                $this->request->user->password = $data['password'];
            }
            if ($this->request->client_app->userModelClass()->update($id, $this->request->user)) {
                return $this->respond(["status" => true, "message" => "Berhasil mengupdate profil", "data" => []], 200);
            } else {
                return $this->respond(["status" => false, "message" => "Gagal mengupdate profil", "data" => []], 400);
            }
            // try {
            // } catch (\Exception $th) {
            //     return $this->fail(["status" => false, "message" => lang("Exception.".$th->getMessage()), "data" => []], 500);
            // }
            return $this->respond(["status" => false, "message" => "Not Found", "data" => []], 404);
        }
    }
}
