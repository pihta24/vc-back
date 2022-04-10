<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Tokens
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Tokens newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tokens newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Tokens query()
 * @method static \Illuminate\Database\Eloquent\Builder|Tokens whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tokens whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tokens whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tokens whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Tokens whereUserId($value)
 * @mixin \Eloquent
 */
class Tokens extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'token'];
}
