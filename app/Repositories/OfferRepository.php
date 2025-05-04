<?php

namespace App\Repositories;

use App\Models\Offer;

/**
 * @extends Repository<Offer>
 */
class OfferRepository extends Repository
{
    public function __construct()
    {
        parent::__construct( Offer::class );
    }
}
