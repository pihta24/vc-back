<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BoardsAccess
 *
 * @property int $id
 * @property int $user_id
 * @property int $board_id
 * @property string $access
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|BoardsAccess newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoardsAccess newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BoardsAccess query()
 * @method static \Illuminate\Database\Eloquent\Builder|BoardsAccess whereAccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoardsAccess whereBoardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoardsAccess whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoardsAccess whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoardsAccess whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BoardsAccess whereUserId($value)
 * @mixin \Eloquent
 */
class BoardsAccess extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'board_id', 'access'];
}
