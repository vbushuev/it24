<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    const UPDATED_AT = null;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_goods';
    protected $fillable = ['good_id',"user_id"];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = 'timestamp';
}
