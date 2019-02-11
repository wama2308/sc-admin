<?php

namespace AppBundle\Controller\ApiRest;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use AppBundle\Document\Country;
use AppBundle\Document\MedicalCenter;
use AppBundle\Document\UsersFront;
use AppBundle\Document\Services;
use \Datetime;
use AppBundle\Document\GeneralConfiguration;

class ApiRestStandardConfigurationController extends Controller {

    /**
     * @Route("/api/queryGeneral")
     * @Method("GET")
     */
    public function queryGeneralAction(Request $request) {
        /*General search*/

        $token = $request->headers->get('access-token');
        if ($token == "") {
            $data = array('message' => 'Token invalido');
            return new JsonResponse($data, 403);
        } else {
            $data_token = $this->get('lexik_jwt_authentication.encoder')
                        ->decode($token);
            if ($data_token == false) {
                $data = array('message' => 'Authentication Required');
                return new JsonResponse($data, 403);
            } else {
                $user_id = $data_token["id"];
                $user = $this->get('doctrine_mongodb')
                      ->getRepository('AppBundle:UsersFront')
                      ->findOneBy(['_id' => $user_id]);
                if ($user) {
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {
                            foreach ($valor->medical_center as $valor_medical_center) {
                                if ($valor_medical_center->is_default == "1") {
                                    $medical_center_id = $valor_medical_center->_id;
                                }
                            }
                        }

                        $general_configuration = $this->get('doctrine_mongodb')
                                               ->getRepository('AppBundle:GeneralConfiguration')
                                               ->find("5ae08f86c5dfa106dc92610a");

                        $sex = $general_configuration->getSex();
                        $civil_state = $general_configuration->getCivilState();
                        $payment_type = $general_configuration->getPaymentType();
                        $type_supplies = $general_configuration->getTypeSupplies();
                        $profession = $general_configuration->getProfession();
                        $specialization = $general_configuration->getSpecialization();

                        foreach ($sex as $sex_aux) {
                            $sex_array[] = array(
                                'label' => $sex_aux['name'],
                                'value' => (string)$sex_aux['_id']
                            );
                        }

                        foreach ($civil_state as $civil_state_aux) {
                            $civil_state_array[] = array(
                                'label' => $civil_state_aux['name'],
                                'value' => (string)$civil_state_aux['_id']
                            );
                        }

                        foreach ($payment_type as $payment_type_aux) {
                            $payment_type_array[] = array(
                                'label' => $payment_type_aux['name'],
                                'value' => (string)$payment_type_aux['_id']
                            );
                        }

                        foreach ($type_supplies as $type_supplies_aux) {
                            $type_supplies_array[] = array(
                                'label' => $type_supplies_aux['name'],
                                'value' => (string)$type_supplies_aux['_id']
                            );
                        }

                        foreach ($profession as $profession_aux) {
                            $profession_array[] = array(
                                'label' => $profession_aux['name'],
                                'value' => (string)$profession_aux['_id']
                            );
                        }

                        foreach ($specialization as $specialization_aux) {
                            $specialization_array[] = array(
                                'label' => $specialization_aux['name'],
                                'value' => (string)$specialization_aux['_id']
                            );
                        }

                        $array_general = array(
                            "sex" => $sex_array,
                            "civil_state" => $civil_state_array,
                            "payment_type" => $payment_type_array,
                            "type_supplies" => $type_supplies_array,
                            "profession" => $profession_array,
                            "specialization" => $specialization_array
                        );

                        $encoders = array(new XmlEncoder(), new JsonEncoder());
                        $normalizers = array(new ObjectNormalizer());
                        $serializer = new Serializer($normalizers, $encoders);

                        $jsonContent = $serializer->serialize($array_general, 'json');
                        return new Response($jsonContent);
                    }
                } else {
                    $data = array('message' => 'Failed to consult the data, problems with the token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

}
