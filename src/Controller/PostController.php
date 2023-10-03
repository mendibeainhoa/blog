<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
class PostController extends AbstractController
{
    private $em;
    /**
     * @param $em
     */
    public function __construct(EntityManagerInterface $em){
        $this->em =$em;
    }

    //CONSULTAS CON ENTITY A LA BBDD
    // #[Route('/post/{id}', name: 'postDetails')]
    // public function index($id): Response
    // {
    //      $posts = $this->em->getRepository(Post::class)->findAll(); //recupera todos los post que se hallan realizado
    //      return $this->render('post/index.html.twig', [
    //          'posts'=> $posts
    //      ]);

        // $post = $this->em->getRepository(Post::class)->find($id); //recupera el post que se pasa como id por la url
        // return $this->render('post/index.html.twig', [
        //     'post'=> $post
        // ]); 

        // $post = $this->em->getRepository(Post::class)->findBy(['id'=>1, 'title'=>'Mi primer post de prueba']); //recupera todos los post con este criterio de busqueda
        // return $this->render('post/index.html.twig', [
        //     'post'=> $post,
        // ]);

        // $post = $this->em->getRepository(Post::class)->findOneBy(['id'=>1]); //recupera un elementode los post con este criterio de busqueda
        // return $this->render('post/index.html.twig', [
        //     'post'=> $post,
        // ]);
        
    // }


    //INSERTAR EN LA BBDD CON Entity
    // #[Route('/insert/post', name: 'insert_post')]

    // public function insert (){
    //     $post = new Post();
    //     $user = $this->em->getRepository(User::class)->find(id:1);
    //     $post->setTitle('Nuevo post insertado')
    //     ->setDescription('Vaya he conseguido añadirlo a la bbdd')
    //     ->setCreationDate(new \DateTime())
    //     ->setUrl('mipaginamola.com')
    //     ->setFile('hola.exe')
    //     ->setType('Opinion')
    //     ->setUser($user);
    //     $this->em->persist($post);
    //     $this->em->flush();
    //     return new JsonResponse(['success'=>true]);
    // }

    //FORMULARIO DE LOS POST DEL BLOG
    #[Route('/', name: 'app_post')]
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $posts =$this->em->getRepository(Post::class)->findAllPosts(); //trae todos los posts de la bbdd

        $form =$this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){ //en caso de que sea valido 
            
            $post->setCreationDate(new \DateTime()); //adquiere la fecha de creación del post
           
            $url = str_replace(' ', '-', $form->get('title')->getData()); // adquiere el titulo del post y se lo añade a la url
            $post->setUrl($url);
            
            $file = $form->get('file')->getData();
            if($file){ //si existe el archivo
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); //adquiere el nombre original del archivo
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();//utiliza la libreria para cambiarle el nombre al archivo por seguridad
                try {
                    $file->move(
                        $this->getParameter('files_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Ups, ha habido un problema en la carga de los archivos');
                }
                $post->setFile($newFilename);

            }

            $user=$this->em->getRepository(User::class)->find(1);
            $post->setUser($user);
            $this->em->persist($post);
            $this->em->flush();
            return $this->redirectToRoute('app_post');
        }
         return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
         ]);

    }
    #[Route('/post/{id}', name: 'postDetails')]
     public function postDetails(Post $post){
        return $this-> render('post/post-details.html.twig', ['post' => $post]);
     }
     
     
}
