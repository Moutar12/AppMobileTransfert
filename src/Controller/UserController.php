<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Profil;
use App\Entity\User;
use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{

    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var ProfilRepository
     */
    private $profilRepository;

    public function __construct(SerializerInterface $serializer, UserRepository $userRepository, EntityManagerInterface $manager,
                                ValidatorInterface $validator, UserPasswordEncoderInterface $encoder, ProfilRepository $profilRepository
    ){
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->validator = $validator;
        $this->encoder = $encoder;
        $this->profilRepository = $profilRepository;
    }

    /**
     *
     *     @Route("/api/add/users", name="addUser",methods={"POST"})
     *
     *
     *
     */
    public function addUser(Request $request): Response
    {
        $user = $request->request->all();

       $profil = $user['profil'];

        $newUser = new User();

        $uploadedFile = $request->files->get('avatar');
        if($uploadedFile){
            $file = $uploadedFile->getRealPath();
            $photo = fopen($file, 'r+');
            $user['avatar'] = $photo;
        }

        $errors = $this->validator->validate($user);
        if ($errors){
            $errors = $this->serializer->serialize($errors,"json");
            return new JsonResponse($errors, Response::HTTP_BAD_REQUEST,[],true);
        }

        $newUser->setUsername($user['username']);
        $newUser->setCni($user['cni']);
        $newUser->setPhone($user['phone']);
        $newUser->setAdresse($user['adresse']);
        $newUser->setNom($user['nom']);
        $newUser->setPrenom($user['prenom']);
        $newUser->setStatus(false);
        $newUser->setAvatar($user['avatar']);
        $newUser->setPassword($this->encoder->encodePassword($newUser, $user['password']));
        $newUser->setProfil($this->profilRepository->findOneBy(["libelle" => $profil]));

        $this->manager->persist($newUser);
        $this->manager->flush();

        return $this->json("Success", 200);
    }
}
