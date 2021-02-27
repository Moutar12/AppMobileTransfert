<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Client;
use App\Entity\Transactions;
use App\Repository\CompteRepository;
use App\Repository\TransactionsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class TransactionController extends AbstractController
{

    private $userRepository;
    private $serializer;
    /**
     * @var CompteRepository
     */
    private $competenceReopsitory;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var TransactionsRepository
     */
    private $transactionsRepository;
    /**
     * @var CompteRepository
     */
    private $competeReopsitory;

    public function __construct(UserRepository $userRepository, SerializerInterface $serialize, CompteRepository $compteRepository,
                                EntityManagerInterface $entityManager, ValidatorInterface $validator, TransactionsRepository $transactionsRepository
    ){
        $this->userRepository = $userRepository;
        $this->serializer = $serialize;
        $this->competeReopsitory = $compteRepository;
        $this->manager = $entityManager;
        $this->validator =$validator;
        $this->transactionsRepository = $transactionsRepository;
    }

    /**
     * @Route(
     *      name="transfertClient" ,
     *      path="/api/transactions" ,
     *     methods={"POST"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::transfertClient",
     *         "_api_resource_class"=Transactions::class ,
     *         "_api_collection_operation_name"="transfertClient"
     *     }
     *)
     * @param Request $request
     * @param SerializerInterface $serializer
     * @return Response
     */
    public function transfertClient(Request $request, SerializerInterface $serializer): Response
    {
        $dataPost =  json_decode($request->getContent());
        //dd($dataPost);

        $montantEnvoie = $dataPost->montant;
        dd($montantEnvoie);
        $userEnvoie = $dataPost->compte;
        $compte = $this->competeReopsitory->findOneBy(['id'=>(int)$userEnvoie]);

        if ($compte->getSolde() < $montantEnvoie){
            return $this->json('Votre solde est insuffisant');
        }

        if($montantEnvoie <= 5000 ) {
            $fraisEnvoie = 425;
            $montantActuel = $montantEnvoie - $fraisEnvoie;
            // dd($realMontant);
        } else if ($montantEnvoie > 5000 && $montantEnvoie <= 10000) {
            $fraisEnvoie = 850;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 10000 && $montantEnvoie <= 15000) {
            $fraisEnvoie = 1270;
            $montantActuel =  $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 15000 && $montantEnvoie <= 20000) {
            $fraisEnvoie = 1695;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 20000 && $montantEnvoie <= 50000) {
            $fraisEnvoie = 2500;
            $montantActuel =  $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 50000 && $montantEnvoie <= 60000) {
            $fraisEnvoie = 3000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 60000 && $montantEnvoie <= 75000) {
            $fraisEnvoie = 4000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 75000 && $montantEnvoie <= 120000) {
            $fraisEnvoie = 5000;
            $montantActuel =  $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 120000 && $montantEnvoie <= 150000) {
            $fraisEnvoie = 6000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 150000 && $montantEnvoie <= 200000) {
            $fraisEnvoie = 7000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 200000 && $montantEnvoie <= 250000) {
            $fraisEnvoie = 8000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 250000 && $montantEnvoie <= 300000) {
            $fraisEnvoie = 9000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 300000 && $montantEnvoie <= 400000) {
            $fraisEnvoie = 12000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 400000 && $montantEnvoie <= 750000) {
            $fraisEnvoie = 15000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 750000 && $montantEnvoie <= 900000) {
            $fraisEnvoie = 22000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 900000 && $montantEnvoie <= 1000000) {
            $fraisEnvoie = 25000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }else if ($montantEnvoie > 1000000 && $montantEnvoie <= 1125000) {
            $fraisEnvoie = 27000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }
        else if ($montantEnvoie > 1125000 && $montantEnvoie <= 1400000) {
            $fraisEnvoie = 30000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }
        else if ($montantEnvoie > 1400000 && $montantEnvoie <= 2000000) {
            $fraisEnvoie = 30000;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }  else if ( $montantEnvoie > 2000000) {
            $fraisEnvoie = $montantEnvoie * 0.02;
            $montantActuel = $montantEnvoie - $fraisEnvoie  ;
        }

        $fraisEtat = $fraisEnvoie * 0.4;
        $fraisSystem = $fraisEnvoie * 0.3;
        $FraisEnvoies = ($fraisSystem) * (1/3);
        $FraisRetrais = ($fraisSystem) * (2/3);

        $compte->getSolde(($compte->getSolde() - $montantEnvoie) + $FraisEnvoies);

        $envoie = $dataPost->userDepot;
        $clientEnvoie = new Client();
        $clientEnvoie->setNomComplet($envoie->nomComplet);
        $clientEnvoie->setPhone($envoie->phone);
        $clientEnvoie->setCni($envoie->cni);
        $this->manager->persist($clientEnvoie);

        $retrait = $dataPost->userRetrait;
        $clientRetrait = new Client();
        $clientRetrait->setNomComplet($retrait->nomComplet);
        $clientRetrait->setPhone($retrait->phone);
        $clientRetrait->setCni($retrait->cni);
        $this->manager->persist($clientRetrait);


        $transactions = new Transactions();
        $transactions->setMontant($montantActuel);
        $times = new \DateTime();
        $transactions->setDateDepot($times);
        $transactions->setDateAnnulation($times);
        $transactions->setFraisEtat($fraisEtat);
        $transactions->setFraisEnvoie($FraisEnvoies);
        $transactions->setFraisRetrait($FraisRetrais);
        $transactions->setFraisSystem($fraisSystem);
        $transactions->setClientDepot($clientEnvoie);
        $transactions->setClientRetrait($clientRetrait);
        $transactions->setCodeTransaction(rand());
        $transactions->setCompte($this->competeReopsitory->findOneBy(['id'=>(int)$userEnvoie]));

        $this->manager->persist($transactions);
        $this->manager->flush();

        return $this->json("sucess", 200);




    }
}
