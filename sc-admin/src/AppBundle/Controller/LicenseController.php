<?php

namespace AppBundle\Controller;

use AppBundle\Document\License;
use AppBundle\Document\Services;
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

class LicenseController extends Controller {

    /**
     * @Route("/license/index", name="license_list")
     * @Method("GET")
     */
    public function listAction(Request $request) {

        $licenses = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->findByActive(true);
        //var_dump($exams);        
        return $this->render('@App/license/index.html.twig', array('licenses' => $licenses));
    }

    /**
     * @Route("/license/create", name="license_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request) {

        $form = $this->createFormBuilder()->getForm();
        $user = $this->getUser()->getId();

        $form->handleRequest($request);
        $license = $request->request->get("license");
        $typelicense = $request->request->get("typelicense");
        $UsersQuantity = $request->request->get("UsersQuantity");
        $numberClients = $request->request->get("numberClients");
        $numberExams = $request->request->get("numberExams");
        $exam = $request->request->get("exams");
        //$arrayExams = explode(",", $exam);
        $modules = $request->request->get("modules");
        //$arrayModules = explode(",", $modules);
        $country = $request->request->get("countries");
        //$arrayCountries = explode(",", $country);
        $durationTime = $request->request->get("durationTime");
        $noticePayment = $request->request->get("noticePayment");
        $description = $request->request->get("description");
        $amount = $request->request->get("amount");


        if (($license != "") && ($typelicense != "") && ($UsersQuantity != "") && ($numberClients != "") && ($numberExams != "") && ($exam != "") && ($modules != "") && ($country != "") && ($durationTime != "") && ($noticePayment != "") && ($description != "") && ($amount != "")) {

            $fechaNow = new \MongoDate();

            $licence = new License();
            $licence->setLicense($license);
            $licence->setTypelicense($typelicense);            
            $licence->setUsersquantity($UsersQuantity);
            $licence->setNumberClients($numberClients);
            $licence->setNumberExams($numberExams);
            $licence->setExams($exam);
            $licence->setModules($modules);
            $licence->setCountries($country);
            $licence->setDurationTime($durationTime);
            $licence->setNoticepayment($noticePayment);
            $licence->setDescription($description);
            $licence->setAmount($amount);
            $licence->setActive(true);
            $licence->setCreatedAt($fechaNow);
            $licence->setCreatedBy($user);
            $licence->setUpdatedAt($fechaNow);
            $licence->setUpdatedBy($user);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($licence);
            $dm->flush();

            $this->addFlash('notice', 'Registered Licence');

            return $this->redirectToRoute('license_list');
        }

        $exams = $this->get('doctrine_mongodb')->getRepository('AppBundle:Services')->findByActive(true);

        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findByActive(true);

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayModules = $GeneralConfiguration->getModules();
        $ArrayTypeLicense = $GeneralConfiguration->getTypelicense();
        //var_dump($ArrayTypeLicense);
        return $this->render('@App/license/create.html.twig', array('ArrayModules' => $ArrayModules, 'exams' => $exams, 'countries' => $countries, 'ArrayTypeLicense' => $ArrayTypeLicense));
    }

    /**
     * @Route("/license/edit/{id}", name="license_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, Request $request) {
        $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($id);
        $fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();
        $arrayExams = $license->getExams();
        
        $license->setLicense($license->getLicense());
        $license->setTypelicense($license->getTypelicense());
        $license->setUsersquantity($license->getUsersquantity());
        $license->setNumberClients($license->getNumberclients());
        $license->setNumberExams($license->getNumberexams());
        $license->setExams($license->getExams());
        $license->setModules($license->getModules());
        $license->setCountries($license->getCountries());
        $license->setDurationTime($license->getDurationtime());
        $license->setNoticepayment($license->getNoticepayment());
        $license->setDescription($license->getDescription());
        $license->setAmount($license->getAmount());

        $licenceName = $request->request->get("license");
        $typelicense = $request->request->get("typelicense");
        $UsersQuantity = $request->request->get("UsersQuantity");
        $numberClients = $request->request->get("numberClients");
        $numberExams = $request->request->get("numberExams");
        $exam = $request->request->get("exams");
        //$arrayExams = explode(",", $exam);
        $modules = $request->request->get("modules");
        //$arrayModules = explode(",", $modules);
        $country = $request->request->get("countries");
        //$arrayCountries = explode(",", $country);
        $durationTime = $request->request->get("durationTime");
        $noticePayment = $request->request->get("noticePayment");
        $description = $request->request->get("description");
        $amount = $request->request->get("amount");

        if (($licenceName != "") && ($typelicense != "") && ($UsersQuantity != "") && ($numberClients != "") && ($numberExams != "") && ($exam != "") && ($modules != "") && ($country != "") && ($durationTime != "") && ($noticePayment != "") && ($description != "") && ($amount != "")) {

            $dm = $this->get('doctrine_mongodb')->getManager();
            $license = $dm->getRepository('AppBundle:License')->find($id);

            $license->setLicense($licenceName);
            $license->setTypelicense($typelicense);            
            $license->setUsersquantity($UsersQuantity);
            $license->setNumberClients($numberClients);
            $license->setNumberExams($numberExams);
            $license->setExams($exam);
            $license->setModules($modules);
            $license->setCountries($country);
            $license->setDurationTime($durationTime);
            $license->setNoticepayment($noticePayment);
            $license->setDescription($description);
            $license->setAmount($amount);

            $dm->flush();

            $this->addFlash('notice', 'License Updated');

            return $this->redirectToRoute('license_details', array('id' => $id));
        }

        $exams = $this->get('doctrine_mongodb')->getRepository('AppBundle:Services')->findByActive(true);
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findByActive(true);
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayModules = $GeneralConfiguration->getModules();
        $ArrayTypeLicense = $GeneralConfiguration->getTypelicense();

        //var_dump($ArrayModules);
        return $this->render('@App/license/edit.html.twig', array('license' => $license, 'ArrayModules' => $ArrayModules, 'exams' => $exams, 'countries' => $countries, 'ArrayTypeLicense' => $ArrayTypeLicense));
    }

    /**
     * @Route("/license/details/{id}", name="license_details")
     * @Method("GET")
     */
    public function detailsAction($id) {
        $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($id);
        $exams = $this->get('doctrine_mongodb')->getRepository('AppBundle:Services')->findByActive(true);
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findByActive(true);
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayModules = $GeneralConfiguration->getModules();
//        var_dump($license->getModules());
        return $this->render('@App/license/details.html.twig', array('license' => $license, 'exams' => $exams, 'ArrayModules' => $ArrayModules, 'countries' => $countries));
    }

    /**
     * @Route("/license/delete/{id}", name="license_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        $dm = $this->get('doctrine_mongodb')->getManager();
        $license = $dm->getRepository('AppBundle:License')->find($id);

        $license->setActive(false);

        $dm->flush();

        $this->addFlash('error', 'License Removed');

        return $this->redirectToRoute('license_details', array('id' => $id));
    }

}
