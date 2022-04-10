<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ShareKeys
 *
 * @property int $id
 * @property string $share_key
 * @property int $task
 * @property int $expires
 * @property int $visitors
 * @property bool $can_edit
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys query()
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys whereCanEdit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys whereExpires($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys whereShareKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys whereTask($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ShareKeys whereVisitors($value)
 * @mixin \Eloquent
 */
class ShareKeys extends Model
{
    use HasFactory;
    protected $fillable = ['share_key', 'task', 'expires', 'visitors', 'can_edit'];
}
