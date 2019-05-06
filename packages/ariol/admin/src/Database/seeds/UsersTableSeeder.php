<?php namespace Ariol\Admin\Database\Seeds;

use DB;
use Ariol\Models\Users\User;
use Ariol\Models\Users\Role;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = Role::where('alias', 'admin')->count();
        if ($roles == 0) {
            DB::table('roles')->insert([
                'id' => '1',
                'alias' => 'admin',
                'name' => 'Администратор'
            ]);
        }

        $user = User::where('email', 'admin@ariol.by')->first();
        if (! $user) {
            DB::table('users')->insert([
                'role_id' => '1',
                'name' => 'Администратор',
                'email' => 'admin@ariol.by',
                'password' => bcrypt('56325632')
            ]);
        }
    }
}
