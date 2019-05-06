<?php namespace Ariol\Models\Users;

use Ariol\Models\Model;

/**
 * Модель, которая связывает права с ролями.
 * @package Ariol\Models\Users
 */
class RolePermissions extends Model
{
    /**
     * Используемая таблица.
     *
     * @var string
     */
    protected $table = 'permission_role';

    /**
     * Разрешаем все поля для автозаполнения.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * В таблице не используются timestamp'ы.
     *
     * @var bool
     */
    public $timestamps = false;
}
