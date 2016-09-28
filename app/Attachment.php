<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Attachment
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $filename
 * @property string $path
 * @property string $md5sum
 * @property string $sha1sum
 * @property boolean $state
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read User $user
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereFilename($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment wherePath($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereMd5sum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereSha1sum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Attachment whereUpdatedAt($value)
 */
class Attachment extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
