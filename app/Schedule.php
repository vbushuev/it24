<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'download_schedules';
    protected $fillable = ['title',"user_id","remote_srv","remote_user","remote_pass","period",'catalogs','goods','price_add','mycatalog'];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = 'timestamp';
}
