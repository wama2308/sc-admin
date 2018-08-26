<?php

namespace AppBundle\Controller;

use AppBundle\Document\MedicalCenter;
use AppBundle\Document\Country;
use AppBundle\Document\GeneralConfiguration;
use AppBundle\Document\License;
use AppBundle\Document\UsersFront;
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
use Symfony\Component\HttpFoundation\JsonResponse;

class MedicalCenterController extends Controller {

    /**
     * @Route("/medical_center/index", name="medical_center_list")
     * @Method("GET")
     */
    public function listAction(Request $request) {

        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findByActive(true);
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findAll();
        //var_dump($countries);

        return $this->render('@App/medical_center/index.html.twig', array('countries' => $countries, 'medicalcenter' => $medicalcenter));
    }

    /**
     * @Route("/medical_center/create", name="medical_center_create")
     * @Method({"GET", "POST"})
     */
    public function createAction(Request $request) {

        $form = $this->createFormBuilder()->getForm();
        $user = $this->getUser()->getId();

        $form->handleRequest($request);
        $country = $request->request->get("country");
        $province = $request->request->get("province");
        $name = $request->request->get("name");
        $code = $request->request->get("code");
//        $typemedicalcenter = $request->request->get("typemedicalcenter");
//        $address = $request->request->get("address");
//
//        $phone = $request->request->get("phone");
//        $arrayPhone = explode(",", $phone);
        $master = $request->request->get("master");

//        $contac1 = $request->request->get("contac1");
//
//        $telephonecontact1 = $request->request->get("telephonecontact1");
//        $arraytelephonecontact1 = explode(",", $telephonecontact1);

        $countLicenses = $request->request->get("countLicenses");
        $countPayments = $request->request->get("countPayments");
        $insertFrontUser = $request->request->get("insertFrontUser");

        if (($country != "") && ($province != "") && ($name != "") && ($code != "") && ($master != "") && ($countPayments != "0") && ($countLicenses != "0")) {

            $fechaNow = new \MongoDate();

            $medicalcenter = new MedicalCenter();

            $medicalcenter->setCountryid($country);
            $medicalcenter->setProvinceid($province);
            $medicalcenter->setName($name);
            $medicalcenter->setCode($code);
//            $medicalcenter->setType($typemedicalcenter);
//            $medicalcenter->setAddress($address);
//            $medicalcenter->setPhone($arrayPhone);

            $arrayMaster[] = array(
                "email" => $master,
                "validation_code" => "",
                "status" => "0",
                "created_at" => $fechaNow);
            $medicalcenter->setMaster($arrayMaster);

//            $medicalcenter->setContac1($contac1);
//            $medicalcenter->setContac1phone($arraytelephonecontact1);

            for ($i = 0; $i < $countLicenses; $i++) {

                $days = $request->request->get("durationTime_" . $i);
                $fecha = date('Y-m-d h:i:s');
                $nuevafecha = strtotime('+' . $days . 'day', strtotime($fecha));
                $nuevafecha = date('Y-m-d h:i:s', $nuevafecha);
                $fecha_expiration = new \MongoDate(strtotime($nuevafecha));

                ////////////////////////////////////////////////////////////////
                $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
                $arrayModules = $GeneralConfiguration->getModules();
                $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($request->request->get("licenseId_" . $i));
                $modules = $license->getModules();
                $acum = 0;

                foreach ($arrayModules as $arrayModule) {

                    foreach ($modules as $module) {
                        if ($arrayModule["_id"] == $module) {
                            $arrayModulesInsert[] = array(
                                "name" => $arrayModule["name"],
                                "controller" => $arrayModule["controller"],
                                "permits" => $arrayModule["permits"]);
                        }
                    }
                }
                ////////////////////////////////////////////////////////////////

                $arrayLicenses[] = array(
                    "license_id" => $request->request->get("licenseId_" . $i),
                    "expiration_date" => $fecha_expiration,
                    "status" => "Active",
                    "modules" => $arrayModulesInsert,
                    "created_at" => $fechaNow,
                    "created_by" => $user,
                    "updated_at" => $fechaNow,
                    "updated_by" => $user);

                unset($arrayModulesInsert);
            }

            $medicalcenter->setLicenses($arrayLicenses);

            for ($i = 0; $i < $countPayments; $i++) {

                $arrayPayments[] = array(
                    "waytopay" => $request->request->get("waytopay_" . $i),
                    "daystopay" => $request->request->get("daystopay_" . $i),
                    "amount" => doubleval($request->request->get("amount_" . $i)),
                    "issuingbank" => $request->request->get("issuingbank_" . $i),
                    "receivingbank" => $request->request->get("receivingbank_" . $i),
                    "cardholder" => $request->request->get("cardholder_" . $i),
                    "cardnumber" => $request->request->get("cardnumber_" . $i),
                    "expiration" => $request->request->get("expiration_" . $i),
                    "cvv" => $request->request->get("cvv_" . $i),
                    "files4" => $request->request->get("files4_" . $i),
                    "operationnumber" => $request->request->get("operationnumber_" . $i),
                    "status" => true);
            }
            $amountTotal = $request->request->get("amountTotal");
            $total = $request->request->get("total");
            if ($amountTotal == $total) {
                $estatusPayments = "Paid out";
            } else {
                $estatusPayments = "To paid";
            }

            $medicalcenter->setPayments($arrayPayments);
            $medicalcenter->setPaymentstatus($estatusPayments);

            $medicalcenter->setActive(true);
            $medicalcenter->setCreatedAt($fechaNow);
            $medicalcenter->setCreatedBy($user);
            $medicalcenter->setUpdatedAt($fechaNow);
            $medicalcenter->setUpdatedBy($user);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($medicalcenter);
            $dm->flush();

//          FUNCION PARA AGREGAR LOS DATOS EN LA COLECCION USERSFRONT EN CASO DE QUE EL CORREO SE REPITA Y EL USUARIO ACEPTE
            if ($insertFrontUser == "1") {
                $medicalCenterLatest = $this->get('doctrine_mongodb')
                        ->getManager()
                        ->createQueryBuilder('AppBundle:MedicalCenter')
                        ->limit(1)
                        ->sort('$natural', '-1')
                        ->getQuery()
                        ->execute();
                $latestIdMedicalCenter = "";
                foreach ($medicalCenterLatest as $medicalCenterLatest) {
                    $latestIdMedicalCenter = $medicalCenterLatest->getId();
                }

                $emailUserFront = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $master));
                $idUserFornt = $emailUserFront[0]->getId();
                $userFront = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($idUserFornt);
                $arrayMedicalCenterUsersFront = $userFront->getMedicalCenter();

                //CREACION DE LOS ARRAYS
                $arrayPermission[] = array(
                    '_id' => 'MASTER',
                    'type' => 0
                );

                $arrayBranchOffice[] = array(
                    '_id' => 0,
                    'name' => 'Sucursal',
                    'is_default' => 0,
                    'permission' => $arrayPermission
                );

                $arrayMedicalCenterUsersFront[] = array(
                    "_id" => $latestIdMedicalCenter,
                    "name" => $name,
                    "is_default" => 0,
                    "branch_office" => $arrayBranchOffice,
                    "created_at" => $fechaNow,
                    "created_by" => $user,
                    "updated_at" => $fechaNow,
                    "updated_by" => $user);

                $userFront->setMedicalCenter($arrayMedicalCenterUsersFront);

                $dm->persist($userFront);
                $dm->flush();
            }
            
            $this->addFlash('notice', 'Registered Medical Center');
            return $this->redirectToRoute('medical_center_list');
        }

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayTypeMedicalCenter = $GeneralConfiguration->getTypemedicalcenter();
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));

        return $this->render('@App/medical_center/create.html.twig', array('ArrayTypeMedicalCenter' => $ArrayTypeMedicalCenter, 'countries' => $countries));
    }

    /**
     * @Route("/medical_center/edit/{id}", name="medical_center_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction($id, Request $request) {

        $fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();

        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
        $countryId = $medicalcenter->getCountryid();
        $countBranches = count($medicalcenter->getBranchoffices());
        $countLicensesMc = count($medicalcenter->getLicenses());
        $countPaymenst = count($medicalcenter->getPayments());

        $country = $request->request->get("country");
        $province = $request->request->get("province");
        $name = $request->request->get("name");
        $code = $request->request->get("code");
//        $typemedicalcenter = $request->request->get("typemedicalcenter");
//        $address = $request->request->get("address");
//        $phone = $request->request->get("phone");
//        $arrayPhone = explode(",", $phone);
        $master = $request->request->get("master");

//        $contac1 = $request->request->get("contac1");
//        $telephonecontact1 = $request->request->get("telephonecontact1");
//        $arraytelephonecontact1 = explode(",", $telephonecontact1);

        $countLicenses = $request->request->get("countLicenses");
        $countPayments = $request->request->get("countPayments");

        if (($country != "") && ($province != "") && ($name != "") && ($code != "") && ($master != "") && ($countPayments != "0") && ($countLicenses != "0")) {

            $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);

            $medicalcenter->setCountryid($country);
            $medicalcenter->setProvinceid($province);
            $medicalcenter->setName($name);
            $medicalcenter->setCode($code);
//            $medicalcenter->setType($typemedicalcenter);
//            $medicalcenter->setAddress($address);
//            $medicalcenter->setPhone($arrayPhone);

            $arrayMaster[] = array(
                "email" => $master,
                "validation_code" => "",
                "status" => "0",
                "created_at" => $fechaNow);
            $medicalcenter->setMaster($arrayMaster);

//            $medicalcenter->setContac1($contac1);
//            $medicalcenter->setContac1phone($arraytelephonecontact1);

            for ($i = 0; $i < $countLicenses; $i++) {

                $days = $request->request->get("durationTime_" . $i);
                $fecha = date('Y-m-d h:i:s');
                $nuevafecha = strtotime('+' . $days . 'day', strtotime($fecha));
                $nuevafecha = date('Y-m-d h:i:s', $nuevafecha);
                $fecha_expiration = new \MongoDate(strtotime($nuevafecha));

                if ($request->request->get("statusRegisterLicense_" . $i) == "2") {
                    $status = "Inactive";
                } else {
                    $status = "Active";
                }

                ////////////////////////////////////////////////////////////////
                $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
                $arrayModules = $GeneralConfiguration->getModules();
                $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($request->request->get("licenseId_" . $i));
                $modules = $license->getModules();
                $acum = 0;

                foreach ($arrayModules as $arrayModule) {

                    foreach ($modules as $module) {
                        if ($arrayModule["_id"] == $module) {
                            $arrayModulesInsert[] = array(
                                "name" => $arrayModule["name"],
                                "controller" => $arrayModule["controller"],
                                "permits" => $arrayModule["permits"]);
                        }
                    }
                }
                ////////////////////////////////////////////////////////////////

                $arrayLicenses[] = array(
                    "license_id" => $request->request->get("licenseId_" . $i),
                    "expiration_date" => $fecha_expiration,
                    "status" => $status,
                    "modules" => $arrayModulesInsert,
                    "created_at" => $fechaNow,
                    "created_by" => $user,
                    "updated_at" => $fechaNow,
                    "updated_by" => $user);

                unset($arrayModulesInsert);
            }
            $medicalcenter->setLicenses($arrayLicenses);

            for ($i = 0; $i < $countPayments; $i++) {

                if ($request->request->get("statusRegisterPayment_" . $i) == "2") {
                    $status = false;
                } else {
                    $status = true;
                }

                $arrayPayments[] = array(
                    "waytopay" => $request->request->get("waytopay_" . $i),
                    "daystopay" => $request->request->get("daystopay_" . $i),
                    "amount" => doubleval($request->request->get("amount_" . $i)),
                    "issuingbank" => $request->request->get("issuingbank_" . $i),
                    "receivingbank" => $request->request->get("receivingbank_" . $i),
                    "cardholder" => $request->request->get("cardholder_" . $i),
                    "cardnumber" => $request->request->get("cardnumber_" . $i),
                    "expiration" => $request->request->get("expiration_" . $i),
                    "cvv" => $request->request->get("cvv_" . $i),
                    "files4" => $request->request->get("files4_" . $i),
                    "operationnumber" => $request->request->get("operationnumber_" . $i),
                    "status" => $status);
            }
            $amountTotal = $request->request->get("amountTotal");
            $total = $request->request->get("total");
            if ($amountTotal == $total) {
                $estatusPayments = "Paid out";
            } else {
                $estatusPayments = "To pay";
            }

            $medicalcenter->setPayments($arrayPayments);
            $medicalcenter->setPaymentstatus($estatusPayments);

            $medicalcenter->setUpdatedAt($fechaNow);
            $medicalcenter->setUpdatedBy($user);

            $dm = $this->get('doctrine_mongodb')->getManager();
            //$dm->persist($medicalcenter);
            $dm->flush();
            $this->addFlash('notice', 'Medical Center Updated');

            return $this->redirectToRoute('medical_center_details', array('id' => $id));
        }

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayTypeMedicalCenter = $GeneralConfiguration->getTypemedicalcenter();
        $ArraySectorMedicalCenter = $GeneralConfiguration->getSectormedicalcenter();
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));
        $licenses = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->findByActive(true);

        return $this->render('@App/medical_center/edit.html.twig', array('medicalcenter' => $medicalcenter, 'ArrayTypeMedicalCenter' => $ArrayTypeMedicalCenter, 'ArraySectorMedicalCenter' => $ArraySectorMedicalCenter, 'countries' => $countries, 'countBranches' => $countBranches, 'countLicensesMc' => $countLicensesMc, 'countPaymenst' => $countPaymenst, 'licenses' => $licenses));
    }

    /**
     * @Route("/medical_center/details/{id}", name="medical_center_details")
     * @Method("GET")
     */
    public function detailsAction($id) {
        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayTypeMedicalCenter = $GeneralConfiguration->getTypemedicalcenter();
        $ArraySectorMedicalCenter = $GeneralConfiguration->getSectormedicalcenter();
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));
        $licenses = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->findByActive(true);
        return $this->render('@App/medical_center/details.html.twig', array('medicalcenter' => $medicalcenter, 'ArrayTypeMedicalCenter' => $ArrayTypeMedicalCenter, 'countries' => $countries, 'licenses' => $licenses));
    }

    /**
     * @Route("/medical_center/delete/{id}", name="medical_center_delete")
     * @Method("GET")
     */
    public function deleteAction($id) {

        $dm = $this->get('doctrine_mongodb')->getManager();
        $medicalcenter = $dm->getRepository('AppBundle:MedicalCenter')->find($id);

        $medicalcenter->setActive(false);

        $dm->flush();

        $this->addFlash('error', 'Medical Center Removed');

        return $this->redirectToRoute('medical_center_details', array('id' => $id));
    }

    /**
     * @Route("/medical_center/ajax", name="ajax")
     * @Method({"GET", "POST"})
     */
    public function LoadProvince(Request $request) {

        if ($request->isXmlHttpRequest()) {
            $country_id = $request->request->get('valor_id');
            $opcion = $request->request->get('opcion');
            $valoredit = $request->request->get('valoredit');
            if (($opcion == 1) || ($opcion == 2)) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $arrayProvince = $country->getProvinces();
                return $this->render('@App/medical_center/loadProvince.html.twig', array('arrayProvince' => $arrayProvince, 'opcion' => $opcion));
            } else if ($opcion == 4) {
                $valor_country = $request->request->get('valor_country');
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($valor_country);
                $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($country_id);
                return $this->render('@App/medical_center/loadProvince.html.twig', array('license' => $license, 'country' => $country, 'opcion' => $opcion));
            } else if ($opcion == 5) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $waytopay = $country->getWaytopay();
                $issuingbank = $country->getIssuingbank();
                $receivingbank = $country->getReceivingbank();
                return $this->render('@App/medical_center/loadProvince.html.twig', array('waytopay' => $waytopay, 'opcion' => $opcion));
            } else if ($opcion == 6) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $issuingbank = $country->getIssuingbank();
                return $this->render('@App/medical_center/loadProvince.html.twig', array('issuingbank' => $issuingbank, 'opcion' => $opcion));
            } else if ($opcion == 7) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $receivingbank = $country->getReceivingbank();
                return $this->render('@App/medical_center/loadProvince.html.twig', array('receivingbank' => $receivingbank, 'opcion' => $opcion));
            } else if ($opcion == 20) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $arrayProvince = $country->getProvinces();
                return $this->render('@App/medical_center/loadProvince.html.twig', array('arrayProvince' => $arrayProvince, 'opcion' => $opcion, 'valoredit' => $valoredit));
            }
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/medical_center/ajax_", name="ajax_")
     * @Method({"GET", "POST"})
     */
    public function LoadLicenses(Request $request) {

        if ($request->isXmlHttpRequest()) {
            $country_id = $request->request->get('valor_id');
            $opcion = $request->request->get('opcion');
            $valoredit = $request->request->get('valoredit');
            if ($opcion == 3) {
                $licensecountry = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->findBy(array('countries' => $country_id));
                return $this->render('@App/medical_center/loadProvince.html.twig', array('licensecountry' => $licensecountry, 'opcion' => $opcion));
            }
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/medical_center/images", name="images")
     * @Method({"GET", "POST"})
     */
    public function LoadImages(Request $request) {

        if ($request->isXmlHttpRequest()) {

            //var_dump($request->files->get('files4'));
            $opcion = "8";
            $countPayments = $request->request->get("countPayments");
            $files2 = $request->files->get("files4");
            $ext2 = $files2->guessExtension();
            $image1 = 'wama_' . time() . "." . $ext2;
            $files2->move("uploads", $image1);
            return $this->render('@App/medical_center/loadProvince.html.twig', array('image1' => $image1, 'opcion' => $opcion, 'countPayments' => $countPayments));
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/medical_center/deletebranchofficess", name="deletebranchofficess")
     * @Method({"GET", "POST"})
     */
    public function deleteBranchOfficess(Request $request) {

        if ($request->isXmlHttpRequest()) {

            $medicalcenterid = $request->request->get("medicalcenterid");
            $position = $request->request->get("position");
            $opcion = $request->request->get("opcion");
            $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalcenterid);
            $arrayBranchOfficess = $medicalcenter->getBranchoffices();
            $arrayBranchOfficess[$position]["active"] = false;
            $medicalcenter->setBranchoffices($arrayBranchOfficess);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($medicalcenter);
            $dm->flush();

            //$this->addFlash('error', 'Province Removed');
            return $this->render('@App/medical_center/loadProvince.html.twig', array('opcion' => $opcion));
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/medical_center/deletelicense", name="deletelicense")
     * @Method({"GET", "POST"})
     */
    public function deleteLicense(Request $request) {

        if ($request->isXmlHttpRequest()) {

            $medicalcenterid = $request->request->get("medicalcenterid");
            $position = $request->request->get("position");
            $opcion = $request->request->get("opcion");
            $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalcenterid);
            $arrayLicenses = $medicalcenter->getLicenses();
            $arrayLicenses[$position]["status"] = "Inactive";
            $medicalcenter->setLicenses($arrayLicenses);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($medicalcenter);
            $dm->flush();

            //$this->addFlash('error', 'Province Removed');
            return $this->render('@App/medical_center/loadProvince.html.twig', array('opcion' => $opcion));
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/medical_center/deletepayment", name="deletepayment")
     * @Method({"GET", "POST"})
     */
    public function deletePayment(Request $request) {

        if ($request->isXmlHttpRequest()) {

            $medicalcenterid = $request->request->get("medicalcenterid");
            $position = $request->request->get("position");
            $opcion = $request->request->get("opcion");
            $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalcenterid);
            $arrayPayment = $medicalcenter->getPayments();
            $arrayPayment[$position]["status"] = false;
            $medicalcenter->setPayments($arrayPayment);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($medicalcenter);
            $dm->flush();

            //$this->addFlash('error', 'Province Removed');
            return $this->render('@App/medical_center/loadProvince.html.twig', array('opcion' => $opcion));
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/medical_center/validateEmail", name="validateEmail")
     * @Method({"GET", "POST"})
     */
    public function validateEmail(Request $request) {

        if ($request->isXmlHttpRequest()) {

            $email = $request->request->get("email");
            $opcion = $request->request->get("opcion");
            $answer = 0;
            $EmailExist = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('master.email' => $email));

            if ($EmailExist) {
                $answer = 1;
                return $this->render('@App/medical_center/loadProvince.html.twig', array('opcion' => $opcion, 'answer' => $answer));
            } else {
                $answer = 2;
                return $this->render('@App/medical_center/loadProvince.html.twig', array('opcion' => $opcion, 'answer' => $answer));
            }



            //$this->addFlash('error', 'Province Removed');
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/medical_center/validateEmailUserFront", name="validateEmailUserFront")
     * @Method({"GET", "POST"})
     */
    public function validateEmailUserFront(Request $request) {

        if ($request->isXmlHttpRequest()) {

            $email = $request->request->get("email");
            $opcion = $request->request->get("opcion");
            $valorCampo;
            $EmailExist = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $email));
//            var_dump($EmailExist);
            if ($EmailExist) {
                $valorCampo = 1;
                $medicalCenter = $EmailExist[0]->getMedicalCenter();
                return $this->render('@App/medical_center/loadProvince.html.twig', array('opcion' => $opcion, 'valorCampo' => $valorCampo, 'medicalCenter' => $medicalCenter));
            } else {
                $valorCampo = 0;
                return $this->render('@App/medical_center/loadProvince.html.twig', array('opcion' => $opcion, 'valorCampo' => $valorCampo));
            }

//            return $this->render('@App/medical_center/loadProvince.html.twig', array('opcion' => $opcion));                
            //$this->addFlash('error', 'Province Removed');
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

}
