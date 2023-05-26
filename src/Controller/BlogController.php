<?php
namespace App\Controller;

use App\Form\PostFormType;
use App\Entity\Post;
use App\Entity\Report;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/blog', name: 'blog_')]
class BlogController extends AbstractController
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
    
    #[Route('/', name: 'index')]
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

    #[Route('/delete/{id}', name: 'delete')]
    public function delete($id): Response
    {
        $user = $this->getUser();
        $post = $this->postRepository->find($id);
        if ($user->getId() == $post->getUser()->getId() or $this->isGranted('ROLE_ADMIN')) {
            $fs = new Filesystem();
            $fs->remove($this->
                getParameter('kernel.project_dir').'/public'.$post->getImg());
                
            $this->postRepository->remove($post,true);
        }
        
        return $this->redirectToRoute("blog_index");
        
    }
    #[Route('/create', name: 'create')]
    public function create(Request $req): Response
    {
        $user = $this->getUser();
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
    #[Route('/edit/{id}/{origin}',name:'edit')]
    public function edit($id, $origin,Request $req){
        $post = $this->postRepository->find($id);
        
        $user = $this->getUser();

        if ($user->getId() == $post->getUser()->getId()) {
            $form = $this->createForm(PostFormType::class, $post,['required'=>false]);
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

                    $image = '/images/'.$newName;
                }
                
                $post->setTitle($form->get('title')->getData());
                $post->setText($form->get('text')->getData());
                $post->setImg($image);
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

        return $this->redirectToRoute('blog_'.$origin,['id'=>$id]);

    }

    
    #[Route('/{id}', name: 'show')]
    public function show($id): Response
    {
        $post = $this->postRepository->find($id);
        return $this->render('blog/show.html.twig',['post'=>$post]);
    }

    

}
