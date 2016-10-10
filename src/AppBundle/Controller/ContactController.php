<?php
/**
 * Created by PhpStorm.
 * User: pablo
 * Date: 09/09/16
 * Time: 17:07
 */
namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as REST;

class ContactController extends Controller
{
    /**
     * @REST\View()
     * @REST\Get("/contacts")
     */
    public function getContactsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $contacts = $em->getRepository("AppBundle:Contact")->findAll();

        return $contacts;
    }

    /**
     * @REST\View()
     * @REST\Get("/contacts/{id}")
     */
    public function getContactAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $contact = $em->getRepository("AppBundle:Contact")->find($id);

        if (empty($contact)) {
            return new JsonResponse(['message' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        return $contact;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @REST\Post("/contacts")
     */
    public function postContactsAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->submit($request->request->all());
        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            return $contact;
        } else
        {
            return $form;
        }
    }

    /**
     * @REST\View()
     * @REST\Put("/contacts/{id}")
     */
    public function putContactAction(Request $request)
    {
        return $this->updateContact($request, true);
    }

    /**
     * @REST\View()
     * @REST\Patch("/contacts/{id}")
     */
    public function patchContactAction(Request $request)
    {
        return $this->updateContact($request, false);
    }

    private function updateContact(Request $request, $clearingMissing)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('AppBundle:Contact')->find($request->get('id'));

        if(empty($contact)){
            return new JsonResponse(['message' => 'Contact not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->submit($request->request->all(), $clearingMissing);

        if($form->isValid()){
            $em->persist($contact);
            $em->flush();
            return $contact;
        }
        else{
            return $form;
        }
    }

    /**
     * @REST\View(statusCode=Response::HTTP_NO_CONTENT)
     * @REST\Delete("contacts/{id}")
     */
    public function removeContactAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $contact = $em->getRepository('AppBundle:Contact')->find($id);

        if($contact){
            $em->remove($contact);
            $em->flush();
        }
    }
}
