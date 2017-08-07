<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
//use FOS\RestBundle\Request;
use AppBundle\Entity\User;
use FOS\UserBundle\Model\UserManager;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;

class UserController extends Controller
{

    /**
     * Matches /main exactly
     *
     * @Get("/user")
     */
    public function registerUser(Request $request)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $dispatcher = $this->container->get('event_dispatcher');

//        dump($userManager);
        $user = $userManager->createUser();

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);
        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $user->setUsername($request->get('username'));
        $user->setEmail($request->get('email'));
        $user->setPlainPassword($request->get('password'));
        $user->setEnabled(true);
        $userManager->updateUser($user);

        return new Response('true');
    }

    /**
     * Matches /main exactly
     *
     * @Post("/user/{id}")
     */
    public function editUser(Request $request, $id)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        if ($request->get('email')) $user->setEmail($request->get('email'));
        if ($request->get('username')) $user->setUsername($request->get('username'));

        $this->get('fos_user.user_manager')->updateUser($user, false);
        $this->getDoctrine()->getManager()->flush();

        return new Response('true');
    }

    /**
     * Matches /main exactly
     *
     * @Get("/user/{id}")
     */
    public function showUser($id)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);

        return new Response($user);
    }

    /**
     * Matches /main exactly
     *
     * @Delete("/user/{id}")
     */
    public function deleteUser($id)
    {
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->findUserBy(['id' => $id]);
        $this->get('fos_user.user_manager')->deleteUser($user, false);

        return new Response('true');
    }
}
