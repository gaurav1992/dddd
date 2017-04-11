<?php

namespace Pingpong\Admin\Repositories\Reviews;

use Pingpong\Admin\Repositories\Repository;

interface ReviewRepository extends Repository
{
    public function getArticle();
}
