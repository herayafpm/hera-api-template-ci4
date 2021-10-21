<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserDevice extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'               => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
			'user_agent'            => ['type' => 'text', 'default' => ''],
			'client_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'device_uuid'            => ['type' => 'varchar', 'constraint' => 255],
			'username'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
			],
			'gfcm'       => [
				'type'           => 'TEXT',
				'null'			=> true
			],
			'last_request'       => ['type' => 'datetime',  'null' => true],
			'created_at'       => ['type' => 'datetime', 'null' => true],
			'updated_at'       => ['type' => 'datetime', 'null' => true],
			'deleted_at'       => ['type' => 'datetime', 'null' => true],
		]);
		$this->forge->addKey('id', true);
		$this->forge->addUniqueKey(['user_agent', 'client_id', 'device_uuid', 'username']);
		$this->forge->addForeignKey('client_id', 'hera_client', 'id', '', 'CASCADE');
		$this->forge->createTable('hera_user_device');
	}

	public function down()
	{
		$this->forge->dropTable('hera_user_device');
	}
}
