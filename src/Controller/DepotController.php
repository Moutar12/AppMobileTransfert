<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Depot;
use App\Repository\CompteRepository;
use App\Repository\DepotRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DepotController extends AbstractController
{


    /**
     * @var CompteRepository
     */
    private $compteRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var SerializerInterface
     */
    private $serilizer;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var DepotRepository
     */
    private $depotRepository;
    private $solde;

    public function __construct(CompteRepository $compteRepository, SerializerInterface $serializer, UserRepository $userRepository,
                                EntityManagerInterface $manager , ValidatorInterface $validator, DepotRepository  $depotRepository
    ){
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository;
        $this->serilizer = $serializer;
        $this->manager = $manager;
        $this->validator = $validator;
        $this->depotRepository = $depotRepository;
    }

    /**
     * @Route(
     *      name="caisseDepot" ,
     *      path="/api/depot",
     *     methods={"POST"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::caisseDepot",
     *         "_api_resource_class"=Depot::class ,
     *         "_api_collection_operation_name"="caisseDepot"
     *     }
     *)
     *
     */
    public function caisseDepot(Request $request, SerializerInterface $serializer): Response
    {
       /* $dataDepot = json_decode($request->getContent());

        //$montantDepot = $dataDepot->getMontant();

       // $caissier = $dataDepot->getCompte();
        //dd($caissier);
        $compte = $this->compteRepository->findOneBy(["id"=>(int)$dataDepot]);

       // $compte->getSolde(($compte->getSolde() + $montantDepot));

        $depotUser = $dataDepot;
        $depot = new Depot();
        $time = new \DateTime();
        $depot->setMontant($depotUser);
        $depot->setDateDepot($time);
        $depot->setCompte($this->compteRepository->findOneBy(["id"=>(int)$dataDepot]));
        dd($depot);*/


        $depotReq = $request->request->all();
        //dd($depotReq);
        $depot = new Depot();
        $depot->setMontant($depotReq['montant']);
        $depot->setDateDepot(new \DateTime());
        $depot->setCompte($this->compteRepository->findOneBy(["id"=>(int)$depotReq]));
        $depot->getCompte()->setSolde( $depot->getCompte()->getSolde($this->solde)+$depotReq['montant']);
        //dd($depot);
        $this->manager->persist($depot);
        $this->manager->flush();

        return $this->json("success", 200);

}
    
}
