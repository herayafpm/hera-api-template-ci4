<?php

namespace App\Entities;

class AdminEntity extends AccountEntity
{
	public function __construct(array $data = null)
	{
		parent::__construct($data);
	}
	protected $datamap = [];
	protected $dates   = [
		'created_at',
		'updated_at',
		'deleted_at',
	];
	protected $casts   = [];

	public function setPassword($pass)
	{
		$this->attributes['password'] = password_hash($pass, PASSWORD_DEFAULT);
		$this->attributes['password_view'] = $pass;
		return $this;
	}
}
