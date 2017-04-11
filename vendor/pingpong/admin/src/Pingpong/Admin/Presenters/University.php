<?php namespace Pingpong\Admin\Presenters;

use Pingpong\Presenters\Presenter;

class University extends Presenter
{

    public function image_path()
    {
        return public_path("images/universities/{$this->entity->image}");
    }
}
