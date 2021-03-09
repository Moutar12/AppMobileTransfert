<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Depot;
use App\Entity\Transactions;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use App\Repository\DepotRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
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
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var AgenceRepository
     */
    private $agenceRepository;
    private $storage;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(CompteRepository $compteRepository, SerializerInterface $serializer, UserRepository $userRepository,
                   AgenceRepository $agenceRepository,  EntityManagerInterface $manager , DepotRepository  $depotRepository,
    TokenStorageInterface $tokenStorage
    ){
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->manager = $manager;
        $this->depotRepository = $depotRepository;
        $this->agenceRepository = $agenceRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route(
     *      name="caisseDepot" ,
     *      path="/api/caissier/depot",
     *     methods={"POST"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::caisseDepot",
     *         "_api_resource_class"=Depot::class ,
     *         "_api_collection_operation_name"="caisseDepot"
     *     }
     *)
     *
     */
    public function caisseDepot(Request $request, Security $security): Response
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


//        $depotReq = $request->request->all();
//        //dd($depotReq);
//        $depot = new Depot();
//        $depot->setMontant($depotReq['montant']);
//        $depot->setDateDepot(new \DateTime());
//        $depot->setCompte($this->compteRepository->findOneBy(["id"=>(int)$depotReq]));
//        $depot->getCompte()->setSolde( $depot->getCompte()->getSolde($this->solde)+$depotReq['montant']);
//        //dd($depot);
//        $this->manager->persist($depot);
//        $this->manager->flush();
//
//        return $this->json("success", 200);

        // recup tous les donnees
        // $dataPostman = json_decode($request->getContent(), true);
        //all data from postman
        // recup tous les donnees
        // $dataPostman = json_decode($request->getContent(), true);
        $dataPostman =  json_decode($request->getContent());
        // dd($dataPostman);
        // denormalize
        // $depot = $this->serializer->denormalize($dataPostman, Depot::class);
        $montant = $dataPostman->montant ; //get montant
        // dd($montant);
        $utilisateur = $dataPostman->user ; //get utilisateur
        // dd($utilisateur);

        // Validate negatif number
        if($montant < 0) {
            // return new JsonResponse("Can be negative number!" ,400) ;
            return $this->json("le montant ne peut pas être négatif!",400);
        }

        // // Instancier Depot
        $newDepot = new Depot();
        $newDepot->setDateDepot(new \DateTime());
        $newDepot->setmontant($dataPostman->montant);
        $newDepot->setUser($security->getUser());
        //get id agence of utilisateur
        $idAgence = $this->userRepository->findOneBy(['id'=>(int)$utilisateur])->getAgence()->getId();
        //  dd($idAgence);
        // Id de l'agence ci_dessus on cherche son compte
        $focusCompte = $this->compteRepository->findBy(['agence'=>$idAgence]); //reper account
        // dd($focusCompte);
        $newDepot->setCompte($focusCompte->getCompt());
        // dd($newDepot);
        $this->manager->persist($newDepot);
        $focusCompte[0]->setSolde($focusCompte[0]->getSolde() + $montant);
        // dd($focusCompte);
        $this->manager->persist($focusCompte[0]);
        $this->manager->flush();

        return $this->json("Votre dépôt a réussi avec success!",201);
    }


    /**
     * @Route(
     *      name="annulerDepot" ,
     *      path="/api/caissier/depot/{id}",
     *     methods={"DELETE"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::annulerDepot",
     *         "_api_resource_class"=Depot::class ,
     *         "_api_collection_operation_name"="annulerDepot"
     *     }
     *)
     *
     */
        public function annulerDepot($id) {

            $userIdAnnulation = $this->tokenStorage->getToken()->getUser()->getId();
            $lastId= $this->genererNum->getLastIdDepot();
            if($id == $lastId){
                $depot = $this->depotRepository->findOneBy(['id'=>$id]);
                $userIdDepot = $depot->getUser()->getId();
                if ($userIdAnnulation == $userIdDepot){
                    $compte = $depot->getCompte();

                    if($compte->getSolde() > $depot->getMontant()){
                        $compte->setSolde($compte->getSolde() - $depot->getMontant());
                        $this->manager->persist($compte);
                        $this->manager->remove($depot);
                        $this->manager->flush();
                        return new JsonResponse("Depot annuler avec succee", 200, [], true);

                    }else{
                        return new JsonResponse(" annulation du depot imposible", 200, [], true);
                    }
                }else{
                    return new JsonResponse("Impossible d'annuler cette depot car il a ete effectuer par quelqu'un d'autre", 500, [], true);
                }
            }else{
                return new JsonResponse(" Impossible d'annuler cette depot car il n'est pas le dernier", 500, [], true);
            }

    }

}
