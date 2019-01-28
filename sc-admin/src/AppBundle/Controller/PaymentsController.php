<?php

namespace AppBundle\Controller;

use AppBundle\Document\MedicalCenter;
use AppBundle\Document\Country;
use AppBundle\Document\GeneralConfiguration;
use AppBundle\Document\License;
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

class PaymentsController extends Controller {

    /**
     * @Route("/payments/index", name="payments_list")     
     * @Method("GET")
     */
    public function listAction(Request $request) {

//        $p = "Funda";
//        $b = "/^$p/";
//        $a = new \MongoRegex($b);
//        $medicalcenter1 = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('name' => $a));
//        var_dump($medicalcenter1);
        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findByActive(true);
        $province = "";

        foreach ($medicalcenter as $medicalcenter) {
            $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($medicalcenter->getCountryid());

            $province = "";
            foreach ($country->getProvinces() as $key => $value) {

                if ($key == $medicalcenter->getProvinceid()) {
                    $province = $value['name'];
                }
            }
            $arrayMedicalCenter[] = $medicalcenter->getName() . " / " . $country->getName() . " / " . $province;
        }

        $medicalcenter_ = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findByActive(true);

        return $this->render('@App/payments/index.html.twig', array('medicalcenter_' => $medicalcenter_, 'arrayMedicalCenter' => $arrayMedicalCenter));
    }

    /**
     * @Route("/payment/create/{id}", name="payment_create")
     * @Method({"GET", "POST"})
     */
    public function createPaymentAction($id, Request $request) {

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();
        $countPayments = $request->request->get("countPayments");
        $medicalcenterPayment = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);

        if ($countPayments != "") {

            $medicalcenterPayment = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
            $arrayPaymentsNew = $medicalcenterPayment->getPayments();
            for ($i = 0; $i < $countPayments; $i++) {

                $arrayPaymentsNew[] = array(
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
                $estatusPayments = "To pay";
            }
//            var_dump($arrayPaymentsNew);
            $medicalcenterPayment->setPayments($arrayPaymentsNew);
            $medicalcenterPayment->setPaymentstatus($estatusPayments);

            $medicalcenterPayment->setUpdatedAt($fechaNow);
            $medicalcenterPayment->setUpdatedBy($user);

            $dm = $this->get('doctrine_mongodb')->getManager();
//            $dm->persist($medicalcenter);
            $dm->flush();
            $this->addFlash('notice', 'Registered Payment');
            return $this->redirectToRoute('payments_details', array('id' => $id));
        }

        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));
        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
        $country_id = $medicalcenter->getCountryid();
        $acumPayments = 0;
        $acumLicenses = 0;
        $acumRemaining = 0;

        $arrayLicenses = $medicalcenter->getLicenses();

        foreach ($arrayLicenses as $arrayLicenses) {
            if ($arrayLicenses['status'] == "Active") {

                $renovation = "";
                $acumRenovation = 0;
                if (isset($arrayLicenses['renovation'])) {
                    $renovation = $arrayLicenses['renovation'];
                    foreach ($renovation as $renovation) {
                        $acumRenovation = $acumRenovation + $renovation['amount'];
                    }
                } else {
                    $renovation = "";
                }

                $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);

                $acumLicenses = $acumLicenses + $acumRenovation + $licensesData->getAmount();
            }
        }


        foreach ($medicalcenter->getPayments() as $arrayPayments) {
            if ($arrayPayments['status'] == true) {
                $acumPayments = $acumPayments + $arrayPayments['amount'];
            }
        }

        $acumRemaining = $acumLicenses - $acumPayments;
        return $this->render('@App/payments/createPayments.html.twig', array('country_id' => $country_id, 'id' => $id, 'acumRemaining' => $acumRemaining, 'acumPayments' => $acumPayments, 'acumLicenses' => $acumLicenses));
    }

    /**
     * @Route("/payment/edit/{id}/{position}", name="payment_edit")
     * @Method({"GET", "POST"})
     */
    public function editPaymentAction($id, $position, Request $request) {
        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();
        $countPayments = $request->request->get("countPayments");
        $waytopay = $request->request->get("waytopay");
        $paymenttype = $request->request->get("paymenttype");
        $daystopay = $request->request->get("daystopay");
        $amount = doubleval($request->request->get("amount"));
        $issuingbank = $request->request->get("issuingbank");
        $receivingbank = $request->request->get("receivingbank");
        $cardholder = $request->request->get("cardholder");
        $cardnumber = $request->request->get("cardnumber");
        $expiration = $request->request->get("expiration");
        $cvv = $request->request->get("cvv");
        $inputFile4Base64 = $request->request->get("inputFile4Base64");
        $operationnumber = $request->request->get("operationnumber");
        $medicalcenterPayment = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);

        $issuingbankData = ($issuingbank == null) ? "Any" : $issuingbank;
        $receivingbankData = ($receivingbank == null) ? "Any" : $receivingbank;
        $operationnumberData = ($operationnumber == null) ? "" : $operationnumber;


        if (($waytopay != "") || ($paymenttype != "") || ($amount != "")) {

            $medicalcenterPayment = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
            $arrayPaymentsNew = $medicalcenterPayment->getPayments();
            $estatusPayments = $medicalcenterPayment->getPaymentstatus();

            $arrayPaymentsNew[$position]["waytopay"] = $waytopay;
            $arrayPaymentsNew[$position]["daystopay"] = $daystopay;
            $arrayPaymentsNew[$position]["amount"] = $amount;
            $arrayPaymentsNew[$position]["issuingbank"] = $issuingbankData;
            $arrayPaymentsNew[$position]["receivingbank"] = $receivingbankData;
            $arrayPaymentsNew[$position]["cardholder"] = $cardholder;
            $arrayPaymentsNew[$position]["cardnumber"] = $cardnumber;
            $arrayPaymentsNew[$position]["expiration"] = $expiration;
            $arrayPaymentsNew[$position]["cvv"] = $cvv;
            $arrayPaymentsNew[$position]["files4"] = $inputFile4Base64;
            $arrayPaymentsNew[$position]["operationnumber"] = $operationnumberData;

            $amountTotal = $request->request->get("amountTotal");
            $total = $request->request->get("totalEdit") + $request->request->get("amount");

            if ($amountTotal == $total) {
                $estatusPayments = "Paid out";
            } else {
                $estatusPayments = "To pay";
            }
//            var_dump($estatusPayments);
            $medicalcenterPayment->setPayments($arrayPaymentsNew);
            $medicalcenterPayment->setPaymentstatus($estatusPayments);

            $medicalcenterPayment->setUpdatedAt($fechaNow);
            $medicalcenterPayment->setUpdatedBy($user);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($medicalcenterPayment);
            $dm->flush();
            $this->addFlash('notice', 'Updated Payment');
            return $this->redirectToRoute('payments_details', array('id' => $id));
        }

        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));
        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
        $country_id = $medicalcenter->getCountryid();
        $acumPayments = 0;
        $acumLicenses = 0;
        $acumRemaining = 0;
        $valuePayments = $medicalcenter->getPayments();

        $arrayLicenses = $medicalcenter->getLicenses();

        foreach ($arrayLicenses as $arrayLicenses) {
            if ($arrayLicenses['status'] == "Active") {

                $renovation = "";
                $acumRenovation = 0;
                if (isset($arrayLicenses['renovation'])) {
                    $renovation = $arrayLicenses['renovation'];
                    foreach ($renovation as $renovation) {
                        $acumRenovation = $acumRenovation + $renovation['amount'];
                    }
                } else {
                    $renovation = "";
                }

                $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);
                $acumLicenses = $acumLicenses + $acumRenovation + $licensesData->getAmount();
            }
        }


        foreach ($medicalcenter->getPayments() as $arrayPayments) {
            if ($arrayPayments['status'] == true) {
                $acumPayments = $acumPayments + $arrayPayments['amount'];
            }
        }

        $acumRemaining = $acumLicenses - $acumPayments;

        return $this->render('@App/payments/editPayments.html.twig', array('country_id' => $country_id, 'id' => $id, 'acumRemaining' => $acumRemaining, 'acumPayments' => $acumPayments, 'acumLicenses' => $acumLicenses, 'valuePayments' => $valuePayments, 'position' => $position));
    }

    /**
     * @Route("/licensePayment/create/{id}", name="license_payment_create")
     * @Method({"GET", "POST"})
     */
    public function createLicenseAction($id, Request $request) {

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();
        $countLicenses = $request->request->get("countLicenses");
        $countPayments = $request->request->get("countPayments");
        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);

        if (($countPayments != "") || ($countLicenses != "")) {

            $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
            $arrayLicenses = $medicalcenter->getLicenses();
            $arrayPayments = $medicalcenter->getPayments();

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

            $medicalcenter->setUpdatedAt($fechaNow);
            $medicalcenter->setUpdatedBy($user);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($medicalcenter);
            $dm->flush();

            $this->addFlash('notice', 'Registered License');
            return $this->redirectToRoute('payments_details', array('id' => $id));
        }

        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));
        $medicalcenterData = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
        $country_id = $medicalcenterData->getCountryid();
        $acumPayments = 0;
        $acumLicenses = 0;
        $acumRemaining = 0;

        $arrayLicenses = $medicalcenterData->getLicenses();

        foreach ($arrayLicenses as $arrayLicenses) {
            if ($arrayLicenses['status'] == "Active") {
                $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);
                $acumLicenses = $acumLicenses + $licensesData->getAmount();
            }
        }


        foreach ($medicalcenterData->getPayments() as $arrayPayments) {
            if ($arrayPayments['status'] == true) {
                $acumPayments = $acumPayments + $arrayPayments['amount'];
            }
        }

        $acumRemaining = $acumLicenses - $acumPayments;
        return $this->render('@App/payments/createLicense.html.twig', array('country_id' => $country_id, 'id' => $id, 'acumRemaining' => $acumRemaining, 'acumPayments' => $acumPayments, 'acumLicenses' => $acumLicenses));
    }

    /**
     * @Route("/licenseRenovate/renovate/{id}/{licenseId}/{position}", name="license_renovate")
     * @Method({"GET", "POST"})
     */
    public function renovateLicenseAction($id, $licenseId, $position, Request $request) {

        $form = $this->createFormBuilder()->getForm();
        $form->handleRequest($request);
        $fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();

        $countPayments = $request->request->get("countPayments");
        $amountData = $request->request->get("amountData");
        $dueDateData = $request->request->get("dueDateData");
        $dueDateDataFormat = date($request->request->get("dueDateData"));
        $dueDateDataMongo = new \MongoDate(strtotime($dueDateDataFormat));
        $durationData = $request->request->get("durationData");
        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);

        if ($countPayments != "") {

            $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
            $arrayLicenses = $medicalcenter->getLicenses();
            $arrayPayments = $medicalcenter->getPayments();

            $fecha = date($dueDateData);
            $nuevafecha = strtotime('+' . $durationData . 'day', strtotime($fecha));
            $nuevafecha = date('Y-m-d h:i:s', $nuevafecha);
            $fecha_expiration = new \MongoDate(strtotime($nuevafecha));

            $arrayRenovation[] = array(
                "previous_amount" => doubleval($amountData),
                "amount" => doubleval($amountData),
                "previous_due_date" => $dueDateDataMongo,
                "due_date" => $fecha_expiration,
                "created_at" => $fechaNow,
                "created_by" => $user);

            $arrayLicenses[$position]["expiration_date"] = $fecha_expiration;
            $arrayLicenses[$position]["renovation"] = $arrayRenovation;

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

            $medicalcenter->setUpdatedAt($fechaNow);
            $medicalcenter->setUpdatedBy($user);

            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($medicalcenter);
            $dm->flush();
//
            $this->addFlash('notice', 'Renewed License');
            return $this->redirectToRoute('payments_details', array('id' => $id));
        }

        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));
        $medicalcenterData = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
        $country_id = $medicalcenterData->getCountryid();
        $acumPayments = 0;
        $acumLicenses = 0;
        $acumRemaining = 0;

        $arrayLicenses = $medicalcenterData->getLicenses();

        $licenseMedical = $medicalcenterData->getLicenses();
        $expiratinLicense = $licenseMedical[$position]["expiration_date"];
        $licensesInfo = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($licenseId);

        foreach ($arrayLicenses as $arrayLicenses) {
            if ($arrayLicenses['status'] == "Active") {

                $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);
                $acumLicenses = $acumLicenses + $licensesData->getAmount();
            }
        }


        foreach ($medicalcenterData->getPayments() as $arrayPayments) {
            if ($arrayPayments['status'] == true) {
                $acumPayments = $acumPayments + $arrayPayments['amount'];
            }
        }

        $acumRemaining = $acumLicenses - $acumPayments;
        return $this->render('@App/payments/renovateLicense.html.twig', array('country_id' => $country_id, 'id' => $id, 'acumRemaining' => $acumRemaining, 'acumPayments' => $acumPayments, 'acumLicenses' => $acumLicenses, 'arrayLicenses' => $arrayLicenses, 'licensesInfo' => $licensesInfo, 'position' => $position, 'expiratinLicense' => $expiratinLicense));
    }

    /**
     * @Route("/payments/LicensePayment", name="license_payment")
     * @Method({"GET", "POST"})
     */
    public function createLicensePaymentAction(Request $request) {

        $form = $this->createFormBuilder()->getForm();
        //$fechaNow = new \MongoDate();
        $user = $this->getUser()->getId();

        $form->handleRequest($request);

        $countLicenses = $request->request->get("countLicenses");
        $countPayments = $request->request->get("countPayments");
        $medicalCenterId = $request->request->get("medicalCenterId");

        if (($countPayments != "0") && ($countLicenses != "0")) {
//        if ($countLicenses != "0") {
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

            $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
            $arrayLicenses = $medicalcenter->getLicenses();
            $arrayPayments = $medicalcenter->getPayments();

            for ($i = 0; $i < $countLicenses; $i++) {

                $register = $request->request->get("register_" . $i);
                $days = $request->request->get("durationTime_" . $i);

                if ($register == "1") {

                    $amountData = $request->request->get("amountLic_" . $i);
                    $dueDateData = $request->request->get("dueDate_" . $i);
                    $dueDateDataFormat = date($request->request->get("dueDate_" . $i));
                    $dueDateDataMongo = new \MongoDate(strtotime($dueDateDataFormat));
                    $durationData = $request->request->get("durationTime_" . $i);

                    $fecha = date($dueDateData);
                    $nuevafecha = strtotime('+' . $durationData . 'day', strtotime($fecha));
                    $nuevafecha = date('Y-m-d h:i:s', $nuevafecha);
                    $fecha_expiration = new \MongoDate(strtotime($nuevafecha));

                    $arrayRenovation[] = array(
                        "previous_amount" => doubleval($amountData),
                        "amount" => doubleval($amountData),
                        "previous_due_date" => $dueDateDataMongo,
                        "due_date" => $fecha_expiration,
                        "created_at" => $fechaNow,
                        "created_by" => $user);

                    $arrayLicenses[$i]["expiration_date"] = $fecha_expiration;
                    $arrayLicenses[$i]["renovation"] = $arrayRenovation;

                    $medicalcenter->setLicenses($arrayLicenses);

                    $dm = $this->get('doctrine_mongodb')->getManager();
                    $dm->flush();
                } else {


                    $fecha = date('Y-m-d h:i:s');
                    $nuevafecha = strtotime('+' . $days . 'day', strtotime($fecha));
                    $nuevafecha = date('Y-m-d h:i:s', $nuevafecha);
                    $fecha_expiration = new \MongoDate(strtotime($nuevafecha));
                    $status = "Active";

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

                    $medicalcenter->setLicenses($arrayLicenses);

                    $dm = $this->get('doctrine_mongodb')->getManager();
                    $dm->flush();
                }
            }

            for ($i = 0; $i < $countPayments; $i++) {

                $status = true;

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
            $dm->flush();
            $this->addFlash('notice', 'Registered Payment Medical Center');
            return $this->redirectToRoute('payments_list');
        }
    }

    /**
     * @Route("/payments/details/{id}", name="payments_details")
     * @Method("GET")
     */
    public function detailsAction($id) {
        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
        $arrayLicenses = $medicalcenter->getLicenses();

        foreach ($arrayLicenses as $arrayLicenses) {

            $renovation = "";
            if (isset($arrayLicenses['renovation'])) {
                $renovation = $arrayLicenses['renovation'];
            } else {
                $renovation = "";
            }

            $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);

            $arrayLicenseData[] = array(
                "idLicense" => $licensesData->getId(),
                "license" => $licensesData->getLicense(),
                "usersquantity" => $licensesData->getUsersquantity(),
                "numberclients" => $licensesData->getNumberclients(),
                "numberexams" => $licensesData->getNumberexams(),
                "durationtime" => $licensesData->getDurationtime(),
                "statusLicense" => $arrayLicenses['status'],
                "expiration_date" => $arrayLicenses['expiration_date'],
                "renovation" => $renovation,
                "amount" => $licensesData->getAmount());
//            }
        }

        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));
        return $this->render('@App/payments/details.html.twig', array('id' => $id, 'medicalcenter' => $medicalcenter, 'countries' => $countries, 'arrayLicenseData' => $arrayLicenseData));
    }

    /**
     * @Route("/payments/delete/{id}/{position}", name="payments_delete")
     * @Method("GET")
     */
    public function deleteAction($id, $position) {

        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
        $arrayPayment = $medicalcenter->getPayments();
        $arrayPayment[$position]["status"] = false;
        $medicalcenter->setPayments($arrayPayment);

        $dm = $this->get('doctrine_mongodb')->getManager();
        $dm->persist($medicalcenter);
        $dm->flush();

        $this->addFlash('error', 'Payment Removed');

//        return $this->redirectToRoute('payments_details', array('id' => $id));
        return $this->redirectToRoute('payments_list');
    }

    /**
     * @Route("/payments/loadPayments", name="load_payments")
     * @Method({"GET", "POST"})
     */
    public function LoadSelectPayments(Request $request) {

        if ($request->isXmlHttpRequest()) {
            $country_id = $request->request->get('valor_id');
            $opcion = $request->request->get('opcion');
            $waytopayEdit = $request->request->get('waytopay');
            $issuingbankEdit = $request->request->get('issuingbank');
            $receivingbankEdit = $request->request->get('receivingbank');
            $daystopayEdit = $request->request->get('daystopay');

            if ($opcion == 1) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $waytopay = $country->getWaytopay();
                return $this->render('@App/payments/loadPayments.html.twig', array('waytopay' => $waytopay, 'opcion' => $opcion, 'waytopayEdit' => $waytopayEdit, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 2) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $issuingbank = $country->getIssuingbank();
                return $this->render('@App/payments/loadPayments.html.twig', array('issuingbank' => $issuingbank, 'opcion' => $opcion, 'issuingbankEdit' => $issuingbankEdit, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 3) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $receivingbank = $country->getReceivingbank();
                return $this->render('@App/payments/loadPayments.html.twig', array('receivingbank' => $receivingbank, 'opcion' => $opcion, 'receivingbankEdit' => $receivingbankEdit, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 4) {
                $licensecountry = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->findBy(array('countries' => $country_id));
                return $this->render('@App/payments/loadPayments.html.twig', array('licensecountry' => $licensecountry, 'opcion' => $opcion, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 5) {
                $valor_country = $request->request->get('valor_country');
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($valor_country);
                $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($country_id);
                $days = $license->getDurationtime();
                $fecha = date('Y-m-d h:i:s');
                $nuevafecha = strtotime('+' . $days . 'day', strtotime($fecha));
                $nuevafecha = date('Y-m-d', $nuevafecha);
                return $this->render('@App/payments/loadPayments.html.twig', array('license' => $license, 'country' => $country, 'opcion' => $opcion, 'daystopayEdit' => $daystopayEdit, 'nuevafecha' => $nuevafecha));
            } else if ($opcion == 6) {
                $value = $country_id;
                $expression = "/$value/i";
                $regex = new \MongoRegex($expression);
                $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('name' => $regex));

                return $this->render('@App/payments/loadPayments.html.twig', array('medicalcenter' => $medicalcenter, 'opcion' => $opcion, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 7) {

                $infoMedical = $country_id;
                $porciones = explode(" / ", $infoMedical);
                $nameMedical = $porciones[0];

                $medicalcenterData = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('name' => $nameMedical));

                $countryId = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($medicalcenterData[0]->getCountryid());
                $currentSymbol = $countryId->getCurrencySymbol();

                $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalcenterData[0]->getId());
                $arrayLicenses = $medicalcenter->getLicenses();
                $duedate = "";
                $countLicense = count($arrayLicenses);
                if ($countLicense > 1) {
                    $separador = " | ";
                } else {
                    $separador = " ";
                }
                foreach ($arrayLicenses as $arrayLicenses) {
//            if ($arrayLicenses['status'] == "Active") {
//            var_dump(isset($arrayLicenses['renovation']));
                    $renovation = "";
                    if (isset($arrayLicenses['renovation'])) {
                        $renovation = $arrayLicenses['renovation'];
                    } else {
                        $renovation = "";
                    }

                    $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);

                    $arrayLicenseData[] = array(
                        "idLicense" => $licensesData->getId(),
                        "license" => $licensesData->getLicense(),
                        "usersquantity" => $licensesData->getUsersquantity(),
                        "numberclients" => $licensesData->getNumberclients(),
                        "numberexams" => $licensesData->getNumberexams(),
                        "durationtime" => $licensesData->getDurationtime(),
                        "statusLicense" => $arrayLicenses['status'],
                        "expiration_date" => $arrayLicenses['expiration_date'],
                        "renovation" => $renovation,
                        "amount" => $licensesData->getAmount());
//            }     
                    if ($arrayLicenses['status'] == "Active") {
                        $duedate = $duedate . " " . $licensesData->getLicense() . " / " . date('Y-m-d', $arrayLicenses['expiration_date']->sec) . $separador;
                    }
                }

                $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));
                return $this->render('@App/payments/loadPayments.html.twig', array('duedate' => $duedate, 'country_id' => $country_id, 'id' => $medicalcenterData[0]->getId(), 'medicalcenter' => $medicalcenter, 'countries' => $countries, 'arrayLicenseData' => $arrayLicenseData, 'opcion' => $opcion, 'daystopayEdit' => $daystopayEdit, 'currentSymbol' => $currentSymbol));
            } else if ($opcion == 8) {
                $infoMedical = $country_id;
                $porciones = explode(" / ", $infoMedical);
                $nameMedical = $porciones[0];
                $medicalcenterData = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('name' => $nameMedical));
                $licensecountry = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->findBy(array('countries' => $medicalcenterData[0]->getCountryid()));
                return $this->render('@App/payments/loadPayments.html.twig', array('country_id' => $medicalcenterData[0]->getCountryid(), 'licensecountry' => $licensecountry, 'opcion' => $opcion, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 9) {
                $infoMedical = $country_id;
                $porciones = explode(" / ", $infoMedical);
                $nameMedical = $porciones[0];
                $medicalcenterData = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('name' => $nameMedical));
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($medicalcenterData[0]->getCountryid());
                $waytopay = $country->getWaytopay();
                return $this->render('@App/payments/loadPayments.html.twig', array('waytopay' => $waytopay, 'opcion' => $opcion, 'waytopayEdit' => $waytopayEdit, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 10) {
                $infoMedical = $country_id;
                $porciones = explode(" / ", $infoMedical);
                $nameMedical = $porciones[0];
                $medicalcenterData = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('name' => $nameMedical));
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($medicalcenterData[0]->getCountryid());
                $issuingbank = $country->getIssuingbank();
                return $this->render('@App/payments/loadPayments.html.twig', array('issuingbank' => $issuingbank, 'opcion' => $opcion, 'issuingbankEdit' => $issuingbankEdit, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 11) {
                $infoMedical = $country_id;
                $porciones = explode(" / ", $infoMedical);
                $nameMedical = $porciones[0];
                $medicalcenterData = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('name' => $nameMedical));
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($medicalcenterData[0]->getCountryid());
                $receivingbank = $country->getReceivingbank();
                return $this->render('@App/payments/loadPayments.html.twig', array('receivingbank' => $receivingbank, 'opcion' => $opcion, 'receivingbankEdit' => $receivingbankEdit, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 12) {

                $infoMedical = $country_id;
                $porciones = explode(" / ", $infoMedical);
                $nameMedical = $porciones[0];

                $medicalcenterData = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('name' => $nameMedical));

                $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalcenterData[0]->getId());
                $arrayLicenses = $medicalcenter->getLicenses();
                $duedate = "";
                $countLicense = count($arrayLicenses);
                if ($countLicense > 1) {
                    $separador = " | ";
                } else {
                    $separador = " ";
                }
                foreach ($arrayLicenses as $arrayLicenses) {
                    if ($arrayLicenses['status'] == "Active") {
//            var_dump(isset($arrayLicenses['renovation']));
                        $renovation = "";
                        if (isset($arrayLicenses['renovation'])) {
                            $renovation = $arrayLicenses['renovation'];
                        } else {
                            $renovation = "";
                        }

                        $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);

                        $arrayLicenseData[] = array(
                            "idLicense" => $licensesData->getId(),
                            "license" => $licensesData->getLicense(),
                            "usersquantity" => $licensesData->getUsersquantity(),
                            "numberclients" => $licensesData->getNumberclients(),
                            "numberexams" => $licensesData->getNumberexams(),
                            "durationtime" => $licensesData->getDurationtime(),
                            "statusLicense" => $arrayLicenses['status'],
                            "expiration_date" => $arrayLicenses['expiration_date'],
                            "renovation" => $renovation,
                            "amount" => $licensesData->getAmount(),
                            "duedate" => date('Y-m-d', $arrayLicenses['expiration_date']->sec));
                    }
                }

                $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));
                return $this->render('@App/payments/loadPayments.html.twig', array('arrayLicenseData' => $arrayLicenseData, 'opcion' => $opcion, 'daystopayEdit' => $daystopayEdit));
            }
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @Route("/payments/searchPayments", name="search_payments")
     * @Method({"GET", "POST"})
     */
    public function searchPayments(Request $request) {

        if ($request->isXmlHttpRequest()) {
            $country_id = $request->request->get('valor_id');
            $opcion = $request->request->get('opcion');
            $waytopayEdit = $request->request->get('waytopay');
            $issuingbankEdit = $request->request->get('issuingbank');
            $receivingbankEdit = $request->request->get('receivingbank');
            $daystopayEdit = $request->request->get('daystopay');

            if ($opcion == 1) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $waytopay = $country->getWaytopay();
                return $this->render('@App/payments/loadPayments.html.twig', array('waytopay' => $waytopay, 'opcion' => $opcion, 'waytopayEdit' => $waytopayEdit, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 2) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $issuingbank = $country->getIssuingbank();
                return $this->render('@App/payments/loadPayments.html.twig', array('issuingbank' => $issuingbank, 'opcion' => $opcion, 'issuingbankEdit' => $issuingbankEdit, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 3) {
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);
                $receivingbank = $country->getReceivingbank();
                return $this->render('@App/payments/loadPayments.html.twig', array('receivingbank' => $receivingbank, 'opcion' => $opcion, 'receivingbankEdit' => $receivingbankEdit, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 4) {
                $licensecountry = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->findBy(array('countries' => $country_id));
                return $this->render('@App/payments/loadPayments.html.twig', array('licensecountry' => $licensecountry, 'opcion' => $opcion, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 5) {
                $valor_country = $request->request->get('valor_country');
                $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($valor_country);
                $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($country_id);
                return $this->render('@App/payments/loadPayments.html.twig', array('license' => $license, 'country' => $country, 'opcion' => $opcion, 'daystopayEdit' => $daystopayEdit));
            } else if ($opcion == 6) {
                $value = $country_id;
                $expression = "/$value/i";
                $regex = new \MongoRegex($expression);
                $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('name' => $regex));

                return $this->render('@App/payments/loadPayments.html.twig', array('medicalcenter' => $medicalcenter, 'opcion' => $opcion, 'daystopayEdit' => $daystopayEdit));
            }
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

}
