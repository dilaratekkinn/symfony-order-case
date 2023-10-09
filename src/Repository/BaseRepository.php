<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class BaseRepository extends ServiceEntityRepository
{
    /**
     * @return void
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
