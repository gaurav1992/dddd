<?php

namespace Pingpong\Admin\Repositories\Universities;

use Pingpong\Admin\Entities\University;
use DB;
class EloquentUniversityRepository implements UniversityRepository
{
    public function perPage()
    {
        return config('admin.university.perpage');
    }

    public function getModel()
    {
        $model = config('admin.university.model');
        
        return new $model;
    }

    public function getArticle()
    {
        return $this->getModel();
    }

    public function allOrSearch($searchQuery = null)
    {
        if (is_null($searchQuery)) {
            return $this->getAll();
        }

        return $this->search($searchQuery);
    }

    public function getAll()
    {
        return $this->getArticle()->latest()->where('id', '<>', '1')->paginate($this->perPage());
    }

    public function search($searchQuery)
    {
        $search = "%{$searchQuery}%";
		
         $countryame = DB::table('countries')->where('countryName',$searchQuery)->lists('countryID'); 

		 $cityid=DB::table('cities')->whereIn('countryID',$countryame)->lists('id');
	
		return	$this->getArticle()->where('universityName', 'like', $search)->where('id', '<>', '1')
							->orWhere(function ($query) use($cityid){
								$query->whereIn('cityID',$cityid);
							  })
								
							->paginate($this->perPage());
		
		
	 
    }

    public function findById($id)
    {
        return $this->getArticle()->find($id);
    }

    public function findBy($key, $value, $operator = '=')
    {
        return $this->getArticle()->where($key, $operator, $value)->paginate($this->perPage());
    }

    public function delete($id)
    {
        $article = $this->findById($id);
		
        if (!is_null($article)) {
            $article->delete();
			
            return true;
        }

        return false;
    }

    public function create(array $data)
    {
        return $this->getModel()->create($data);
    }
}
