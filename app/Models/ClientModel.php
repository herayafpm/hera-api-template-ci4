<?php

namespace App\Models;

use App\Entities\ClientEntity;
use App\Libraries\ClaJWT;
use Ramsey\Uuid\Uuid;

class ClientModel extends BaseModel
{
    protected $table                = 'hera_client';
    protected $primaryKey           = 'id';
    protected $useAutoIncrement     = true;
    protected $insertID             = 0;
    protected $returnType           = ClientEntity::class;
    protected $useSoftDeletes        = true;
    protected $protectFields        = true;
    protected $allowedFields        = ['application_id', 'nama','nick_name', 'access_token', 'access_token_expired', 'hit_limit', 'deleted_at'];

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
    protected $beforeInsert         = ['generateApplicationId', 'generateClientToken','generateNickName'];
    protected $afterInsert          = [];
    protected $beforeUpdate         = ['generateApplicationId', 'generateClientToken'];
    protected $afterUpdate          = [];
    protected $beforeFind           = [];
    protected $afterFind            = [];
    protected $beforeDelete         = [];
    protected $afterDelete          = [];

    protected $data_client = [];

    public function generateApplicationId(array $datas)
    {
        if (isset($datas['data']['application_id'])) return $datas;
        $application_id = Uuid::uuid4();
        $client = $this->where('application_id', $application_id)->first();
        if ($client) {
            return $this->generateApplicationId($datas);
        } else {
            $datas['data']['application_id'] = $application_id;
            return $datas;
        }
    }
    public function generateNickName(array $datas)
    {
        if (isset($datas['data']['nick_name'])) return $datas;
        $datas['data']['nick_name'] = strtolower(str_replace(" ","_", $datas['data']['nama']));
        return $datas;
    }
    public function generateClientToken(array $datas)
    {
        if (isset($datas['data']['access_token'])) return $datas;
        $token_data = [
            'application_id' => $datas['data']['application_id'],
        ];
        if (isset($datas['data']['access_token_expired'])) {
            $data_jwt = ClaJWT::encode($token_data, $datas['data']['access_token_expired'], true, false);
        } else {
            $data_jwt = ClaJWT::encode($token_data, null, false, false);
        }
        $datas['data']['access_token'] = $data_jwt['access_token'];
        $datas['data']['access_token_expired'] = $data_jwt['access_token_expired'] ?? null;
        return $datas;
    }

    public function updateWithToken($id, $data, $client)
    {
        if (!(bool)$data['resetToken']) {
            $data['application_id'] = $client->application_id;
            $data['access_token'] = $client->access_token;
        }
        if (!empty($data['access_token_expired'])) {
            $data['access_token_expired'] =  $data['access_token_expired'] . " 23:59:59";
        }
        return $this->update($id, $data);
    }

    public function findClientByNickname($nick_name)
	{
		return $this->where(['nick_name' => $nick_name])->first();
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
