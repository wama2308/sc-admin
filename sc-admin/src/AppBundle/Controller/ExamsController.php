<?php

namespace AppBundle\Controller;

use AppBundle\Document\Exams;
use AppBundle\Document\GeneralConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ExamsController extends Controller {

    /**
     * @Route("/exams/index", name="exams_list")
     * @Method("GET")
     */
    public function listAction(Request $request) {

        $exams = $this->get('doctrine_mongodb')->getRepository('AppBundle:Exams')->findAll();
        //var_dump($exams);
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();
        return $this->render('@App/exams/index.html.twig', array('exams' => $exams, 'ArrayCategoryExams' => $ArrayCategoryExams));
    }

    /**
     * @Route("/exams/create", name="exams_create")
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

            $exam = new Exams();
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

            $this->addFlash('notice', 'Registered Exam');

            return $this->redirectToRoute('exams_list');
        }

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();
        //var_dump($GeneralConfiguration);
        return $this->render('@App/exams/create.html.twig', array('ArrayCategoryExams' => $ArrayCategoryExams));
    }

    /**
     * @Route("/exams/edit/{id}", name="exams_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, Request $request) {
        $exam = $this->get('doctrine_mongodb')->getRepository('AppBundle:Exams')->find($id);
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
            $exam = $dm->getRepository('AppBundle:Exams')->find($id);

            $exam->setName($examName);
            $exam->setCategory($category);
            $exam->setFields($arrayFields);
            $exam->setFormat($format);            
            $exam->setUpdatedAt($fechaNow);
            $exam->setUpdatedBy($user);

            $dm->flush();

            $this->addFlash('notice', 'Exam Updated');

            return $this->redirectToRoute('exams_details', array('id' => $id));
        }
        
        
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();
        return $this->render('@App/exams/edit.html.twig', array('exam' => $exam, 'ArrayCategoryExams' => $ArrayCategoryExams));
    }
    
    /**
     * @Route("/exams/details/{id}", name="exams_details")
     * @Method("GET")
     */
    public function detailsAction($id) {
        $exams = $this->get('doctrine_mongodb')->getRepository('AppBundle:Exams')->find($id);
        //var_dump($exams);
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();
        return $this->render('@App/exams/details.html.twig', array('exams' => $exams, 'ArrayCategoryExams' => $ArrayCategoryExams));
    }

    /**
     * @Route("/exams/delete/{id}", name="exams_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        $dm = $this->get('doctrine_mongodb')->getManager();
        $exam = $dm->getRepository('AppBundle:Exams')->find($id);

        $exam->setActive(false);

        $dm->flush();

        $this->addFlash('error', 'Exam Removed');

        return $this->redirectToRoute('exams_details', array('id' => $id));
    }

}
