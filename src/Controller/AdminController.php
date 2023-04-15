<?php

namespace App\Controller;


use App\Entity\Post;
use App\Entity\Report;
use App\Entity\User;
use App\Form\AdminUsersType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $form = $this->createForm(AdminUsersType::class);

        $page = $req->query->getInt('page', 1);
        $perPage = 15;
        
        $form->handleRequest($req);
        if($form->isSubmitted()){
            $userName = $form->get('userName')->getData();
            $users = $this->userRepository->likeUserName($userName);
            $perPage = sizeof($users)+1;
        }
        else{
            $users = $this->userRepository->findAll();
            $userName="";
        }

        // Paginate the articles
        $paginatedUsers = $this->paginator->paginate(
        $users,
        $page, // Current page number
        $perPage // Number of items per page
        );

        // Render the template, passing the paginated articles as an argument
        return $this->render('admin/index.html.twig', [
            'users' => $paginatedUsers,
            'form' => $form->createView(),
            'userName' => $userName
        ]);

    }
}
