<?php

namespace AppBundle\Controller;

use AppBundle\Document\Country;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\TextareaType;
//use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;

class UsersController extends Controller
{
/* le indicamos el método http, el nombre de la acción y action para decirle que esto es una acción del controlador */
    public function getUsersAction()
    {
        $provinces = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true)));
        //var_dump($provinces);
        if ($provinces === null) {
            return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $provinces;
    }
 
}
