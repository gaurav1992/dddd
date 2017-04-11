<?php

namespace Pingpong\Admin\Repositories\Reviews;

use Pingpong\Admin\Entities\Review;
use DB;


class EloquentReviewRepository implements ReviewRepository
{
    public function perPage()
    {
        return config('admin.review.perpage');
    }

    public function getModel()
    {
        $model = config('admin.review.model');
        
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
        return $this->getArticle()->latest()->with(['userdetail','univdetail'])->paginate($this->perPage());
    }

    public function search($searchQuery)
    {
        $search = "%{$searchQuery}%";
		
		
		$usersList = DB::table('users')
								->orWhere('fname','like',$search)
								->orWhere('lname','like',$search)
								->lists('id');
		$univList = DB::table('universities')
								->where('universityName','like',$search)
								->lists('id');						
		
        return $this->getArticle()->orWhere('message', 'like', $search)
								  ->orWhereIn('userId',$usersList)
								  ->orWhereIn('universityId',$univList)
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
