<?php

namespace App\Models;

use App\Entities\DeviceEntity;
use Ramsey\Uuid\Uuid;

class UserDeviceModel extends BaseModel
{
    protected $table                = 'hera_user_device';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = DeviceEntity::class;
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['user_agent', 'client_id','gfcm', 'device_uuid', 'username', 'last_request','deleted_at'];

    // Dates
    protected $useTimestamps        = true;
    protected $dateFormat           = 'datetime';
    protected $createdField         = 'created_at';
    protected $updatedField         = 'updated_at';
    protected $deletedField         = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks       = true;
    protected $beforeInsert         = [];
    protected $afterInsert          = [];
    protected $beforeUpdate         = [];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    public function saveOrUpdateDevice($userAgent, $client_id, $username, $uuid)
    {
        if(!$uuid){
            $uuid = Uuid::uuid4();
        }
        $device = $this->where(['client_id' => $client_id, 'username' => $username, 'device_uuid' => $uuid])->like('user_agent',$userAgent)->first();
        if ($device) {
            $this->update($device->id, ['last_request' => date("Y-m-d H:i:s")]);
        } else {
            $this->save([
                'user_agent' => $userAgent,
                'client_id' => $client_id,
                'device_uuid' => $uuid,
                'username' => $username,
                'last_request' => date("Y-m-d H:i:s"),
            ]);
        }
        return $uuid;
    }
}
