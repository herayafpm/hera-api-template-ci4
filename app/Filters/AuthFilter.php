<?php

namespace App\Filters;

use App\Libraries\ClaJWT;
use App\Models\UserDeviceModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use DomainException;
use Exception;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service("response");
        $data_res = [
            'status' => false,
            'message' => "",
            'data' => []
        ];
        try {
            if ($request->client_app->canLogin()) {
                if (!$request->hasHeader('X-API-KEY')) {
                    throw new DomainException(lang("Filter.unAuthorized"), 401);
                }
                $jwt = explode("Bearer ", $request->getHeader('X-API-KEY')->getValue())[1];
                $decoded = ClaJWT::decode($jwt);
                $model = $request->client_app->userModelClass($decoded->jenis_user ?? '');
                $model_user_check = $model->cekUser($decoded->username);
                if($model_user_check){
                    $request->user = $model_user_check;
                }else{
                    throw new DomainException(lang("Filter.unAuthorized"), 401);
                }
                if (!$request->hasHeader('deviceId')) {
                    throw new DomainException(lang("Device.required"), 401);
                }
                $device_model = model(UserDeviceModel::class);
                $request->device = $device_model->where(['device_uuid' => $request->getHeader('deviceId')->getValue(), 'username' => $request->user->username, 'client_id' => $request->client_app->id])->like('user_agent',$request->getUserAgent()->getAgentString())->first();
                if (empty($request->device)) {
                    unset($request->device);
                    throw new \UnexpectedValueException(lang("Device.notFound"), 401);
                }
                $request->user->appName = str_replace('-','_',$request->uri->getSegments()[3]);
            }
        } catch (\UnexpectedValueException $th) {
            $data_res['message'] = $th->getMessage();
            $data_res['data'] = ['login_action' => true];
            $after_request_filter = new AfterRequestFilter();
            $response = $response->setStatusCode(401)->setJSON($data_res);
            return $after_request_filter->after($request, $response, $arguments);
        } catch (\DomainException $th) {
            $data_res['message'] = $th->getMessage();
            $after_request_filter = new AfterRequestFilter();
            $response = $response->setStatusCode(401)->setJSON($data_res);
            return $after_request_filter->after($request, $response, $arguments);
        } catch (Exception $th) {
            $data_res['message'] = $th->getMessage();
            $after_request_filter = new AfterRequestFilter();
            $response = $response->setStatusCode(500)->setJSON($data_res);
            return $after_request_filter->after($request, $response, $arguments);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
