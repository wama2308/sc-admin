<?php

namespace AppBundle\Controller;

use AppBundle\Document\Services;
use AppBundle\Document\GeneralConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ServicesController extends Controller {

    /**
     * @Route("/services/index", name="services_list")
     * @Method("GET")
     */
    public function listAction(Request $request) {
        
        $exams = $this->get('doctrine_mongodb')->getRepository('AppBundle:Services')->findAll();
        //var_dump($exams);
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();
        return $this->render('@App/services/index.html.twig', array('exams' => $exams, 'ArrayCategoryExams' => $ArrayCategoryExams));
    }

    /**
     * @Route("/services/create", name="services_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request) {

        $form = $this->createFormBuilder()->getForm();
        $user = $this->getUser()->getId();

        $form->handleRequest($request);
        $examName = $request->request->get("exam");
        $category = $request->request->get("category");
        $fields = $request->request->get("fields");
        $format = $request->request->get("format");
        $arrayFields = explode(",", $fields);


        if (($examName != "") && ($category != "") && ($fields != "") && ($format != "")) {

            $fechaNow = new \MongoDate();

            $exam = new Services();
            $exam->setName($examName);
            $exam->setCategory($category);
            $exam->setFields($arrayFields);
            $exam->setFormat($format);
            $exam->setActive(true);
            $exam->setCreatedAt($fechaNow);
            $exam->setCreatedBy($user);
            $exam->setUpdatedAt($fechaNow);
            $exam->setUpdatedBy($user);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($exam);
            $dm->flush();

            $this->addFlash('notice', 'Registered Service');

            return $this->redirectToRoute('services_list');
        }

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();
        //var_dump($GeneralConfiguration);
        return $this->render('@App/services/create.html.twig', array('ArrayCategoryExams' => $ArrayCategoryExams));
    }

    /**
     * @Route("/services/edit/{id}", name="services_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, Request $request) {
        $exam = $this->get('doctrine_mongodb')->getRepository('AppBundle:Services')->find($id);
        //var_dump($exam);
        $fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();

        $exam->setName($exam->getName());
        $exam->setCategory($exam->getCategory());
        $exam->setFields($exam->getFields());
        $exam->setFormat($exam->getFormat());

        $examName = $request->request->get("exam");
        $category = $request->request->get("category");
        $fields = $request->request->get("fields");
        $format = $request->request->get("format");
        $arrayFields = explode(",", $fields);

        if (($examName != "") && ($category != "") && ($fields != "") && ($format != "")) {

            $dm = $this->get('doctrine_mongodb')->getManager();
            $exam = $dm->getRepository('AppBundle:Services')->find($id);

            $exam->setName($examName);
            $exam->setCategory($category);
            $exam->setFields($arrayFields);
            $exam->setFormat($format);
            $exam->setUpdatedAt($fechaNow);
            $exam->setUpdatedBy($user);

            $dm->flush();

            $this->addFlash('notice', 'Service Updated');

            return $this->redirectToRoute('services_details', array('id' => $id));
        }


        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();
        return $this->render('@App/services/edit.html.twig', array('exam' => $exam, 'ArrayCategoryExams' => $ArrayCategoryExams));
    }

    /**
     * @Route("/services/details/{id}", name="services_details")
     * @Method("GET")
     */
    public function detailsAction($id) {
        $exams = $this->get('doctrine_mongodb')->getRepository('AppBundle:Services')->find($id);
        //var_dump($exams);
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();
        return $this->render('@App/services/details.html.twig', array('exams' => $exams, 'ArrayCategoryExams' => $ArrayCategoryExams));
    }

    /**
     * @Route("/services/delete/{id}", name="services_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        $dm = $this->get('doctrine_mongodb')->getManager();
        $exam = $dm->getRepository('AppBundle:Services')->find($id);

        $exam->setActive(false);

        $dm->flush();

        $this->addFlash('error', 'Service Removed');

        return $this->redirectToRoute('services_details', array('id' => $id));
    }

}
