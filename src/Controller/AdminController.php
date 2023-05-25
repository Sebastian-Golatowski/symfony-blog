<?php

namespace App\Controller;


use App\Entity\Post;
use App\Entity\Report;
use App\Entity\User;
use App\Form\AdminUsersType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{
    private $postRepository;
    private $userRepository;
    private $reportRepository;
    private $paginator;
    private $em;

    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator)
    {   
        $this->postRepository = $em->getRepository(Post::class);
        $this->userRepository = $em->getRepository(User::class);
        $this->reportRepository = $em->getRepository(Report::class);
        $this->paginator = $paginator;
        $this->em = $em;
    }

    #[Route('/', name: 'users')]
    public function showUsers(Request $req): Response
    {
        $userId= $this->getUser()->getId();
        $form = $this->createForm(AdminUsersType::class);

        $page = $req->query->getInt('page', 1);
        $perPage = 15;
        
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $userName = $form->get('userName')->getData();
            $users = $this->userRepository->likeUserName($userName);
            $users = array_filter($users, static function ($user) use($userId) {
                return $user->getId() !== $userId;
            });
            $perPage = sizeof($users)+1;
        }
        else{
            $users = $this->userRepository->findAll();
            $users = array_filter($users, static function ($user) use($userId) {
                return $user->getId() !== $userId;
            });
            $userName="";
        }

        
        $paginatedUsers = $this->paginator->paginate(
        $users,
        $page, // Current page number
        $perPage // Number of items per page
        );

        return $this->render('admin/index.html.twig', [
            'users' => $paginatedUsers,
            'form' => $form->createView(),
            'userName' => $userName
        ]);

    }

    #[Route('/reports', name:'reports')]
    public function showReports(Request $req):Response
    {

        $reports = $this->reportRepository->countReports(1);
        
        $page = $req->query->getInt('page', 1);
        $paginatedReports = $this->paginator->paginate(
            $reports,
            $page, // Current page number
            20 // Number of items per page
            );
        return $this->render('admin/reports.html.twig',[
            'reports' => $paginatedReports
        ]);
    }

    #[Route('/deleteAccount', name:"deleteAccount",methods: ['POST'])]
    public function deleteAccount(Request $req): JsonResponse
    {
        $payload = json_decode($req->getContent(), false);
        $userId = $payload->user;

        if ($this->isGranted("ROLE_ADMIN")) {
            $user = $this->userRepository->find($userId);
            $posts = $this->postRepository->findBy(['user'=>$userId]);
            foreach($posts as $post){
                $this->postRepository->remove($post,true);
            }
            $this->userRepository->remove($user, true);

            return $this->json("user deleted",200);
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
}
