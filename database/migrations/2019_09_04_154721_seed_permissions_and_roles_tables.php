<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class SeedPermissionsAndRolesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Reset cached roles and permissions
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // create permissions
        $this->createPermissions();

        // create roles and assign created permissions
        $this->createRoles();

        // assign role to user
        $this->assignRoles();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Reset cached roles and permissions
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 清空所有数据表数据
        $tableNames = config('permission.table_names');

        Model::unguard();

        // disable foreign key check for this connection
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table($tableNames['role_has_permissions'])->truncate();
        DB::table($tableNames['model_has_roles'])->truncate();
        DB::table($tableNames['model_has_permissions'])->truncate();
        DB::table($tableNames['roles'])->truncate();
        DB::table($tableNames['permissions'])->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        Model::reguard();
    }

    /**
     * 创建权限
     */
    protected function createPermissions()
    {
        Permission::create([
            'name' => 'permissions_index',
            'guard_name' => 'api',
            'description' => '查看权限列表',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'permissions_show',
            'guard_name' => 'api',
            'description' => '查看权限详情',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'permissions_store',
            'guard_name' => 'api',
            'description' => '创建权限',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'permissions_update',
            'guard_name' => 'api',
            'description' => '更新权限',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'permissions_destroy',
            'guard_name' => 'api',
            'description' => '删除权限',
            'is_basic' => true,
        ]);

        Permission::create([
            'name' => 'roles_index',
            'guard_name' => 'api',
            'description' => '查看角色列表',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'roles_show',
            'guard_name' => 'api',
            'description' => '查看角色详情',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'roles_store',
            'guard_name' => 'api',
            'description' => '创建角色',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'roles_update',
            'guard_name' => 'api',
            'description' => '更新角色',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'roles_destroy',
            'guard_name' => 'api',
            'description' => '删除角色',
            'is_basic' => true,
        ]);

        Permission::create([
            'name' => 'users_index',
            'guard_name' => 'api',
            'description' => '查看用户列表',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'users_show',
            'guard_name' => 'api',
            'description' => '查看用户详情',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'users_store',
            'guard_name' => 'api',
            'description' => '创建用户',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'users_update',
            'guard_name' => 'api',
            'description' => '更新用户',
            'is_basic' => true,
        ]);
        Permission::create([
            'name' => 'users_destroy',
            'guard_name' => 'api',
            'description' => '删除用户',
            'is_basic' => true,
        ]);
    }

    /**
     * 创建角色并分配权限
     */
    protected function createRoles()
    {
        // 管理员
        $role = Role::create([
            'name' => 'administrator',
            'guard_name' => 'api',
            'description' => '管理员',
            'is_basic' => true,
        ]);
        $role->givePermissionTo(Permission::where('guard_name', 'api')->get());

        // 测试员
        $role = Role::create([
            'name' => 'test',
            'guard_name' => 'api',
            'description' => '测试员',
            'is_basic' => true,
        ]);
        $role->givePermissionTo([
            'users_index',
            'users_show',
        ]);
    }

    /**
     * 为用户分配角色
     *
     */
    protected function assignRoles()
    {
        // 管理员
        $role = Role::find(1);
        $user = User::find(1);
        $user->assignRole($role);
    }
}
