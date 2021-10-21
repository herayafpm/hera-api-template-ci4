<?php

namespace App\Controllers\Api\V1\Auth;

use App\Controllers\Api\BaseResourceApi;
use App\Entities\AdminEntity;
use App\Libraries\ClaJWT;
use App\Models\AdminModel;
use App\Models\UserDeviceModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use DomainException;
use Psr\Log\LoggerInterface;

class LoginApi extends BaseResourceApi
{

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        if (!$this->checkClientCanLogin()) {
            $this->unAuthorized($request, $response)->send();
        }
    }

    protected function rules_login($key = null)
    {
        $rules = [
            'username' => [
                'label'  => lang("Auth.labelUsername"),
                'rules'  => 'required',
                'errors' => []
            ],
            'password' => [
                'label'  => lang("Auth.labelPassword"),
                'rules'  => 'required',
                'errors' => []
            ],
        ];
        if ($key) {
            if (!key_exists($key, $rules)) {
                throw new DomainException(lang("Validation.notFound"), 400);
            } else {
                return [
                    $key => $rules[$key]
                ];
            }
        } else {
            return $rules;
        }
    }

    public function validation()
    {
        $data = $this->getDataRequest();
        try {
            $rules = $this->rules_login($data['key']);
        } catch (\DomainException $th) {
            return $this->respond(["status" => false, "message" => $th->getMessage(), "data" => []], $th->getCode());
        } catch (\Exception $th) {
            return $this->respond(["status" => false, "message" => $th->getMessage(), "data" => []], 500);
        }
        if (!$this->validate($rules)) {
            return $this->respond(["status" => false, "message" => $this->validator->getError($data['key']), "data" => []], 400);
        }
        return $this->respond(["status" => true, "message" => "", "data" => []], 200);
    }


    public function login()
    {

        try {
            $rules = $this->rules_login();
        } catch (\DomainException $th) {
            return $this->respond(["status" => false, "message" => $th->getMessage(), "data" => []], $th->getCode());
        }
        if (!$this->validate($rules)) {
            return $this->respond(["status" => false, "message" => lang("Validation.errorValidation"), "data" => $this->validator->getErrors()], 400);
        }
        $data = $this->getDataRequest();
        $admin_entity = new AdminEntity($data);
        $admin_model = model(AdminModel::class);
        $login_success = $admin_model->attempt($admin_entity);
        $username = $admin_entity->username;
        $message = $admin_model->getMessage();
        if ($login_success) {
            $device_model = model(UserDeviceModel::class);
            $client_id = $this->request->client_app->id;
            $device_uuid = $device_model->saveOrUpdateDevice($this->request->getUserAgent()->getAgentString(), $client_id, $username, $data['device_uuid'] ?? null);
            $jwt = ClaJWT::encode(['username' => $username], null, false, false);
            $jwt = array_merge($jwt, ['device_uuid' => $device_uuid]);
            return $this->respond(["status" => true, "message" => $message, "data" => $jwt], 200);
        } else {
            return $this->respond(["status" => false, "message" => $message, "data" => []], 400);
        }
    }
}
