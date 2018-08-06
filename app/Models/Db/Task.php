<?php

namespace App\Models\Db;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $author_id
 * @property int $performer_id
 * @property int $organization_id
 * @property string $name
 * @property int $status
 * @property string $cancellation_reason
 * @property string $rejection_reason
 * @property string $deadline
 * @property string $created_at
 * @property string $updated_at
 * @property User $performer
 * @property User $author
 * @property Organization $organization
 */
class Task extends Model
{
    /**
     * Всевозможные статусы таска
     *
     * @const
     */
    const STATUS_NEW = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_REJECTED = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_DONE = 5;

    /**
     * @var array
     */
    protected $fillable = ['author_id', 'performer_id', 'organization_id', 'name', 'status', 'cancellation_reason', 'rejection_reason', 'deadline', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    public static $statusList = [
        self::STATUS_NEW         => ['id' => self::STATUS_NEW, 'title' => 'Нове'],
        self::STATUS_IN_PROGRESS => ['id' => self::STATUS_IN_PROGRESS, 'title' => 'Прийняте'],
        self::STATUS_REJECTED    => ['id' => self::STATUS_REJECTED, 'title' => 'Відхилене'],
        self::STATUS_CANCELLED   => ['id' => self::STATUS_CANCELLED, 'title' => 'Скасоване'],
        self::STATUS_DONE        => ['id' => self::STATUS_DONE, 'title' => 'Виконане'],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function performer()
    {
        return $this->belongsTo(User::class, 'performer_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Получить текущий статус задачи
     *
     * @return string
     */
    public function getCurrentStatus(): string
    {
        return self::$statusList[$this->status]['title'];
    }

}
