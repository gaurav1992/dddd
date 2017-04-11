<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "universities";
    protected $primaryKey = 'id';
    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'universityName',
        'cityID'
    ];

    public function city()
    {
        return $this->belongsTo('App\City','cityID');
    }

    public function universityCity($id){
        $city = $this->city()->find($id);
        return $city;
    }

    public function country()
    {
        $cityID = $this->city->cityID;
        $country = City::where('cityID', $cityID)->first();
        return $country;
    }
	 public function reviews()
    {
        return $this->hasMany('App\Review','universityId');
    }
}
