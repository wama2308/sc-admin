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

        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findByActive(true);
        $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findAll();
        //var_dump($countries);

        return $this->render('@App/payments/index.html.twig', array('countries' => $countries, 'medicalcenter' => $medicalcenter));
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
                $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);
                $acumLicenses = $acumLicenses + $licensesData->getAmount();
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
        

        if (($waytopay != "") || ($paymenttype != "") || ($amount != "")){

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
                $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);
                $acumLicenses = $acumLicenses + $licensesData->getAmount();
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
     * @Route("/payments/details/{id}", name="payments_details")
     * @Method("GET")
     */
    public function detailsAction($id) {
        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
        $arrayLicenses = $medicalcenter->getLicenses();

        foreach ($arrayLicenses as $arrayLicenses) {
            if ($arrayLicenses['status'] == "Active") {
                $licensesData = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($arrayLicenses['license_id']);

                $arrayLicenseData[] = array("license" => $licensesData->getLicense(),
                    "usersquantity" => $licensesData->getUsersquantity(),
                    "numberclients" => $licensesData->getNumberclients(),
                    "numberexams" => $licensesData->getNumberexams(),
                    "durationtime" => $licensesData->getDurationtime(),
                    "amount" => $licensesData->getAmount());
            }
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

        return $this->redirectToRoute('payments_details', array('id' => $id));
    }

    /**
     * @Route("/payments/guevo", name="guevo")
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
            }
        }
        return new Response('Unauthorized Request, No es XmlHttpRequest', Response::HTTP_UNAUTHORIZED);
    }

}
