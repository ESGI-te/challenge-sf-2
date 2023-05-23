<?php

namespace App\Service;

use App\Repository\RecipeRequestRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class RecipeRequestService
{

    private RecipeRequestRepository $rqRepo;

    public function __construct(
        RecipeRequestRepository $rqRepo,
    ) {
        $this->rqRepo = $rqRepo;
    }


    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getRequestNumber($id):int
    {
        return $this->rqRepo->countDayRequest($id);
    }


}