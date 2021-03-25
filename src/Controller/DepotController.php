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
                   AgenceRepository $agenceRepository,  EntityManagerInterface $manager , DepotRepository  $depotRepository

    ){
        $this->compteRepository = $compteRepository;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->manager = $manager;
        $this->depotRepository = $depotRepository;
        $this->agenceRepository = $agenceRepository;

    }

    /**
     * @Route(
     *      name="caisseDepot" ,
     *      path="/api/caissier/depot",
     *     methods={"POST"} ,
     *     defaults={
     *         "__controller"="App\Controller\DepotController::caisseDepot",
     *         "_api_resource_class"=Depot::class ,
     *         "_api_collection_operation_name"="caisseDepot"
     *     }
     *)
     *
     */
    public function caisseDepot(Request $request, Security $security): Response
    {
        $infos = json_decode($request->getContent(),true);
        $depot = $this->serializer->denormalize($infos, Depot::class);
        //dd($depot);
        $user = $security->getUser();
        //dd($user);
        if(isset($infos['comptes'])){
            $compte = $this->compteRepository->findOneBy(['id' =>$infos['comptes']]);
            //dd($compte);
        }
        if($infos['montant']> 0){
           // dd($infos['montant']);
            $compte->setSolde($compte->getSolde() + $infos['montant']);

        }else{
            return new JsonResponse("le montant doit etre superieiur à 0",400,[],true);
        }

        $depot->setDateDepot(new \DateTime('now'));
        $depot->setUser($user);
        $depot->setCompte($compte);
        $this->manager->persist($depot);
        $this->manager->flush();
        return $this->json(['message' => 'le depot a été effectuer avec success ', 'data'=>$depot]);

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
