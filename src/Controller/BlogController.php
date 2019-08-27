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
