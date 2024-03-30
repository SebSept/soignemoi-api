<?php

namespace App\Controller;

use App\Repository\DoctorRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class RecentDoctors extends AbstractController
{
    public function __construct(private readonly DoctorRepository $doctorRepository)
    {
    }

    public function __invoke(): array
    {
        return $this->doctorRepository->findBy(['medicalSpeciality' => 'pédiatrie']);
    }

}