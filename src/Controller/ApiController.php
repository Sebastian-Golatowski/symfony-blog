<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Report;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    private $postRepository;
    private $userRepository;
    private $reportRepository;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {   
        $this->postRepository = $em->getRepository(Post::class);
        $this->userRepository = $em->getRepository(User::class);
        $this->reportRepository = $em->getRepository(Report::class);
        $this->em = $em;
    }

    #[Route('/deleteAccount', name:"deleteAccount",methods: ['POST'])]
    public function deleteAccount(Request $req): JsonResponse
    {
        $payload = json_decode($req->getContent(), false);
        $userId = $payload->user;

        if ($this->isGranted("ROLE_ADMIN")) {
            $user = $this->userRepository->find($userId);
            $posts = $user->getPosts();
            foreach($posts as $post){
                $this->postRepository->remove($post,true);
            }
            $this->userRepository->remove($user, true);

            return $this->json("user deleted",200);
        }
        
        return $this->json("u cant do that",201);
    } 

    #[Route('/deletePost', name:"deletePost",methods: ['POST'])]
    public function deletePost(Request $req): JsonResponse
    {
        $payload = json_decode($req->getContent(), false);
        $postId = $payload->post;
        $owner = $payload->owner;

        $post = $this->postRepository->find($postId);

        if ($this->isGranted("ROLE_ADMIN")) {
            if($owner == 'no'){
                $this->postRepository->remove($post,true);
                return $this->json("post deleted",200);
            }
            elseif($owner == 'yes'){
                $user = $post->getUser();
                $posts = $user->getPosts();
                
                foreach($posts as $post){
                    $this->postRepository->remove($post,true);
                }
                $this->userRepository->remove($user, true);
                return $this->json("post and user deleted",200);
            }
        }
        
        return $this->json("u cant do that",201);
    } 

    #[Route('/changeStatus', name:"changeStatus",methods: ['POST'])]
    public function changeStatus(Request $req): JsonResponse
    {
        $payload = json_decode($req->getContent(), false);
        $userId = $payload->user;
        $toWhat = $payload->towhat;

        if ($this->isGranted("ROLE_ADMIN")) {
            $user = $this->userRepository->find($userId);
            if ($user) {
                if($toWhat == 'admin'){
                    $user->setRoles(["ROLE_ADMIN"]);
                    $this->em->flush();
                }
                elseif($toWhat == 'user'){
                    $user->setRoles([]);
                    $this->em->flush();
                }
            }
            return $this->json("ok", 200);
        }
    }

    #[Route('/allowIt', name:"allowIt",methods: ['POST'])]
    public function allowIt(Request $req): JsonResponse
    {
        $payload = json_decode($req->getContent(), false);
        $postId = $payload->post;

        if ($this->isGranted("ROLE_ADMIN")) {
            $post = $this->postRepository->find($postId);
            $this->postRepository->remove($post,true);

            return $this->json("ok", 200);
        }
    }

    #[Route('/report', name: 'report',methods: ['POST'])]
    public function report(Request $req): Response
    {
        $payload = json_decode($req->getContent(), false);
        // $userId = $payload->user;
        $postId = $payload->post;

        $user = $this->getUser();
        $post = $this->postRepository->find($postId);

        $reports = $this->reportRepository->isAlreadyReported($user->getId(), $postId);

        if(sizeof($reports) == 0){
            $newReport = new Report();
            $newReport->setPost($post);
            $newReport->setUser($user);
            $this->em->persist($newReport);
            $this->em->flush();

            return $this->json("good",200);
        }

        return $this->json("already reported", 201);
    }
}
