<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    /**
     * 
     * @Route("/api/client/{id<\d+>}/commands", name="api_client_commands", methods={"GET"})
     */
    public function getClientIdCommands(UserRepository $userRepository, $id): Response
    {
        $commands=$userRepository->findByClientIdCommands($id);
       
        return $this->json(
            $commands,
            Response::HTTP_OK,
            [],
            [
                'groups' => [ 'client_commands']
            ]);
    }
    /**
     * 
     * @Route("/api/client/{id<\d+>}", name="api_client_get", methods={"GET"})
     */
    public function getClientId(UserRepository $userRepository, $id){
        $client=$userRepository->findByClientId($id);
       
        return $this->json(
            $client,
            Response::HTTP_OK,
            [],
            [
                'groups' => [ 'client_id']
            ]);

    }
     /**
     * 
     * @Route("/api/client/{id<\d+>}/info", name="api_client_info", methods={"GET"})
     */
    public function getItemClient(User $user = null): Response
    {
        // 404 ?
        if ($user === null) {
            return $this->json(['error' => 'Client non trouvé.'], Response::HTTP_NOT_FOUND);
        }

       
        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            [
                'groups' => [ 'user_info']
            ]);
    }
     /**
     * 
     * @Route("/api/login_check", name="api_login_check", methods={"GET"})
     */

     /**
     * 
     * @Route("/api/client/{id<\d+>}", name="api_client_post", methods={"POST"})
     */
    public function postClientId(Request $request, SerializerInterface $serializer, ManagerRegistry $doctrine)
    {
        //Récuperer le contenu JSON
        $jsonContent=$request->getContent();

        //Désérialiser (convertir) le JSON en entité Doctrine User
        $user = $serializer->deserialize($jsonContent, User::class, 'json');

        //Validé l'entité

        //On sauvegarde l'entité
        $entityManager = $doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // On retourne la réponse adapté (200, 204 ou 404)
        //dd($user);
        return $this->json(
            //Le client modifié peut etre ajouté en retour
            $user,
            //Le status code : 200 OK
            //utilisation des constantes de classes
            Response::HTTP_OK,
            
            //Groups
            ['groups' => 'client_id']
        );
    }
}
