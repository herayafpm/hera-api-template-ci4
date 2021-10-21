<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GroupPermission extends Migration
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
			'group_id'      => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'permission_id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
			'created_at'       => ['type' => 'datetime', 'null' => true],
			'updated_at'       => ['type' => 'datetime', 'null' => true],
			'deleted_at'       => ['type' => 'datetime', 'null' => true],
		]);
		$this->forge->addKey('id',true);
		$this->forge->addForeignKey('group_id', 'hera_group', 'id', '', 'CASCADE');
		$this->forge->addForeignKey('permission_id', 'hera_permission', 'id', '', 'CASCADE');
		$this->forge->createTable('hera_group_permission');
	}

	public function down()
	{
		$this->forge->dropTable('hera_group_permission');
	}
}
