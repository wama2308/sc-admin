<?php

namespace AppBundle\Controller;

use AppBundle\Document\Country;
use AppBundle\Document\GeneralConfiguration;
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

class CountryController extends Controller {

    /**
     * @Route("/country/index", name="country_list")     
     * @Method("GET")
     */
    public function listAction(Request $request) {

//        $m = $this->container->get('doctrine_mongodb.odm.default_connection');
//        $db = $m->selectDatabase('smart_clinic');
//        $collection = $db->createCollection('Country');
//        $countries = $collection->find();
//        return $this->render('@Admin/country/index.html.twig', array('countries' => $countries));
        //var_dump($countries);
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findAll();
        //var_dump($countries);
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayLanguajes = $GeneralConfiguration->getLanguajes();
        return $this->render('@App/country/index.html.twig', array('countries' => $countries, 'ArrayLanguajes' => $ArrayLanguajes));
    }

    /**
     * @Route("/country/create", name="country_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request) {

        $form = $this->createFormBuilder()->getForm();
        $user = $this->getUser()->getId();

        $form->handleRequest($request);
        $name = $request->request->get("country");
        $timeZone = $request->request->get("time_zone");
        $acronym = $request->request->get("acronym");
        $coin = $request->request->get("coin");
        $currencySymbol = $request->request->get("currency_symbol");
        $taxRate = $request->request->get("tax_rate");
        $telefonePrefix = $request->request->get("telefone_prefix");
        $languaje = $request->request->get("languaje");
        $issuingbank = $request->request->get("issuingbank");
        $arrayIssuingbank = explode(",", $issuingbank);
        $receivingbank = $request->request->get("receivingbank");
        $arrayReceivingbank = explode(",", $receivingbank);
        $waytopay = $request->request->get("waytopay");
        $arrayWaytopay = explode(",", $waytopay);

        //if ($form->isSubmitted() && $form->isValid()) {
        if (($name != "") && ($timeZone != "") && ($acronym != "") && ($coin != "") && ($currencySymbol != "") && ($taxRate != "") && ($telefonePrefix != "") && ($languaje != "")) {

            $fechaNow = new \MongoDate();

            $country = new Country();
            $country->setName($name);
            $country->setTimezone($timeZone);
            $country->setAcronym($acronym);
            $country->setCoin($coin);
            $country->setCurrencySymbol($currencySymbol);
            $country->setTaxRate($taxRate);
            $country->setTelephonePrefix($telefonePrefix);
            $country->setActive(true);
            $country->setLanguaje($languaje);
            $country->setIssuingbank($arrayIssuingbank);
            $country->setReceivingbank($arrayReceivingbank);
            $country->setWaytopay($arrayWaytopay);
            $country->setCreatedAt($fechaNow);
            $country->setCreatedBy($user);
            $country->setUpdatedAt($fechaNow);
            $country->setUpdatedBy($user);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($country);
            $dm->flush();

            $this->addFlash('notice', 'Registered Country');

            return $this->redirectToRoute('country_list');
        }

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayLanguajes = $GeneralConfiguration->getLanguajes();
        return $this->render('@App/country/create.html.twig', array('ArrayLanguajes' => $ArrayLanguajes));
    }

    /**
     * @Route("/country/edit/{id}", name="country_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, Request $request) {
        $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($id);
        $fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();

        $country->setName($country->getName());
        $country->setTimezone($country->getTimezone());
        $country->setAcronym($country->getAcronym());
        $country->setCoin($country->getCoin());
        $country->setCurrencySymbol($country->getCurrencySymbol());
        $country->setTaxRate($country->getTaxRate());
        $country->setTelephonePrefix($country->getTelephonePrefix());
        $country->setLanguaje($country->getLanguaje());
        $country->setIssuingbank($country->getIssuingbank());
        $country->setReceivingbank($country->getReceivingbank());
        $country->setWaytopay($country->getWaytopay());

        $name = $request->request->get("country");
        $timeZone = $request->request->get("time_zone");
        $acronym = $request->request->get("acronym");
        $coin = $request->request->get("coin");
        $currencySymbol = $request->request->get("currency_symbol");
        $taxRate = $request->request->get("tax_rate");
        $telefonePrefix = $request->request->get("telefone_prefix");
        $languaje = $request->request->get("languaje");
        $issuingbank = $request->request->get("issuingbank");
        $arrayIssuingbank = explode(",", $issuingbank);
        $receivingbank = $request->request->get("receivingbank");
        $arrayReceivingbank = explode(",", $receivingbank);
        $waytopay = $request->request->get("waytopay");
        $arrayWaytopay = explode(",", $waytopay);

        if (($name != "") && ($timeZone != "") && ($acronym != "") && ($coin != "") && ($currencySymbol != "") && ($taxRate != "") && ($telefonePrefix != "") && ($languaje != "")) {

            $dm = $this->get('doctrine_mongodb')->getManager();
            $country = $dm->getRepository('AppBundle:Country')->find($id);

            $country->setName($name);
            $country->setTimezone($timeZone);
            $country->setAcronym($acronym);
            $country->setCoin($coin);
            $country->setCurrencySymbol($currencySymbol);
            $country->setTaxRate($taxRate);
            $country->setTelephonePrefix($telefonePrefix);
            $country->setLanguaje($languaje);
            $country->setIssuingbank($arrayIssuingbank);
            $country->setReceivingbank($arrayReceivingbank);
            $country->setWaytopay($arrayWaytopay);
            $country->setUpdatedAt($fechaNow);
            $country->setUpdatedBy($user);

            $dm->flush();

            $this->addFlash('notice', 'Country Updated');

            return $this->redirectToRoute('country_list');
        }

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayLanguajes = $GeneralConfiguration->getLanguajes();
        return $this->render('@App/country/edit.html.twig', array('country' => $country, 'ArrayLanguajes' => $ArrayLanguajes));
    }

    /**
     * @Route("/country/details/{id}", name="country_details")
     * @Method("GET")
     */
    public function detailsAction($id) {
        $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($id);
        return $this->render('@App/country/details.html.twig', array('country' => $country));
    }

    /**
     * @Route("/country/delete/{id}", name="country_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        $dm = $this->get('doctrine_mongodb')->getManager();
        $country = $dm->getRepository('AppBundle:Country')->find($id);

        $country->setActive(false);

        $dm->flush();

        $this->addFlash('error', 'Country Removed');

        return $this->redirectToRoute('country_list');
    }    

}
