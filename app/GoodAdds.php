<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoodAdds extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'goods_adds';
    protected $fillable = ["user_id","good_id",'price_add'];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = 'timestamp';
}
