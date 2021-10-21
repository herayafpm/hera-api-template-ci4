<?php

namespace App\Entities;
use App\Models\AdminModel;
use App\Models\ClientPermissionModel;
use CodeIgniter\Entity\Entity;
use CodeIgniter\I18n\Time;
use Student\Models\PenggunaModel;

class ClientEntity extends Entity
{
    protected $permission_model;
    protected $client_permission_model;
    public function __construct(array $data = null)
    {
        parent::__construct($data);
        $this->permission_model = model(PermissionModel::class);
        $this->client_permission_model = model(ClientPermissionModel::class);
    }
    protected $datamap = [];
    protected $dates   = [
        'access_token_expired',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
    protected $casts   = [];
    public function canLogin()
    {
        return $this->hasPermission("can_login");
    }

    public function userModelClass($jenis_user = '')
    {
        $model = null;
        $appName = $this->attributes['nick_name'];
        $name = $appName;
        switch ($this->attributes['nick_name']) {
            case 'student':
                $model = PenggunaModel::class;
                break;
            default:
                $name = 'default';
                $model = AdminModel::class;
                break;
        }
        return model($model)->setDBGroup($name);
    }


    public function hasPermission($permission)
    {

        // @phpstan-ignore-next-line
        if (empty($permission) || (!is_string($permission) && !is_numeric($permission))) {
            return null;
        }
        $permission = "{$this->attributes['nick_name']}_".$permission;
        $client_id = $this->attributes['id'];

        if (empty($client_id)) {
            return null;
        }

        // Get the Permission ID
        $permissionId = $this->getPermissionID($permission);

        if (!is_numeric($permissionId)) {
            return false;
        }
        // First check the permission model. If that exists, then we're golden.
        if ($this->client_permission_model->doesClientHavePermission($client_id, (int)$permissionId)) {
            return true;
        }

        // Still here? Then we have one last check to make - any user private permissions.
        return $this->doesClientHavePermission($client_id, (int)$permissionId);
    }

    public function doesClientHavePermission($client_id, $permission)
    {
        $permissionId = $this->getPermissionID($permission);

        if (!is_numeric($permissionId)) {
            return false;
        }

        if (empty($client_id)) {
            return null;
        }

        return $this->client_permission_model->doesClientHavePermission($client_id, $permissionId);
    }

    protected function getPermissionID($permission)
    {
        // If it's a number, we're done here.
        if (is_numeric($permission)) {
            return (int) $permission;
        }

        // Otherwise, pull it from the database.
        $p = $this->permission_model->asObject()->where('nama', $permission)->first();

        if (!$p) {
            $this->error = lang('Auth.permissionNotFound', [$permission]);

            return false;
        }

        return (int) $p->id;
    }

    public function setCreatedAt(string $dateString)
    {
        $this->attributes['created_at'] = new Time($dateString, 'UTC');

        return $this;
    }

    public function getCreatedAt(string $format = 'Y-m-d H:i:s')
    {
        // Convert to CodeIgniter\I18n\Time object
        $this->attributes['created_at'] = $this->mutateDate($this->attributes['created_at']);

        $timezone = $this->timezone ?? app_timezone();

        $this->attributes['created_at']->setTimezone($timezone);

        return $this->attributes['created_at']->format($format);
    }

}
