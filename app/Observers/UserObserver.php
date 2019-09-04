<?php

namespace App\Observers;

use App\Models\User;
use Exception;

class UserObserver
{
    /**
     * Listen to the User retrieved event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function retrieved(User $user)
    {
        //
    }

    /**
     * Listen to the User creating event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function creating(User $user)
    {
        //
    }

    /**
     * Listen to the User created event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        //
    }

    /**
     * Listen to the User updating event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updating(User $user)
    {
        if ($user->id == 1) {
            // unset($user->name);
            // unset($user->email);

            if ($user->name != 'admin' || $user->email != 'admin@localhost.test') {
                throw new Exception('不允许修改管理员昵称和邮箱');
            }
        }
    }

    /**
     * Listen to the User updated event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * Listen to the User saving event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function saving(User $user)
    {
        if ($user->getAttribute('id') != 1) {
            if (mb_stripos($user->name, 'admin') === 0) {
                throw new Exception('非管理员，用户名不能以「admin」开头，无论大小写');
            }
        }
    }

    /**
     * Listen to the User saved event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function saved(User $user)
    {
        //
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        if ($user->id == 1) {
            throw new Exception('不允许删除管理员');
        }
    }

    /**
     * Listen to the User deleted event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Listen to the User restoring event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restoring(User $user)
    {
        //
    }

    /**
     * Listen to the User restored event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }
}
