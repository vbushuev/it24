<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    const UPDATED_AT = null;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'catalogs';
    protected $fillable = ['parent_id',"title","level"];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = 'timestamp';
}
