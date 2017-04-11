<?php

namespace Pingpong\Admin\Repositories\Subscribers;

use Pingpong\Admin\Repositories\Repository;

interface SubscriberRepository extends Repository
{
    public function getArticle();
}
