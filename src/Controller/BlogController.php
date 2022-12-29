<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/blog', name: 'blog')]
class BlogController extends AbstractController
{
    private $postRepository;
    private $userRepository;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {   
        $this->postRepository = $em->getRepository(Post::class);
        $this->userRepository = $em->getRepository(User::class);
        $this->em = $em;
    }
    
    #[Route('/', name: '_index')]
    public function index(): Response
    {
        $posts = $this->postRepository->findAll();
        return $this->render('blog/index.html.twig',['posts'=>$posts]);
    }

    #[Route('/{id}', name: '_show')]
    public function show($id): Response
    {
        $post = $this->postRepository->find($id);
        return $this->render('blog/show.html.twig',['post'=>$post]);
    }

    #[Route('/delete/{id}', name: '_delete')]
    public function delete($id): Response
    {
        $post = $this->postRepository->find($id);
        $this->em->remove($post);
        $this->em->flush();
        
        return $this->redirectToRoute("blog_index");
    }

}
