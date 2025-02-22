<?php

namespace App\Repositories;

use App\Models\Offer;

class OfferRepository
{
    public function findBy(array $params): Offer|null
    {
        return Offer::where($params)->first();
    }

    public function create(array $params): Offer
    {
        return Offer::create($params);
    }

    public function delete(Offer $offer): bool
    {
        return $offer->delete();
    }
}
