<?php
/**
 * Created by PhpStorm.
 * User: pablo
 * Date: 10/09/16
 * Time: 00:47
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use AppBundle\Form\Type\UserType;
use FOS\RestBundle\Controller\Annotations as REST;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @REST\View()
     * @REST\Get("/users")
     */
    public function getUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository("AppBundle:User")->findAll();

        return $users;
    }

    /**
     * @REST\View()
     * @REST\Get("/users/{id}")
     */
    public function getUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);

        if(empty($user)){
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    /**
     * @REST\View(statusCode=Response::HTTP_CREATED)
     * @REST\Post("/users")
     */
    public function postUserAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());
        if($form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $user;
        }else{
            return $form;
        }
    }

    /**
     * @REST\View(statusCode=Response::HTTP_NO_CONTENT)
     * @REST\Delete("/users/{id}")
     */
    public function removeUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AppBundle:User")->find($id);

        if($user){
            $em->remove($user);
            $em->flush();
        }
    }
}
