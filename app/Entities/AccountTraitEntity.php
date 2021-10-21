<?php namespace App\Entities;

trait AccountTraitEntity{
    public function inGroup($groups)
	{
		$username = $this->attributes['username'];
		if ($username === 0) {
			return false;
		}

		if (!is_array($groups)) {
			$groups = [$groups];
		}
		$userGroups = $this->user_group_model->getGroupsForUser($username);
		if (empty($userGroups)) {
			return false;
		}

		foreach ($groups as $group) {
			if (is_numeric($group)) {
				$ids = array_column($userGroups, 'group_id');
				if (in_array($group, $ids)) {
					return true;
				}
			} else if (is_string($group)) {
				$names = array_column($userGroups, 'nama');
				if (in_array($group, $names)) {
					return true;
				}
			}
		}

		return false;
	}

	public function groups()
	{
		return $this->user_group_model->join("hera_group","hera_user_group.group_id = hera_group.id")->where('username',$this->attributes['username'])->findColumn('nama');
	}



	public function hasPermission($permission)
	{

		// @phpstan-ignore-next-line
		if (empty($permission) || (!is_string($permission) && !is_numeric($permission))) {
			return null;
		}
		$permission = "{$this->attributes['appName']}_".$permission;
		$username = $this->attributes['username'];

		if (empty($username)) {
			return null;
		}

		// Get the Permission ID
		$permissionId = $this->getPermissionID($permission);

		if (!is_numeric($permissionId)) {
			return false;
		}

		// First check the permission model. If that exists, then we're golden.
		if ($this->group_permission_model->doesUserHavePermission($username, (int)$permissionId)) {
			return true;
		}

		// Still here? Then we have one last check to make - any user private permissions.
		return $this->doesUserHavePermission($username, (int)$permissionId);
	}

	public function doesUserHavePermission($username, $permission)
	{
		$permissionId = $this->getPermissionID($permission);

		if (!is_numeric($permissionId)) {
			return false;
		}

		if (empty($username)) {
			return null;
		}

		return $this->group_permission_model->doesUserHavePermission($username, $permissionId);
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
}