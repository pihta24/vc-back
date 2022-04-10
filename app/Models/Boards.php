<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Boards
 *
 * @property int $id
 * @property string $title
 * @property string $tasks
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Boards newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Boards newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Boards query()
 * @method static \Illuminate\Database\Eloquent\Builder|Boards whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Boards whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Boards whereTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Boards whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Boards whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Boards extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'tasks'];
}
