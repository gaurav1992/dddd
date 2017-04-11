<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "user_types";
    protected $primaryKey = 'id';
    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'title'
    ];

 
}
