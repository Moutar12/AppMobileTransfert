<?php
namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Depot;
use App\Entity\Compte;
use App\Repository\CompteRepository;
use App\Repository\DepotRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\SerializerInterface;

final class DepotDatapersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var CompteRepository
     */
    private $compteRepository;
    /**
     * @var SerializerInterface
     */
    private $serilizer;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var DepotRepository
     */
    private $depotRepository;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var RequestStack
     */
    private $request;
    private $serializer;
    /**
     * @var Security
     */
    private $security;

    public function __construct(CompteRepository $compteRepository, SerializerInterface $serializer, UserRepository $userRepository,
           EntityManagerInterface $manager , Security $security, DepotRepository  $depotRepository, RequestStack $request
    ){
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository;
        $this->serializer=$serializer;
        $this->manager = $manager;
        $this->security = $security;
        $this->depotRepository = $depotRepository;
        $this->request=$request;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Depot;
    }

    public function persist($data, array $context = [])
    {
        $montant = $data->getMontant();
        $data->getAgence()->setSolde($montant);
        $data->setCaissier($this->security->getUser());
        $this->manager->persist($data);
        $this->manager->flush();
        return new JsonResponse("success", 200);

    }

    public function remove($data, array $context = [])
    {
        // call your persistence layer to delete $data
    }
}