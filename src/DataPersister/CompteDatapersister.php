<?php


namespace App\DataPersister;

use App\Entity\User;
use App\Entity\Compte;
use App\Repository\UserRepository;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommissionsRepository;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class CompteDatapersister implements ContextAwareDataPersisterInterface
{

    private $entityManager;
    /**
     * @var CompteRepository
     */
    private $compteRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var AgenceRepository
     */
    private $agenceRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $entityManager, CompteRepository $compteRepository, UserRepository $userRepository, AgenceRepository $agenceRepository)
    {
        $this->manager = $entityManager;
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository;
        $this->agenceRepository = $agenceRepository;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Compte;
    }

    public function persist($data, array $context = [])
    {
        $this->manager->persist($data);
        $this->manager->flush();
    }

    public function remove($data, array $context = [])
    {

        // get id compte
        $id = $data->getId();
        $compteBlock = $this->compteRepository->findById($id);

        $compteBlock[0]->setStatus(1);
        $block = $this->manager->persist($compteBlock[0]);
        $this->manager->flush($block);


        }


} 