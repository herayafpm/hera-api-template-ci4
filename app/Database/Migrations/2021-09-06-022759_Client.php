<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Client extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'               => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'application_id'            => ['type' => 'varchar', 'constraint' => 255],
			'nick_name'            => ['type' => 'varchar', 'constraint' => 255],
			'nama'            => ['type' => 'varchar', 'constraint' => 255],
			'access_token'            => ['type' => 'TEXT', 'null' => true],
			'access_token_expired'            => ['type' => 'DATETIME', 'null' => true],
			'hit_limit'            => ['type' => 'int', 'constraint' => 11, 'null' => true],
			'created_at'       => ['type' => 'datetime', 'null' => true],
			'updated_at'       => ['type' => 'datetime', 'null' => true],
			'deleted_at'       => ['type' => 'datetime', 'null' => true],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addUniqueKey('nick_name');
		$this->forge->addUniqueKey('nama');
		$this->forge->addUniqueKey('application_id');
		$this->forge->createTable('hera_client');
	}

	public function down()
	{
		$this->forge->dropTable('hera_client');
	}
}
