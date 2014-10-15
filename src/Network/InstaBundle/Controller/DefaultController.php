<?php

namespace Network\InstaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NetworkInstaBundle:Default:index.html.twig', array('name' => $name));
    }
}
