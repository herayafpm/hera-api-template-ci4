<?php

namespace App\Filters;

use App\Libraries\ClaJWT;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use DomainException;
use Exception;

class ClientFilter implements FilterInterface
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
            if (!$request->hasHeader('applicationToken')) {
                throw new DomainException(lang("Filter.unAuthorized"), 401);
            }
            $jwt = $request->getHeader('applicationToken')->getValue();
            $decoded = ClaJWT::decode($jwt);
            if (property_exists($decoded, 'application_id')) {
                $client_model = model(ClientModel::class);
                $request->client_app = $client_model->where('application_id', $decoded->application_id)->first();
                if (!$request->client_app) {
                    throw new DomainException(lang("Filter.unAuthorized"), 401);
                }
                if ($request->client_app->hit_limit === 0) {
                    throw new DomainException(lang("Client.hitLimitExceeded"), 401);
                }
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
