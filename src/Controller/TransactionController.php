<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Client;
use App\Entity\ResumeTransaction;
use App\Entity\Transactions;
use App\Repository\AgenceRepository;
use App\Repository\ClientRepository;
use App\Repository\CommissionsRepository;
use App\Repository\CompteRepository;
use App\Repository\TarifRepository;
use App\Repository\TransactionsRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints\Json;

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
    /**
     * @var CommissionsRepository
     */
    private $commissionRepository;
    /**
     * @var TarifRepository
     */
    private $tarifRepository;
    /**
     * @var ClientRepository
     */
    private $clientRepository;
    /**
     * @var TokenStorageInterface
     */
    private $storage;
    /**
     * @var CompteRepository
     */
    private $compteReopsitory;
    /**
     * @var AgenceRepository
     */
    private $agenceRepository;
    private $commissionsRepository;

    public function __construct(UserRepository $userRepository, SerializerInterface $serialize, CompteRepository $compteRepository,
                                EntityManagerInterface $entityManager, ValidatorInterface $validator, TransactionsRepository $transactionsRepository,
            CommissionsRepository $commissionsRepository, TarifRepository $tarifRepository, ClientRepository $clientRepository, TokenStorageInterface $tokenStorage,
                            AgenceRepository $agenceRepository
    ){
        $this->userRepository = $userRepository;
        $this->serializer = $serialize;
        $this->compteReopsitory = $compteRepository;
        $this->manager = $entityManager;
        $this->validator =$validator;
        $this->transactionsRepository = $transactionsRepository;
        $this->commissionRepository = $commissionsRepository;
        $this->tarifRepository = $tarifRepository;
        $this->clientRepository = $clientRepository;
        $this->storage = $tokenStorage;
        $this->agenceRepository = $agenceRepository;
    }


    public function genereCode() {
        // genere code transaction
        $rand1 = rand(1, 99);  // choose number beetween 10-1000
        $rand2 = rand(1, 99);  // choose number beetween 1000-1000
        $date = new \DateTime('now');
        $genereCodeTransaction = $rand1.date_format($date, 'mdHis').$rand2;
        //  $genereCodeTransaction = str_shuffle($rand1.date_format($date, 'YmdHi').$rand2);
        return $genereCodeTransaction;
    }



    public function getTarif($montant) {
        $allTarifs = $this->tarifRepository->findAll();

        foreach($allTarifs as $value) {
            if($value->getBornInferieur() < $montant && $value->getBorneSuperieur() >= $montant) {
                return $value->getFraisEnvoie() ;
            }
        }
    }



    public function getCommissions() {
        $coms = $this->commissionRepository->findAll();
        foreach($coms as $value) {

                return $value ;

        }
    }
    public function getFrais($montant){
        // $data =[];
        //$frais=  $this->CalcFrais($montant);
        $datas = $this->tarifRepository->findAll();
        $frais = 0;
        $data =[];
        foreach ($datas as $value){
            if($montant>=2000000){
                $frais = ($value->getFraisEnvoie()*$montant)/100;
            }else{
                switch($montant){
                    case $montant>= $value->getBornInferieur() && $montant<$value->getBornInferieur():
                        $frais = $value->getFraisEnvoie();
                        break;
                }
            }


        }
        $data['frais'] = $frais;
        $data['montantSend'] = $montant - $frais;
        return $data;
        // 500 -> 425 = 925
    }

//    public function genereCode() {
//        // genere code transaction
//        $rand1 = rand(1, 100);  // choose number beetween 10-1000
//        $rand2 = rand(100, 1000);  // choose number beetween 1000-1000
//        $date = new \DateTime('now');
//        $genereCodeTransaction = str_shuffle($rand1.date_format($date, 'YmdHi').$rand2);
//        return $genereCodeTransaction;
//    }
//








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
     * @return Response
     */
    public function transfertClient(Request $request, Security $security): Response
    {
        // $typeTransaction = array('envoi','retrait');
        // recup tous les donnees
        $data = json_decode($request->getContent());

        //dd($this->getUser());
     $depot = $security->getUser();
      $this->userRepository->findOneBy(['id' => $depot->getId()]);
    //dd($depot);
        $agence = $depot->getAgence()->getId();
        //dd($agence);

        $compteEnvoi = $this->compteReopsitory->findBy(['agence' =>$agence])[0];
        //dd($compteDepot);

        $montantEnvoi = $data->montant;

        if ($montantEnvoi < 0) {
            return new JsonResponse('Le montant doit etre supérieur à 0', 400);
        }
        if ($compteEnvoi->getSolde() < $montantEnvoi){
            return new JsonResponse("Votre solde ne vous permet de faire cette action",400);
        }
//        $dataPostman->user;$frais = $this->getTarif($data["montant"]);
//        $montantReelEnvoye  = $data["montant"] - $frais;
//        //dd($montantReelEnvoye);
//        $etat = $frais*0.4;
//        $system = $frais*0.3;
//        $fraisDepot = $frais*0.1;
//        $fraisRetrait = $frais*0.2;

        if ($montantEnvoi < 2000000){
            $fraisEnvoie = $this->getTarif($montantEnvoi);
            $montant = $montantEnvoi - $fraisEnvoie;
        }elseif ($montantEnvoi >= 2000000){
            $fraisEnvoie = $montantEnvoi * 0.2;
            $montant = $montantEnvoi - $fraisEnvoie;
        }

        //dd($this->getCommissions());
        $comEtat = ($this->getCommissions()->getFraisEtat()) / 100;
        //dd($comEtat);
        $comSystem = ($this->getCommissions()->getFraisSystem()) / 100;
        $comEnvoie = ($this->getCommissions()->getFraisenvoie()) / 100;
        $comRetrit = ($this->getCommissions()->getFraisRetrait()) / 100;
//dd($comRetrit);

        $fraisEtat = $fraisEnvoie * $comEtat;
        $fraisSystem = $fraisEnvoie * $comSystem;
        $fraisEnvoi = $fraisEnvoie * $comEnvoie;
        $fraisRetraits = $fraisEnvoie * $comRetrit;

        $compteEnvoi->setSolde(($compteEnvoi->getSolde() - $montantEnvoi + $fraisEnvoie));

        $genereCodeTransaction = $this->genereCode();

        $clientEnv = new Client();
        $codeTransaction = rand(100,999)*243;
        $clientEnv->setMontantEnvoyer($montant)
            ->setPhone($data->phoneEnvoi)
            ->setPrenom($data->prenomEnvoi)
            ->setNom($data->nomEnvoi)
            ->setCodeTransaction($genereCodeTransaction)
            ->setAction("depot");
            //->setcni($dataPostman["cni"]);
        $this->manager->persist($clientEnv);


        $clientRecv = new Client();
        $clientRecv->setMontantEnvoyer($montant)
           ->setPhone($data->phoneRec)
            ->setNom($data->nomRec)
            ->setPrenom($data->prenomRec)
            ->setCodeTransaction($genereCodeTransaction)
            ->setcni($data->cni);
            $this->manager->persist($clientRecv);

//        $cpte = $this->compteReopsitory->find($dataPostman["compte"]);
//        $dept = $cpte->setSolde(($cpte->getSolde() - ($dataPostman["montant"]) + $frais)) ;







        $depot = new Transactions();
        $depot->setDateDepot(new \DateTime())
            ->setMontant($montant)
            ->setUserDepot($security->getUser())
            ->setTtc(100)
            ->setFraisEtat($fraisEtat)
            ->setFraisSystem($fraisSystem)
            ->setFraisEnvoie($fraisEnvoi)
            ->setFraisRetrait($fraisRetraits)
            ->setCodeTransaction($genereCodeTransaction)
            ->setClientDepot($clientEnv)
            ->setClientRetrait($clientRecv)
            ->setCompteEnvoie($compteEnvoi)
            ->setType("EnCours")
            ->setStatus(false);

        //   ->getCompte()->setSolde($cpte->getSolde() - $depot->getMontant());
        //    + $depot->solde

        //    dd(($depot->getCompte()));



        $this->manager->persist($depot);

        $this->manager->flush();

        return $this->json("success",201);


    }




    /**
     * @Route(
     *      name="calculFrais" ,
     *      path="/api/decalculer",
     *     methods={"POST"} ,
     *
     *)
     * @param Request $request
     * @return Response
     */

    public function retunFrais(Request $request) {
        $montantPostman =  json_decode($request->getContent());
        //$montant = $this->serializer->denormalize()
        if($montantPostman->montant < 0) {
            return $this->json("le montant ne peut pas être négatif!", 400);
        } else if(!is_numeric($montantPostman->montant)) {
            return $this->json("Vous devez founir un nombre valide, non une chaine de caractère!", 400);
        } else if($montantPostman->montant > 2000000) {
            $frais = ((int)($montantPostman->montant)) * 0.02;
            return $this->json($frais, 200);
        }

        $frais  = $this->getTarif((int)($montantPostman->montant));
        //$array = json_decode($frais, true);
        return $this->json($frais, 200);
    }

    /**
     * @Route(
     *      name="reucuperTransaction" ,
     *      path="/api/recupTransaction/{code}" ,
     *     methods={"GET"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::reucuperTransaction",
     *         "_api_resource_class"=Transactions::class ,
     *         "_api_collection_operation_name"="reucuperTransaction"
     *     }
     *)
     */
    public function reucuperTransaction(Request $request, $code, Security $security)
    {

        $RetraitRansaction =  $this->transactionsRepository->findTransactionByCode($code) ;

        if($RetraitRansaction) {

            if($RetraitRansaction->getType() == "Reussie") {
                return $this->json("Cette transaction est déjà retirée ", 400);
            } else if($RetraitRansaction->getType() == "Annulee"){
                return $this->json("Cette transaction a étè annulée ", 400);
            } else {
                // data given on postman
                //$dataPostman =  json_decode($request->getContent());
               // $idCompteCaissierGiven = $dataPostman->compte;
                $userRetrait = $security->getUser();
                $this->userRepository->findOneBy(['id' => $userRetrait->getId()]);
                $agence = $userRetrait->getAgence()->getId();
                //dd($agence);

                $compte = $this->compteReopsitory->findBy(['id' => $agence])[0];
                ///dd($compte);
                $compte->setSolde($compte->getSolde() +$RetraitRansaction->getMontant() + $RetraitRansaction->getFraisRetrait());
                $time = new \DateTime();
                $RetraitRansaction->setDateRetrait($time);
                $RetraitRansaction->setType("Reussie");
                $RetraitRansaction->setCompteRetrait($compte);
                $RetraitRansaction->setUserRetrait($security->getUser());
                $this->manager->persist($RetraitRansaction);

//                $compte =  $this->competeReopsitory->findOneBy(['id'=>(int)$idCompteCaissierGiven]);
//                //dd($compte);
//                $compte->setSolde($compte->getSolde() +$RetraitRansaction->getMontant() + $RetraitRansaction->getFraisRetrait());
//                $this->manager->persist($compte);
//                //  dd($compteFocus);
//
//                //update client received
                $clientReceiver = $this->clientRepository->find($RetraitRansaction->getClientDepot()->getId());
                $clientReceiver->setMontantEnvoyer($RetraitRansaction->getMontant());
                $clientReceiver->setAction("retrait");
                $this->manager->persist($clientReceiver);

                // summarize transaction
//                $summarizeTransaction = new ResumeTransaction();
//                $summarizeTransaction->setMontant($RetraitRansaction->getMontant());
//                $summarizeTransaction->setCompte($idCompteCaissierGiven);
//                $summarizeTransaction->setType("retrait");
//                $this->manager->persist($summarizeTransaction);

                $this->manager->flush();
                return $this->json("retrait reussit", 201);
            }

        } else {
            return $this->json("Ce code n'est pas valide", 400);
        }

    }


    /**
     * @Route("/api/transaction/{code}", name="addTransaction", methods={"GET"})
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param $code
     * @return JsonResponse
     */
    public function getTransactionByCode(Request $request, SerializerInterface $serializer, $code )
    {
        $data = array();

        $transaction =  $this->transactionsRepository->findTransactionByCode($code) ;
//dd($transaction);
        if($transaction) {

            $recuperator = $this->clientRepository->findOneBy(["id"=>$transaction->getClientDepot()->getId()]);
            if($recuperator){
            $envoyer = $this->clientRepository->findOneBy(["id"=>$transaction->getClientRetrait()->getId()]);
            //dd($envoyer);
            foreach($envoyer as $key=>$env ) {
                foreach($recuperator as $recup) {
                    array_push($data, $transaction, $env, $recup );
                }
            }
            return $this->json($data , 200);
             }
            else {
                $deposer = $this->clientRepository->findById($transaction->getCompteEnvoie()->getId());
                $retrait = $this->clientRepository->findById($transaction->getClientRetrait()->getId());

                foreach($deposer as $dep) {
                    foreach($retrait as $ret) {
                        array_push($data, $transaction, $dep, $ret);
                    }
                }
                return $this->json($data , 200);
            }

        } else {
            return $this->json("Ce code n'est pas valide", 400);
        }

    }
}
