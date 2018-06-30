<?php

namespace AppBundle\Controller\ApiRest;

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
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;


class ApiRestCountryController extends Controller {
    /* le indicamos el método http, el nombre de la acción y action para decirle que esto es una acción del controlador */
    
    /**
     * @Rest\Get("/apiCountry")
     */    
    public function getAction() {
        $provinces = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true)));
        //var_dump($provinces);
        if ($provinces === null) {
            return new View("there are no users exist", Response::HTTP_NOT_FOUND);
        }
        return $provinces;
    }

}
