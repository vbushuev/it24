<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCatalog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_catalogs';
    protected $fillable = ['title',"user_id","catalog_id","parent_id"];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = 'timestamp';
}
