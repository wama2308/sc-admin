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

class ApiRestStandardCountryController extends Controller {

    /**
     * @Route("/api/queryProvinces")
     * @Method("GET")
     */
    public function queryProvincesAction(Request $request) {
        /*Function responsible for connecting and bringing all
          the data of the provinces of a country*/

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

                        $medical_center = $this->get('doctrine_mongodb')
                                        ->getRepository('AppBundle:MedicalCenter')
                                        ->find($medical_center_id);

                        $array_patients = $medical_center->getPatients();
                        $medical_center_country = $medical_center->getCountryid();

                        $country = $this->get('doctrine_mongodb')
                                 ->getRepository('AppBundle:Country')
                                 ->find($medical_center_country);

                        $province = $country->getProvinces();

                        foreach ($province as $province_data) {
                            $array_province[] = array(
                                'label' => $province_data['name'],
                                'value' => (string)$province_data['_id']
                            );
                        }

                        return new JsonResponse($array_province);
                    }
                } else {
                    $data = array('message' => 'Failed to consult the data, problems with the token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/queryDistricts")
     * @Method("POST")
     */
    public function queryDistrictsAction(Request $request) {
        /*Function responsible for connecting and bringing all
          the data of the districts of a country*/

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

                        $medical_center = $this->get('doctrine_mongodb')
                                        ->getRepository('AppBundle:MedicalCenter')
                                        ->find($medical_center_id);

                        $medical_center_country = $medical_center->getCountryid();


                        $country = $this->get('doctrine_mongodb')
                                 ->getRepository('AppBundle:Country')
                                 ->find($medical_center_country);

                        $province_id = $request->request->get('_id');
                        $province = $country->getProvinces();

                        if (!empty($province_id)) {
                            foreach ($province as $province_aux) {
                                if ($province_id == (string) $province_aux['_id']) {
                                    foreach ($province_aux['district'] as $district_aux) {
                                        $array_district[] = array(
                                            'label' => $district_aux['name'],
                                            'value' => (string)$district_aux['_id']
                                        );
                                    }
                                    return new JsonResponse($array_district);
                                }
                            }
                        }
                    }
                } else {
                    $data = array('message' => 'Failed to consult the data, problems with the token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/queryNationalPayments")
     * @Method("GET")
     */
    public function queryNationalPaymentsAction(Request $request) {
        /*Search of the banks*/

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

                        $medical_center = $this->get('doctrine_mongodb')
                                        ->getRepository('AppBundle:MedicalCenter')
                                        ->find($medical_center_id);

                        $medical_center_country = $medical_center->getCountryid();

                        $country = $this->get('doctrine_mongodb')
                                 ->getRepository('AppBundle:Country')
                                 ->find($medical_center_country);

                        $issuing_bank = $country->getIssuingbank();
                        $receiving_bank = $country->getReceivingbank();
                        $way_to_pay = $country->getWaytopay();;
                        $type_identity = $country->getTypeIdentity();
                        $tax_rate = $country->getTaxRate();

                        foreach ($issuing_bank as $issuing_bank_aux) {
                            $issuing_bank_array[] = array(
                                'label' => $issuing_bank_aux,
                                'value' => $issuing_bank_aux
                            );
                        }

                        foreach ($receiving_bank as $receiving_bank_aux) {
                            $receiving_bank_array[] = array(
                                'label' => $receiving_bank_aux,
                                'value' => $receiving_bank_aux
                            );
                        }

                        foreach ($way_to_pay as $way_to_pay_aux) {
                            $way_to_pay_array[] = array(
                                'label' => $way_to_pay_aux,
                                'value' => $way_to_pay_aux
                            );
                        }

                        foreach ($type_identity as $type_identity_aux) {
                            $type_identity_array[] = array(
                                'label' => $type_identity_aux['name'],
                                'value' => (string)$type_identity_aux['_id']
                            );
                        }

                        $national_payments = array(
                            "issuing_bank" => $issuing_bank_array,
                            "receiving_bank" => $receiving_bank_array,
                            "way_to_pay" => $way_to_pay_array,
                            "type_identity" => $type_identity_array,
                            "tax_rate" => $tax_rate
                        );

                        $encoders = array(new XmlEncoder(), new JsonEncoder());
                        $normalizers = array(new ObjectNormalizer());
                        $serializer = new Serializer($normalizers, $encoders);

                        $jsonContent = $serializer->serialize($national_payments, 'json');
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
