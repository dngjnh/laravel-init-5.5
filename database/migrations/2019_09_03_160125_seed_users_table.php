<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\User;

class SeedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = factory(User::class)->make();
        $user_array = $user->makeVisible(['password', 'remember_token'])->toArray();

        User::insert($user_array);

        // admin
        $user = User::find(1);
        $user->name = 'admin';
        $user->email = 'admin@localhost.test';
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('users')->truncate();
    }
}
