<?php

namespace App\Policies;

use App\Models\MigrationRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MigrationRecordPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MigrationRecord  $migrationRecord
     * @return mixed
     */
    public function view(User $user, MigrationRecord $migrationRecord)
    {
        return $user->id === $migrationRecord->user_id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MigrationRecord  $migrationRecord
     * @return mixed
     */
    public function update(User $user, MigrationRecord $migrationRecord)
    {
        return $user->id === $migrationRecord->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\MigrationRecord  $migrationRecord
     * @return mixed
     */
    public function delete(User $user, MigrationRecord $migrationRecord)
    {
        return $user->id === $migrationRecord->user_id;
    }
}
