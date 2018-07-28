<?php

namespace AppBundle\Controller;

use AppBundle\Document\Country;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
//use Symfony\Component\Form\Extension\Core\Type\TextareaType;
//use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
//use FOS\RestBundle\Controller\Annotations as Rest;
//use FOS\RestBundle\Controller\FOSRestController;
//use Symfony\Component\HttpFoundation\Response;
//use FOS\RestBundle\View\View;
//use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

class ProvinceController extends Controller {

    /**
     * @Route("/province/index", name="province_list")
     * @Method("GET")
     */
    public function listAction(Request $request) {

        $provinces = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true)));
        //var_dump($provinces);
        return $this->render('@App/province/index.html.twig', array('provinces' => $provinces));
    }

    /**
     * @Route("/province/create", name="province_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request) {
        $form = $this->createFormBuilder()->getForm();
        $user = $this->getUser()->getId();
        $fechaNow = new \MongoDate();

        $form->handleRequest($request);
        $country_id = $request->request->get("country_id");
        $province_name = $request->request->get("province");

        if (($country_id != "") && ($province_name != "")) {

            $province = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
            $province_exist = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('id' => $country_id, 'provinces.name' => $province_name));

            $countProvinceExist = count($province_exist);

            if ($countProvinceExist >= 1) {
                $this->addFlash('notice', 'Esta provincia ya esta registrada');
            } else {
                $classProvince = new \AppBundle\Document\provinces();

                $classProvince->setName($province_name);
                $classProvince->setActive(true);
                $classProvince->setCreatedAt($fechaNow);
                $classProvince->setCreatedBy($user);
                $classProvince->setUpdatedAt($fechaNow);
                $classProvince->setUpdatedBy($user);
                $provincia_id = new \MongoId();

                $arrayProvince = $province->getProvinces();

                $arrayProvince[] = array("_id" => $provincia_id, "name" => $classProvince->getName(), "active" => $classProvince->getActive(), "created_at" => $classProvince->getCreatedAt(), "created_by" => $classProvince->getCreatedBy(), "updated_at" => $classProvince->getUpdatedAt(), "updated_by" => $classProvince->getUpdatedBy());

                $province->setProvinces($arrayProvince);

                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->persist($province);
                $dm->flush();

                $this->addFlash('notice', 'Registered Province');

                return $this->redirectToRoute('province_list');
            }
        }
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findByActive(true);
        return $this->render('@App/province/create.html.twig', array('countries' => $countries));
    }

    /**
     * @Route("/province/edit/{id}/{posicionProvince}", name="province_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, $posicionProvince, Request $request) {
        
        $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($id);
        $form = $this->createFormBuilder()->getForm();
        
        $date = new \MongoDate();
        $form->handleRequest($request);
        $country_id = $request->request->get("country_id");
        $province_name = $request->request->get("province");

        if (($country_id != "") && ($province_name != "")) {
            
            $provincia = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
            //$provincia = $this->get('doctrine_mongodb')->getRepository('AppBundle:Pais')->findBy(array('id' => $pais_id, 'provincia.name' => $nombreProvincia));
            $arrayProvince = $provincia->getProvinces();
            $arrayProvince[$posicionProvince]["name"] = $province_name;
            $arrayProvince[$posicionProvince]["updated_at"] = $date;
            $country->setProvinces($arrayProvince);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($provincia);
            $dm->flush();

            $this->addFlash('notice', 'Province Updated');

            return $this->redirectToRoute('province_details', array('id' => $id, 'posicionProvince' => $posicionProvince));
        }
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findByActive(true);
        return $this->render('@App/province/edit.html.twig', array('country' => $country, 'countries' => $countries, 'posicionProvince' => $posicionProvince));
    }

    /**
     * @Route("/province/details/{id}/{posicionProvince}", name="province_details")
     * @Method("GET")
     */
    public function detailsAction($id, $posicionProvince) {
        $CountryProvince = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($id);
        $nameCountry = $CountryProvince->getName();
        $arrayProvince = $CountryProvince->getProvinces();
        $nameProvince = $arrayProvince[$posicionProvince]["name"];        
        $activeProvince = $arrayProvince[$posicionProvince]["active"];        

        return $this->render('@App/province/details.html.twig', array('country' => $nameCountry, 'province' => $nameProvince, 'id' => $id, 'posicionProvince' => $posicionProvince, 'activeProvince' => $activeProvince));
    }

    /**
     * @Route("/province/delete/{id}/{posicionProvince}", name="province_delete")
     * @Method("GET")
     */
    public function deleteAction($id, $posicionProvince) {

        $province = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($id);
        $arrayProvince = $province->getProvinces();
        $arrayProvince[$posicionProvince]["active"] = false;
        $province->setProvinces($arrayProvince);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($province);
        $dm->flush();

        $this->addFlash('error', 'Province Removed');

        return $this->redirectToRoute('province_details', array('id' => $id, 'posicionProvince' => $posicionProvince));
    }

}
