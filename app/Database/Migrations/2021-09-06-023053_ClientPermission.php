<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ClientPermission extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id'          => [
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'permission_id'      => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'client_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'created_at'       => ['type' => 'datetime', 'null' => true],
			'updated_at'       => ['type' => 'datetime', 'null' => true],
			'deleted_at'       => ['type' => 'datetime', 'null' => true],
		]);
		$this->forge->addKey('id',true);
		$this->forge->addForeignKey('permission_id', 'hera_permission', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('client_id', 'hera_client', 'id', '', 'CASCADE');
		$this->forge->createTable('hera_client_permission');
	}

	public function down()
	{
		$this->forge->dropTable('hera_client_permission');
	}
}
