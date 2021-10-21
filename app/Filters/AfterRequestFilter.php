<?php

namespace App\Filters;

use App\Models\RequestLogModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AfterRequestFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $request_log_model = model(RequestLogModel::class);
        $ipAddress = $request->getIPAddress();
        $userAgent = $request->getUserAgent();
        $path = $request->uri->getPath();
        $method = $request->getMethod();
        $username = null;
        $nama = null;
        $device_id = null;
        $client_id = 0;
        $client_nama = null;
        if ($response->getStatusCode() != 500 && property_exists($request, 'client_app')) {
            $body = json_decode($response->getBody());
            $message = $body->message ?? "";
            if (property_exists($request, 'user')) {
                $username = $request->user->username;
                $nama = $request->user->nama;
                if (property_exists($request, 'device')) {
                    $device_id = $request->device->id;
                }
            }
            if (property_exists($request, 'client_app')) {
                if ($request->client_app) {
                    $client_id = $request->client_app->id;
                    $client_nama = $request->client_app->nama;
                }
            }
            $request_log_model->save([
                'nama'            => $nama,
                'username'            => $username,
                'client_id'            => $client_id,
                'device_id'            => $device_id,
                'client_nama'            => $client_nama,
                'path'            => $path,
                'method'            => $method,
                'ip'            => $ipAddress,
                'user_agent'            => $userAgent,
                'status_code'            => $response->getStatusCode(),
                'status_message'            => $message,
            ]);
        }
        return $response;
    }
}
