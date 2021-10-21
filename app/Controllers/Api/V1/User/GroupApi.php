<?php

namespace App\Controllers\Api\V1\User;

use App\Controllers\Api\BaseResourceApi;

class GroupApi extends BaseResourceApi
{

    public function index()
    {
        if($this->isAuthorizeClientPermission('can_get_groups')){
            return $this->respond(["status" => true, "message" => lang("User.successGetData"), "data" => $this->request->user->groups()], 200);
        }
    }
}
