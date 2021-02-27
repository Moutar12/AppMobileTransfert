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
use Symfony\Component\HttpFoundation\RequestStack;
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

    public function __construct(CompteRepository $compteRepository, SerializerInterface $serializer, UserRepository $userRepository,
           EntityManagerInterface $manager , ValidatorInterface $validator, DepotRepository  $depotRepository, RequestStack $request
    ){
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository;
        $this->serializer=$serializer;
        $this->manager = $manager;
        $this->validator = $validator;
        $this->depotRepository = $depotRepository;
        $this->request=$request;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Depot;
    }

    public function persist($data, array $context = [])
    {
        // call your persistence layer to save $data
        $dataActu =$this->request->getCurrentRequest()->getContent();
        //dd($dataActu);
        $TabData=$this->serializer->decode($dataActu, 'json');
        //dd($context['previous_data']);

        $compt=$this->compteRepository->findOneBy(["id"=>(int)$TabData]);
        //dd( $compt);
        $data->setMontant($TabData['montant']);
        $data->setCompte($compt);

        $data->getCompte()->setSolde( $data->getCompte()->getSolde()+$TabData['montant']);
        //dd($data);
        //$user=$this->token->getToken()->getUser();
        //dd($user);
        $data->setUsers();
        //dd($data);
        $data->setDateDepot(new \DateTime());




        $this->manager->persist($data);
        $this->manager->flush();

        return $data;
    }

    public function remove($data, array $context = [])
    {
        // call your persistence layer to delete $data
    }
}