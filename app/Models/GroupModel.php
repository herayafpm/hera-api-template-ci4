<?php

namespace App\Models;


class GroupModel extends BaseModel
{
	protected $table                = 'hera_group';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDeletes       = true;
	protected $protectFields        = true;
	protected $allowedFields        = ['nama','desc','deleted_at'];

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

	public function findGroupByName($nama)
	{
		return $this->where(['nama' => $nama])->first();
	}

	public function filter($limit, $start, $order, $ordered, $params = [])
	{
		$builder = $this;
		$order = $this->filterData($order);
		$builder->orderBy($order, $ordered);
		
        if(isset($params['select'])){
            $builder->select($params['select']);
        }else{
            $builder->select("{$this->table}.*");
        }

		if (isset($params['where'])) {
			$where = $params['where'];
			foreach ($where as $key => $value) {
				$pos = strpos($key, '.');
				if($pos === false){
					unset($where[$key]);
					$where["{$this->table}.{$key}"] = $value;
				}
			}
			$builder->where($where);
		}
		if (isset($params['like'])) {
			foreach ($params['like'] as $key => $value) {
				$pos = strpos($key, '.');
				if($pos === false){
					$key = "{$this->table}.{$key}";
				}
				$builder->like($key, $value);
			}
		}
		if (isset($params['orLike'])) {
			foreach ($params['orLike'] as $key => $value) {
				$pos = strpos($key, '.');
				if($pos === false){
					$key = "{$this->table}.{$key}";
				}
				$builder->orLike($key, $value);
			}
		}
        if(isset($params['withDeleted'])){
            $builder->withDeleted();
        }
        if ($limit > 0) {
			return $builder->findAll($limit, $start); // Untuk menambahkan query LIMIT
		}else{
            return $builder->findAll();
        }
	}
    public function count_all($params = [])
	{
		$builder = $this;
		
        if(isset($params['select'])){
            $builder->select($params['select']);
        }else{
            $builder->select("{$this->table}.*");
        }

		if (isset($params['where'])) {
			$where = $params['where'];
			foreach ($where as $key => $value) {
				$pos = strpos($key, '.');
				if($pos === false){
					unset($where[$key]);
					$where["{$this->table}.{$key}"] = $value;
				}
			}
			$builder->where($where);
		}
		if (isset($params['like'])) {
			foreach ($params['like'] as $key => $value) {
				$pos = strpos($key, '.');
				if($pos === false){
					$key = "{$this->table}.{$key}";
				}
				$builder->like($key, $value);
			}
		}
		if (isset($params['orLike'])) {
			foreach ($params['orLike'] as $key => $value) {
				$pos = strpos($key, '.');
				if($pos === false){
					$key = "{$this->table}.{$key}";
				}
				$builder->orLike($key, $value);
			}
		}
        if(isset($params['withDeleted'])){
            $builder->withDeleted();
        }
        return $builder->countAllResults();
	}

    public function filterData($key)
	{
		// switch ($key) {
		// 	case 'program_studi':
		// 		$key = 'd.NAMA_DEPT';
		// 		break;
		// 	case 'nama':
		// 		$key = 'm.NAMA';
		// 		break;
		// }
		$key = $this->alias_field[$key] ?? $key;
		$pos = strpos($key, '.');
		if($pos === false){
			$key = "{$this->table}.{$key}";
		}	
		return $key;
	}
}
