<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = "countries";
    protected $primaryKey = 'countryID';
    /**
     * Fillable fields
     *
     * @var array
     */
    protected $fillable = [
        'countryID',
        'countryName',
        'publishFlag'
    ];

    public function cities()
    {
        return $this->hasMany('App\City', 'countryID');
    }

    public function universities()
    {
        return $this->hasManyThrough('App\University', 'App\City', 'countryID', 'cityID');
    }

    public function universityNames()
    {
        return $this->hasManyThrough('App\University', 'App\City', 'countryID', 'cityID')->lists('universityName');
    }

    public function homeCountryToExchangeStudents()
    {
        $universityList = $this->universities()->lists('universityID');
        $exchangeStudents = ExchangeStudent::whereIn('homeUniversityID', $universityList)->get();
        return $exchangeStudents;
    }

    public function hostCountryToExchangeStudents()
    {
        $universityList = $this->universities()->lists('universityID');
        $exchangeStudents = ExchangeStudent::whereIn('hostUniversityID', $universityList)->get();
        return $exchangeStudents;
    }
}
