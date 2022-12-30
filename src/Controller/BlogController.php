<?php

namespace App\Controller;

use App\Form\PostFormType;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/blog', name: 'blog')]
class BlogController extends AbstractController
{
    private $postRepository;
    private $userRepository;
    private $paginator;
    private $em;

    public function __construct(EntityManagerInterface $em, PaginatorInterface $paginator)
    {   
        $this->postRepository = $em->getRepository(Post::class);
        $this->userRepository = $em->getRepository(User::class);
        $this->paginator = $paginator;
        $this->em = $em;
    }
    
    #[Route('/', name: '_index')]
    public function index(Request $req): Response
    {
        $page = $req->query->getInt('page', 1);
        $posts = $this->postRepository->findBy([],['id'=>'DESC']);

        // Paginate the articles
        $paginatedPosts = $this->paginator->paginate(
        $posts,
        $page, // Current page number
        3 // Number of items per page
        );

        // Render the template, passing the paginated articles as an argument
        return $this->render('blog/index.html.twig', [
            'posts' => $paginatedPosts,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete')]
    public function delete($id): Response
    {
        $post = $this->postRepository->find($id);
        $fs = new Filesystem();
                $fs->remove($this->
                getParameter('kernel.project_dir').'/public'.$post->getImg());
        $this->em->remove($post);
        $this->em->flush();
        
        return $this->redirectToRoute("blog_index");
    }
    #[Route('/create', name: '_create')]
    public function create(Request $req): Response
    {
        $user = $this->userRepository->find(6);
        $post = new Post();
        $form = $this->createForm(PostFormType::class,$post,['required'=>true]);
        
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $newPost = $form ->getData();
            $image = $form->get('img')->getData();

            $newName = uniqid().'.'.$image->guessExtension();

            try{
                $image->move($this->getParameter('kernel.project_dir').'/public/images', $newName);
            } catch (FileException $e){
                return new Response($e ->getMessage());
            }

            $newPost->setImg('/images/'.$newName);

            $newPost->setUser($user);
            
            $this->em->persist($newPost);
            $this->em->flush();

            return $this->redirectToRoute("blog_index");

        }
        return $this->render('blog/create.html.twig',[
            'form'=>$form->createView()
        ]);
        
    }
    #[Route('/edit/{id}/{origin}',name:'_edit')]
    public function edit($id, $origin,Request $req){
        $post = $this->postRepository->find($id);
        $form = $this->createForm(PostFormType::class, $post,['required'=>false]);

        $user = $this->userRepository->find(6);

        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){

            $image = $post -> getImg();
            $imageForm = $form->get('img')->getData();
            if($imageForm){

                $fs = new Filesystem();
                $fs->remove($this->
                getParameter('kernel.project_dir').'/public'.$image);
                
                $image = $imageForm;

                $newName = uniqid().'.'.$image->guessExtension();
                try{
                    $image->move($this->getParameter('kernel.project_dir').'/public/images', $newName);
                } catch (FileException $e){
                    return new Response($e ->getMessage());
                }
            }
            
            $post->setTitle($form->get('title')->getData());
            $post->setText($form->get('text')->getData());
            $post->setImg('/images/'.$newName);
            $post->setUser($user);
            
            $this->em->persist($post);
            $this->em->flush();


            
            return $this->redirectToRoute('blog_'.$origin,['id'=>$id]);

        }

        return $this->render('blog/edit.html.twig',[
            'form'=>$form->createView(),
            'post'=>$post,
        ]);

    }
    
    #[Route('/{id}', name: '_show')]
    public function show($id): Response
    {
        $post = $this->postRepository->find($id);
        return $this->render('blog/show.html.twig',['post'=>$post]);
    }


}
