<?php

namespace App\Controllers\Api;

use App\Filters\AfterRequestFilter;
use CodeIgniter\Config\Services;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Validation\Exceptions\ValidationException;
use Psr\Log\LoggerInterface;

class BaseResourceApi extends ResourceController
{
    protected $appName = 'default';
    protected $data;
    protected $validator;
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        $config = config("App");
        $language = \Config\Services::language();
        $locale = $request->getLocale();
        $language->setLocale($locale ?? $config->defaultLocale);
    }
    protected function validate($rules, array $messages = []): bool
    {
        $this->validator = Services::validation();
        // If you replace the $rules array with the name of the group
        if (is_string($rules)) {
            $validation = config('Validation');

            // If the rule wasn't found in the \Config\Validation, we
            // should throw an exception so the developer can find it.
            if (!isset($validation->$rules)) {
                throw ValidationException::forRuleNotFound($rules);
            }

            // If no error message is defined, use the error message in the Config\Validation file
            if (!$messages) {
                $errorName = $rules . '_errors';
                $messages  = $validation->$errorName ?? [];
            }

            $rules = $validation->$rules;
        }
        $data = $this->getDataRequest();
        return $this->validator->setRules($rules, $messages)->run((array)$data);
    }
    protected function getDataRequest($filtering = true)
    {
        $request = $this->request;
        /** @var IncomingRequest $request */
        if (strpos($request->getHeaderLine('Content-Type'), 'application/json') !== false) {
            $data = $request->getJSON(true);
        }else{
            if (
                in_array($request->getMethod(), ['put', 'patch', 'delete'], true)
                && strpos($request->getHeaderLine('Content-Type'), 'multipart/form-data') === false
            ) {
                $data = $request->getRawInput();
            } else {
                $data = $request->getVar() ?? [];
            }
        }
        $data = (array) array_merge((array)$data, $request->getFiles() ?? []);
        if($filtering){
            return $this->filteringData($data);
        }else{
            return $data;
        }
    }
    protected function filteringData($data)
    {
        foreach ($data as &$value) {
            if (is_string($value)) {
                $value = htmlspecialchars($value, true);
            }
        }
        unset($value);
        return $data;
    }

    protected function datatable($model, $params = [])
    {
        $limit = $_POST['length']; // Ambil data limit per page
        $start = $_POST['start']; // Ambil data start
        $order_index = $_POST['order'][0]['column']; // Untuk mengambil index yg menjadi acuan untuk sorting
        $orderBy = $_POST['columns'][$order_index]['data']; // Untuk mengambil nama field yg menjadi acuan untuk sorting
        $ordered = $_POST['order'][0]['dir']; // Untuk menentukan order by "ASC" atau "DESC"
        $sql_total = $model->count_all($params); // Panggil fungsi count_all pada Admin
        $sql_data = $model->filter($limit, $start, $orderBy, $ordered, $params); // Panggil fungsi filter pada Admin
        $sql_filter = $model->count_all($params); // Panggil fungsi count_filter pada Admin
        $callback = [
            'draw' => $_POST['draw'], // Ini dari datatablenya
            'recordsTotal' => $sql_total,
            'recordsFiltered' => $sql_filter,
            'data' => $sql_data
        ];
        return $callback;
    }

    public function unAuthorized($request, $response)
    {
        $data_res = [
            'status' => false,
            'message' => "",
            'data' => []
        ];
        $data_res['message'] = lang("Filter.unAuthorized");
        $data_res['data'] = [];
        $after_request_filter = new AfterRequestFilter();
        $response = $response->setStatusCode(401)->setJSON($data_res);
        $after_request_filter->after($request, $response, null)->send();
        die();
    }

    public function checkClientCanLogin()
    {
        return $this->request->client_app->canLogin();
    }

    public function isAuthorizeClientPermission($permission)
    {
        try {
            if($this->checkClientCanLogin()){
                if($this->request->user->hasPermission($permission)){
                    return true;
                }
                $this->respond(["status" => false, "message" => lang("Filter.unAuthorized"), "data" => []], 401)->send();
                return false;
            }
            if(!$this->request->client_app->hasPermission($permission)){
                $this->respond(["status" => false, "message" => lang("Filter.unAuthorized"), "data" => []], 401)->send();
                return false;
            }
            return true;
        } catch (\Exception $th) {
            $this->respond(["status" => false, "message" => lang("Exception.".$th->getMessage()), "data" => []], 500)->send();
            return false;
        }
    }

}
