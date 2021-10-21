<?php

namespace App\Models;

use CodeIgniter\Model;

class BaseModel extends Model
{
    protected $DBGroup  = 'default';
    protected $message;

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function setDBGroup($db)
	{
		$this->DBGroup = $db;
        $this->db = db_connect($db,true);
		return $this;
	}

    public function getTable()
    {
        return $this->table;
    }
    public function getTableAs()
    {
        return $this->tableAs;
    }
}
