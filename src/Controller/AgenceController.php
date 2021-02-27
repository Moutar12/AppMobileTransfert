<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Agence;
use App\Entity\Compte;
use App\Repository\AgenceRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AgenceController extends AbstractController
{

    /**
     * @var AgenceRepository
     */
    private $agenceRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var CompteRepository
     */
    private $compteRepository;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    private $solde;
    private $numCompte;

    public function __contruct(AgenceRepository $agenceRepository, CompteRepository $compteRepository, EntityManagerInterface $manager,
                               SerializerInterface $serializer, ValidatorInterface $validator
    ){
        $this->agenceRepository = $agenceRepository;
        $this->compteRepository = $compteRepository;
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }


    /**
     * @Route(
     *      name="addAgence" ,
     *      path="/api/add/agence",
     *     methods={"POST"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::addAgence",
     *         "_api_resource_class"=Agence::class ,
     *         "_api_collection_operation_name"="addAgence"
     *     }
     *)
     *
     */
    public function addAgence(Request $request): Response
    {
        $newAgence = json_decode($request->getContent(), true);
        dd($newAgence);
        $agence = new Agence();
        $agence->setNomAgence($newAgence['nomAgence']);
        $agence->setAdressAgence($newAgence['adressAgence']);
        $agence->setStatus(true);
//        $compte = new Compte();
//        $compte->setNumCompte($newAgence['numCompte']);
//        $compte->setSolde($newAgence['solde']);
        //dd($agence);
        $agence->getCompte()->setNumCompte($newAgence[]);
        $agence->getCompte()->setSolde($newAgence['solde']);
        dd($agence);

    }

}
