<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "reviews";
    protected $primaryKey = 'id';
    /**
     * Fillable fields
     *
     * @var array 
     */
    protected $fillable = [
        'universityId',
        'userId',
        'message'
    ];

    public function userdetail()
    {
        return $this->belongsTo('App\User','userId');
    }

    
}
