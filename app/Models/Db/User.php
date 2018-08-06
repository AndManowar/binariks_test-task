<?php

namespace App\Models\Db;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $name;
 * @property string $surname;
 * @property int $role;
 * @property string $email;
 * @property string $password;
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property Task[] $tasks
 * @property UserOrganization[] $organizations
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * Роль "керівник"
     *
     * @const
     */
    const ROLE_OWNER = 1;

    /**
     * Роль "виконавець"
     *
     * @const
     */
    const ROLE_PERFORMER = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'surname', 'email', 'password', 'role',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'performer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organizations()
    {
        return $this->hasMany(UserOrganization::class);
    }

    /**
     * Получить полное имя пользователя
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->surname.' '.$this->name;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
