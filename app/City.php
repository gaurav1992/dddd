<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model {
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "cities";
    protected $primaryKey = 'id';
    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'cityName',
        'countryID'
    ];

    public function country()
    {
        return $this->belongsTo('App\Country','countryID');
    }

    public function universities()
    {
        return $this->hasMany('App\University','cityID');
    }

    public function universityNames()
    {
        return $this->hasMany('App\University','cityID')->lists('universityName');
    }

    
}
