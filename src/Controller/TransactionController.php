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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;
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




    public function getTarif($montant) {
        $allTarifs = $this->tarifRepository->findAll();

        foreach($allTarifs as $value) {
            if($value->getBornInferieur() < $montant && $value->getBorneSuperieur() >= $montant) {
                return $value->getFraisEnvoie() ;
            }
        }
    }



    public function getCommissions() {
        $coms = $this->commissionsRepository->findAll();
        foreach($coms as $value) {
            if($value->getActive() == true && $value->getArchivage() == false) {
                return $value ;
            }
        }
    }

    public function genereCode() {
        // genere code transaction
        $rand1 = rand(1, 100);  // choose number beetween 10-1000
        $rand2 = rand(100, 1000);  // choose number beetween 1000-1000
        $date = new \DateTime('now');
        $genereCodeTransaction = str_shuffle($rand1.date_format($date, 'YmdHi').$rand2);
        return $genereCodeTransaction;
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
     * @return Response
     */
    public function transfertClient(Request $request, Security $security): Response
    {
        // $typeTransaction = array('envoi','retrait');
        // recup tous les donnees
        $dataPostman = json_decode($request->getContent(), true);

        $depot = $this->serializer->denormalize($dataPostman, Transactions::class);

        $frais = $this->getTarif($dataPostman["montant"]);
        $montantReelEnvoye  = $dataPostman["montant"] - $frais;
        $etat = $frais*0.4;
        $system = $frais*0.3;
        $fraisDepot = $frais*0.1;
        $fraisRetrait = $frais*0.2;

        $clientEnv = new Client();
        $codeTransaction = rand(100,999)*243;
        $clientEnv->setMontantEnvoyer($montantReelEnvoye)
            ->setnomComplet($dataPostman['nomCompletEnvoi'])
            ->setPhone($dataPostman['phoneEnvoi'])
            ->setCodeTransaction($codeTransaction)
            ->setAction("depot")
            ->setcni($dataPostman["cni"]);
        $this->manager->persist($clientEnv);


        $clientRecv = new Client();
        $clientRecv->setMontantEnvoyer($montantReelEnvoye)
            ->setnomComplet($dataPostman['nomCompletRec'])
            ->setPhone($dataPostman['phoneRec'])
            ->setCodeTransaction($codeTransaction)
            ->setcni($dataPostman["cni"]);
            $this->manager->persist($clientRecv);


        //   dd($clientRecv);


        // On recupere le montant
        // $montant = $this->compterepository->find($dataPostman["montant"]);
        // dd($montant);

        // On recupere l'id compte
        $cpte = $this->compteReopsitory->find($dataPostman["comptes"]);
        // dd($cpte);
        // Deduction du compte cad lors d'un depot cad actualisation
        $dept = $cpte->setSolde(($cpte->getSolde() - ($dataPostman["montant"])) + $fraisDepot) ;


        // dd($dept);


        //     // Ajout du compte cad lors d'unretrait
        // $dept = $cpte->setSolde($cpte->getSolde() + $depot->getMontant());



        //($cpte->setSolde($cpte->getSolde() - $depot->getMontant()));

        // genere code transaction
        $numBeetween = rand(1, 10);  // choose number beetween 100-1000
        $date = new \DateTime('now');



        $depot->setDateDepot(new \DateTime())
            ->setdateRetrait(new \DateTime())
            ->setdateAnnulation(new \DateTime())
            ->setUserDepot($security->getUser())
            ->setTtc(100)
            ->setFraisEtat($etat)
            ->setFraisSystem($system)
            ->setFraisEnvoie($fraisDepot)
            ->setFraisRetrait($fraisRetrait)
            ->setCodeTransaction($codeTransaction)
            ->setClientDepot($clientEnv)
            ->setClientRetrait($clientRecv)
            ->setType("EnCours")
            ->setStatus(false);

        //   ->getCompte()->setSolde($cpte->getSolde() - $depot->getMontant());
        //    + $depot->solde

        //    dd(($depot->getCompte()));



        $this->manager->persist($depot);
        $this->manager->persist($dept);

        $this->manager->flush();

        return $this->json("success",201);


    }

    /**
     * @Route(
     *      name="reucuperTransaction" ,
     *      path="/api/recupTransaction/{code}" ,
     *     methods={"PUT"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::reucuperTransaction",
     *         "_api_resource_class"=Transactions::class ,
     *         "_api_collection_operation_name"="reucuperTransaction"
     *     }
     *)
     */
    public function reucuperTransaction(Request $request, $code)
    {

        $RetraitRansaction =  $this->transactionsRepository->findTransactionByCode($code) ;

        if($RetraitRansaction) {

            if($RetraitRansaction->getType() == "Reussie") {
                return $this->json("Cette transaction est déjà retirée ", 400);
            } else if($RetraitRansaction->getType() == "Annulee"){
                return $this->json("Cette transaction a étè annulée ", 400);
            } else {
                // data given on postman
                $dataPostman =  json_decode($request->getContent());
                $idCompteCaissierGiven = $dataPostman->compte;

                $time = new \DateTime();
                $RetraitRansaction->setDateRetrait($time);
                $RetraitRansaction->setType("Reussie");
                $RetraitRansaction->setCompteRetrait($this->competeReopsitory->findOneBy(['id'=>(int)$idCompteCaissierGiven]));
                $this->manager->persist($RetraitRansaction);
                // dd($transactionDo);

                $compte =  $this->competeReopsitory->findOneBy(['id'=>(int)$idCompteCaissierGiven]);
                $compte->setSolde($compte->getSolde() +$RetraitRansaction->getMontant() + $RetraitRansaction->getFraisRetrait());
                $this->manager->persist($compte);
                //  dd($compteFocus);

                //update client received
                $clientReceiver = $this->clientRepository->find($RetraitRansaction->getClientDepot()->getId());
                $clientReceiver->setMontantEnvoyer($RetraitRansaction->getMontant());
                $clientReceiver->setAction("retrait");
                $this->manager->persist($clientReceiver);

                // summarize transaction
                $summarizeTransaction = new ResumeTransaction();
                $summarizeTransaction->setMontant($RetraitRansaction->getMontant());
                $summarizeTransaction->setCompte($idCompteCaissierGiven);
                $summarizeTransaction->setType("retrait");
                $this->manager->persist($summarizeTransaction);

                $this->manager->flush();
                return $this->json("retrait reussit", 201);
            }

        } else {
            return $this->json("Ce code n'est pas valide", 400);
        }

    }


    /**
     * @Route(
     *      name="getTransactionByCode" ,
     *      path="/api/transaction/{code}" ,
     *     methods={"GET"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::getTransactionByCode",
     *         "_api_resource_class"=Transactions::class ,
     *         "_api_collection_operation_name"="getTransactionByCode"
     *     }
     *)
     */
    public function getTransactionByCode(Request $request, SerializerInterface $serializer, $code)
    {
        $data = array();

        $transaction =  $this->transactionsRepository->findTransactionByCode($code) ;

        if($transaction) {

            $recuperator = $this->clientRepository->findById($transaction->getClientRetrait()->getId());
            // transaction client
           // dd($recuperator);
            if($recuperator) {
                $envoyer = $this->clientRepository->findById($transaction->getClientDepot()->getId());
                // browser data
                //dd($envoyer);
                foreach($envoyer as $env ) {
                    foreach($recuperator as $recup) {
                        array_push($data, $transaction, $env, $recup );
                        //dd(array_push());
                        //dd($data);
                    }
                }
                return $this->json($data , 200);
            } else {
                //dd($data);
                 $deposer = $this->clientRepository->findById($transaction->getClientDepot()->getId());
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
