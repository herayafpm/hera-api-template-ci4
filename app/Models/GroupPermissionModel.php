<?php

namespace App\Models;

class GroupPermissionModel extends BaseModel
{
    protected $table                = 'hera_group_permission';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = 'array';
    protected $useSoftDeletes       = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['group_id', 'permission_id','deleted_at'];

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

    public function doesUserHavePermission(string $username, int $permissionId): bool
    {
        // Check group permissions
        $count = $this->join("hera_user_group", "{$this->table}.group_id = hera_user_group.group_id", "LEFT")->where(['username' => $username, 'permission_id' => $permissionId])->countAllResults();

        return $count > 0;
    }
}
