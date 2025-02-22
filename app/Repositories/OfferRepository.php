<?php

namespace App\Repositories;

use App\Models\Offer;

class OfferRepository
{
    public function create(array $params): Offer
    {
        return Offer::create($params);
    }
}
