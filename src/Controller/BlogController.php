<?php

namespace App\Controller;

use App\Entity\BlogPost;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    /**
     * @Route("/post/{id}", name="blog_by_id", requirements={"id"="\d+"})
     */
    public function post($id) {
        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->find($id)
        );
    }

    /**
     * @Route("/post/{slug}", name="blog_by_slug") 
     */
    public function postBySlug($slug)
    {
        return $this->json(
            $this->getDoctrine()->getRepository(BlogPost::class)->findBy(['slug' => $slug])
        );
    }

    
    /**
     * @Route ("/{page}", name="blog_list", defaults={"page": 5}, requirements={"page"="\d+"})
     */
    public function list($page = 1, Request $request) {
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(BlogPost::class);
        $items = $repository->findAll();
        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'data' => array_map(function(BlogPost $item) {
                    return $this->generateUrl('blog_by_slug', ['slug' => $item->getSlug()]);
                }, $items)
            ]
        );
    }

    /**
     * @Route("/add", name="blog_add", methods={"POST"})
     */
    public function add(Request $request)
    {        
        $serializer = $this->get('serializer');
        
        try {

            $blogPost = $serializer->deserialize($request->getContent(), BlogPost::class, 'json');

            $em = $this->getDoctrine()->getManager();
            $em->persist($blogPost);
            $em->flush();
            
            return $this->json(array(
                "sucess" => true,
                "message" => "Data saved.. "
            ));

        } catch (Exception $e) {

            return $this->json(array(
                "sucess" => false,
                "message" => "Data is failed to be saved: ", $e->getMessage()
            ));

        }
    }

}
