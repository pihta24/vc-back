<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tasks
 *
 * @property int $id
 * @property string $title
 * @property string $text
 * @property int $creator
 * @property int $owner
 * @property string $spectators
 * @property int $time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereOwner($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereSpectators($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property bool $completed
 * @method static \Illuminate\Database\Eloquent\Builder|Tasks whereCompleted($value)
 */
class Tasks extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'text', 'creator', 'owner', 'spectators', 'time'];
}
