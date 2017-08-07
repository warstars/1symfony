<?php
namespace AppBundle\Controller;

use Doctrine\DBAL\Types\DateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use FOS\RestBundle\Request;
use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Model\UserManagerInterface;
use AppBundle\Entity\Blog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication;

class ArticleController extends Controller
{

    /**
     * Matches /main exactly
     *
     * @Get("/api/menu")
     */
//    public function getIndex()
//    {
//        $name = 'Hello programmer';
//
//        return $this->render('main.html.twig', array('name' => $name));
//    }

    /**
     * Matches /main exactly
     *
     * @Post("/users", name="get_user")
     */
//    public function postUser(Request $request)
//    {
////        $userManager = $this->get('fos_user.user_manager')->getUser();
////        dump($userManager);
//        $user = new User;
//        $form = $this->createFormBuilder($user)->add('save', SubmitType::class, array('label' => 'Create Task'))->getForm();
//        $form->handleRequest($request);
//        dump($form->handleRequest($request));
//        $name = 'Hello programmer';
//
//        return $this->render('blog.html.twig', array('name' => $name));
//    }
    /**
     * Matches /main exactly
     *
     * @Post("/api/news")
     */
    public function postNewsArticle(Request $request, EntityManagerInterface $em)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (!empty($request)) {
            $article = new Blog();
            $article->setTitle($request->get('title', null));
            $article->setDescription($request->get('description', null));
            $article->setCreatedAt(new \DateTime(date('Y-m-d H:i:s')));
            if ($user != 'anon.') {
                $article->setCreatedBy($user->getUsername());
            }

            $em->persist($article);
            $em->flush();
        } else {
            return new Response('false');
        }

        return new Response('true');
    }

    /**
     * Matches /main exactly
     *
     * @Get("/api/news")
     */
    public function getArticles(Request $request)
    {
        $articles = $this->getDoctrine()
            ->getRepository(Blog::class)->findAll();
        if (!empty($articles)) {
            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $articles,
                $request->query->getInt('page', 1)/*page number*/,
                10/*limit per page*/
            );

            return $this->render('AppBundle:Article:list.html.twig', array('pagination' => $pagination));
        }

        return new Response('false');
    }

    /**
     * Matches /main exactly
     *
     * @Put("/api/news/{id}")
     */
    public function editArticle($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Blog::class)->find($id);
        if (!empty($article) and $request) {
            $article->setTitle($request->get('title'));
            $article->setDescription($request->get('description'));

            $em->persist($article);
            $em->flush();

            return new Response('true');
        }

        return new Response('false');
    }

    /**
     * Matches /main exactly
     *
     * @Get("/api/news/{id}")
     */
    public function getArticleOne($id)
    {
        $article = $this->getDoctrine()
            ->getRepository(Blog::class)->find($id);

        return new Response($article);
    }
    /**
     * Matches /main exactly
     *
     * @Delete("/api/news/{id}")
     */
    public function deleteArticle($id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Blog::class)->find($id);
        if (!empty($article)) {

            $em->remove($article);
            $em->flush();

            return new Response('true');
        }

        return new Response('false');
    }
}
