<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Form\Type\ClientType;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\File\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
/**
 * Class ClientController
 * @package AppBundle\Controller
 *
 * @RouteResource("Client")
 */
class ClientController extends FOSRestController implements ClassResourceInterface
{

    /**
     * @Route("/", name="all")
     * @Method("GET")
     */

    public function getAction()
    {
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        return $this->render('client/all.html.twig', [
            'clients' => $clients,
        ]);
    }
    /**
     * @Route("/create", name="create")
     * @Method({"POST", "GET"})
     */
    public function postAction(Request $request)
    {
        $form = $this->createForm(ClientType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $client = $form->getData();
            $ip = $client->getIpAddress();
            $mac = $client->getMacAddress();
            $response = $this->forward('AppBundle:Another:get', array(
                'Ip_address' => $ip,
                'Mac_address' => $mac,
            ));

            if ($response->getContent() == '{"success":"true"}') {
                $em->persist($client);
                $em->flush();

            }
            else return new Response('no match between ip and mac');
            $this->addFlash(
                'success',
                'Client added!'
            );

        }
        return $this->render('client/create.html.twig', [
            'form' => $form->createView()
        ]);
        }


    /**
     * @Route("/edit/{id}", name="edit")
     * @Method({"POST","GET"})
     */
    public function patchAction(Request $request, Client $client)
    {
        if($client->getImage()!=null)
            $client->setImage(new File($this->getParameter('image_directory').'/'.$client->getImage()));

        $form = $this->createForm('AppBundle\Form\Type\ClientType', $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('all', array('id' => $client->getId()));
        }
        return $this->render('client/edit.html.twig', array(
            'client' => $client,
            'form' => $form->createView()
        ));

    }

    /**
     * @Route("/delete/{client}", name="delete")
     * @Method({"DELETE", "GET"})
     */
    public function deleteAction($client)
    {
        if ($client === null) {
            return $this->redirectToRoute('all');
        }
        $em = $this->getDoctrine()->getManager();
        $delclient = $em->getRepository('AppBundle:Client')->findOneById($client);
        if (!$delclient) {
            throw $this->createNotFoundException('No client found for id '.$client);
        }
        $em->remove($delclient);
        $em->flush();
        return $this->redirectToRoute('all');
    }

}
