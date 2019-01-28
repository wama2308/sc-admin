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
        $controller = $request->request->get("controller");
        $description = $request->request->get("description");
        $countPermits = $request->request->get("countPermits");

        //if ($form->isSubmitted() && $form->isValid()) {
        if (($module != "") && ($controller != "") && ($countPermits != "0")) {

            //$fechaNow = new \MongoDate();
            ///////////////////////PARA LA FECHA CON SU RESPECTIVA ZONA HORARIA
            $timeZ = $request->request->get("timeZ");
            if ($timeZ == null) {
                $timeZone = "America/Caracas";
            } else {
                $timeZone = $timeZ;
            }
            date_default_timezone_set($timeZone);
            $dt = new \DateTime(date('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
            $ts = $dt->getTimestamp();
            $fechaNow = new \MongoDate($ts);
            ///////////////////////PARA LA FECHA CON SU RESPECTIVA ZONA HORARIA

            $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
            $arrayModules = $GeneralConfiguration->getModules();
            $module_id = new \MongoId();

            for ($i = 0; $i < $countPermits; $i++) {

                $arrayMethod = explode(",", $request->request->get("method_" . $i));

                $arrayPermits[] = array(
                    "_id" => new \MongoId(),
                    "permit" => $request->request->get("permit_" . $i),
                    "route" => $request->request->get("route_" . $i),
                    "action" => $request->request->get("action_" . $i),
                    "method" => $arrayMethod,
                    "descriptionPermit" => $request->request->get("descriptionPermit_" . $i));
            }

            $arrayModules[] = array(
                "_id" => $module_id,
                "name" => $module,
                "controller" => $controller,
                "description" => $description,
                "description" => $description,
                "permits" => $arrayPermits,
                "status" => 1,
                "created_at" => $fechaNow,
                "created_by" => $user,
                "updated_at" => $fechaNow,
                "updated_by" => $user
            );

            $GeneralConfiguration->setModules($arrayModules);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($GeneralConfiguration);
            $dm->flush();

            $this->addFlash('notice', 'Registered Module');

            return $this->redirectToRoute('modules_list');
        }
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $methodArray = $GeneralConfiguration->getMethods();
        $permitsArray = $GeneralConfiguration->getPermits();
        return $this->render('@App/general_configuration/create.html.twig', array('methodArray' => $methodArray, 'permitsArray' => $permitsArray));
    }

    /**
     * @Route("/modules/edit/{id}/{positionModule}", name="modules_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, $positionModule, Request $request) {

        //$fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();

        $module = $request->request->get("module");
        $controller = $request->request->get("controller");
        $description = $request->request->get("description");
        $countPermits = $request->request->get("countPermits");

        //if ($form->isSubmitted() && $form->isValid()) {
        if (($module != "") && ($controller != "") && ($countPermits != "0")) {

            //$fechaNow = new \MongoDate();
            ///////////////////////PARA LA FECHA CON SU RESPECTIVA ZONA HORARIA
            $timeZ = $request->request->get("timeZ");
            if ($timeZ == null) {
                $timeZone = "America/Caracas";
            } else {
                $timeZone = $timeZ;
            }
            date_default_timezone_set($timeZone);
            $dt = new \DateTime(date('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
            $ts = $dt->getTimestamp();
            $fechaNow = new \MongoDate($ts);
            ///////////////////////PARA LA FECHA CON SU RESPECTIVA ZONA HORARIA

            $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
            $arrayModules = $GeneralConfiguration->getModules();
            $module_id = new \MongoId();

            for ($i = 0; $i <= $countPermits; $i++) {

                if ($request->request->get("permit_" . $i) != null) {
//                    var_dump($request->request->get("permit_" . $i));
                    $arrayMethod = explode(",", $request->request->get("method_" . $i));

                    $arrayPermits[] = array(
                        "_id" => new \MongoId(),
                        "permit" => $request->request->get("permit_" . $i),
                        "route" => $request->request->get("route_" . $i),
                        "action" => $request->request->get("action_" . $i),
                        "method" => $arrayMethod,
                        "descriptionPermit" => $request->request->get("descriptionPermit_" . $i));
                }
            }
//            var_dump($arrayPermits);
            $arrayModules[$positionModule] = array(
                "_id" => $module_id,
                "name" => $module,
                "controller" => $controller,
                "description" => $description,
                "description" => $description,
                "permits" => $arrayPermits,
                "status" => 1,
                "created_at" => $fechaNow,
                "created_by" => $user,
                "updated_at" => $fechaNow,
                "updated_by" => $user);

            $GeneralConfiguration->setModules($arrayModules);

            $dm = $this->get('doctrine_mongodb')->getManager();
//            $dm->persist($GeneralConfiguration);
            $dm->flush();

            $this->addFlash('notice', 'Module Updated');

            return $this->redirectToRoute('modules_details', array('id' => $id, 'positionModule' => $positionModule));
        }

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $modules = $GeneralConfiguration->getModules();
        $methodArray = $GeneralConfiguration->getMethods();
        $permitsArray = $GeneralConfiguration->getPermits();
        return $this->render('@App/general_configuration/edit.html.twig', array('modules' => $modules, 'id' => $id, 'positionModule' => $positionModule, 'methodArray' => $methodArray, 'permitsArray' => $permitsArray));
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
        $controller = $arrayModules[$positionModule]["controller"];
        $description = $arrayModules[$positionModule]["description"];
        $permits = $arrayModules[$positionModule]["permits"];
        $statusModule = $arrayModules[$positionModule]["status"];
        //var_dump($permits);
        return $this->render('@App/general_configuration/details.html.twig', array('id' => $id, 'name' => $name, 'controller' => $controller, 'statusModule' => $statusModule, 'permits' => $permits, 'description' => $description, 'positionModule' => $positionModule));
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
