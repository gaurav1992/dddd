<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "subscribers";
    protected $primaryKey = 'email';
    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'email',
    ];
}
