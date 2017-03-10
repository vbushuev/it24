<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Uploads extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'upload_transactions';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = 'timestamp';
}
