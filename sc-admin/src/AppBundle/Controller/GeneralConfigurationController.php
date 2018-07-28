<?php

namespace AppBundle\Controller;

use AppBundle\Document\GeneralConfiguration;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GeneralConfigurationController extends Controller {

    /**
     * @Route("/modules/index", name="modules_list")     
     * @Method("GET")
     */
    public function listAction(Request $request) {

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $modules = $GeneralConfiguration->getModules();
        return $this->render('@App/general_configuration/index.html.twig', array('modules' => $modules));
    }

    /**
     * @Route("/modules/create", name="modules_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request) {

        $form = $this->createFormBuilder()->getForm();
        $user = $this->getUser()->getId();

        $form->handleRequest($request);
        $module = $request->request->get("module");
        $route = $request->request->get("route");
        $action = $request->request->get("action");
        $description = $request->request->get("description");
        $permits = $request->request->get("permits");
        $arrayPermits = explode(",", $permits);


        //if ($form->isSubmitted() && $form->isValid()) {
        if (($module != "") && ($route != "") && ($action != "") && ($description != "") && ($permits != "")) {

            $fechaNow = new \MongoDate();

            $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
            $arrayModules = $GeneralConfiguration->getModules();
            $module_id = new \MongoId();

            $arrayModules[] = array(
                "_id" => $module_id,
                "name" => $module,
                "route" => $route,
                "action" => $action,
                "description" => $description,
                "permits" => $arrayPermits,
                "status" => 1,
                "created_at" => $fechaNow,
                "created_by" => $user,
                "updated_at" => $fechaNow,
                "updated_by" => $user);

            $GeneralConfiguration->setModules($arrayModules);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($GeneralConfiguration);
            $dm->flush();

            $this->addFlash('notice', 'Registered Module');

//            return $this->redirectToRoute('modules_list');
        }

        return $this->render('@App/general_configuration/create.html.twig');
    }

    /**
     * @Route("/modules/edit/{id}/{positionModule}", name="modules_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, $positionModule, Request $request) {
        $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($id);
        $fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();

        $module = $request->request->get("module");
        $route = $request->request->get("route");
        $action = $request->request->get("action");
        $description = $request->request->get("description");
        $permits = $request->request->get("permits");
        $arrayPermits = explode(",", $permits);

        if (($module != "") && ($route != "") && ($action != "") && ($description != "") && ($permits != "")) {

            $fechaNow = new \MongoDate();

            $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
            $arrayModules = $GeneralConfiguration->getModules();
            
            $arrayModules[$positionModule]["name"] = $module;
            $arrayModules[$positionModule]["route"] = $route;
            $arrayModules[$positionModule]["action"] = $action;
            $arrayModules[$positionModule]["description"] = $description;
            $arrayModules[$positionModule]["permits"] = $arrayPermits;
            $arrayModules[$positionModule]["updated_at"] = $fechaNow;
            $arrayModules[$positionModule]["updated_by"] = $user;
            
            $GeneralConfiguration->setModules($arrayModules);
           

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($GeneralConfiguration);
            $dm->flush();

            $this->addFlash('notice', 'Module Updated');

            return $this->redirectToRoute('modules_details', array('id' => $id, 'positionModule' => $positionModule));
        }

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $modules = $GeneralConfiguration->getModules();
        return $this->render('@App/general_configuration/edit.html.twig', array('modules' => $modules, 'id' => $id, 'positionModule' => $positionModule));
    }

    /**
     * @Route("/modules/details/{id}/{positionModule}", name="modules_details")
     * @Method("GET")
     */
    public function detailsAction($id, $positionModule) {

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $arrayModules = $GeneralConfiguration->getModules();
        $id = $arrayModules[$positionModule]["_id"];
        $name = $arrayModules[$positionModule]["name"];
        $route = $arrayModules[$positionModule]["route"];
        $action = $arrayModules[$positionModule]["action"];
        $description = $arrayModules[$positionModule]["description"];
        $statusModule = $arrayModules[$positionModule]["status"];
        $permits = $arrayModules[$positionModule]["permits"];
        //var_dump($permits);
        return $this->render('@App/general_configuration/details.html.twig', array('id' => $id, 'name' => $name, 'route' => $route, 'action' => $action, 'statusModule' => $statusModule, 'permits' => $permits, 'description' => $description, 'positionModule' => $positionModule));
    }

    /**
     * @Route("/modules/delete/{id}/{positionModule}", name="modules_delete")
     * @Method("GET")
     */
    public function deleteAction($id, $positionModule) {

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $arrayModules = $GeneralConfiguration->getModules();
        $arrayModules[$positionModule]["status"] = 0;
        $GeneralConfiguration->setModules($arrayModules);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($GeneralConfiguration);
        $dm->flush();

        $this->addFlash('error', 'Module Removed');

        return $this->redirectToRoute('modules_details', array('id' => $id, 'positionModule' => $positionModule));
    }

}
