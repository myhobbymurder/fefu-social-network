<?php

namespace Network\InstaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;

class CheckLoginController extends Controller
{
    public function getCodeAction()
    {

        $parameters = array(
            'grant_type'    => $this->container->getParameter('grant_type'),
            'client_id'     => $this->container->getParameter('client_id'),
            'client_secret' => $this->container->getParameter('client_secret'),
            'redirect_uri'  => $this->container->getParameter('redirect-uri'),
            'code'  => $_GET['code']
        );
        try
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->container->getParameter('instagram-uri'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            /*$response = curl_exec($ch);
            $provider = $this->get('network.userprovider');
            $provider->loadUserByOAuthUserResponse($response);*/
        } catch (Exception $e) {
            sprintf($e->getTrace());
        }
        return  $this->render('NetworkInstaBundle:Default:index.html.twig', array('name' => "Hello"));
    }
}
