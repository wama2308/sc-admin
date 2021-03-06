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

class ApiRestCustomerSupportController extends Controller {

    /**
     * @Route("/api/queryPatients")
     * @Method("GET")
     */
    public function queryPatientsAction(Request $request) {
        /*Function responsible for connecting and bringing all patient data
          from the same clinical center*/

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

                        $type_identity = $country->getTypeIdentity();
                        $array_patients = $medical_center->getPatients();

                        if (!empty($array_patients)) {
                            foreach ($array_patients as $valor_patients) {
                                foreach ($type_identity as $type_identity_aux) {
                                    if ((string)$type_identity_aux['_id'] == $valor_patients['type_identity']) {
                                        $type_identity_set = $type_identity_aux['name'];
                                    }
                                }
                                $array_aux_patients[] = array(
                                    'label' => $type_identity_set . "-" . $valor_patients['dni'] . " " .
                                    $valor_patients['names'] . " " . $valor_patients['surnames'],
                                    'value' => (string) $valor_patients['_id']
                                );
                            }
                        } else {
                            return new JsonResponse(0);
                        }

                        $encoders = array(new XmlEncoder(), new JsonEncoder());
                        $normalizers = array(new ObjectNormalizer());
                        $serializer = new Serializer($normalizers, $encoders);

                        $jsonContent = $serializer->serialize($array_aux_patients, 'json');
                        return new Response($jsonContent);
                    }
                } else {
                    $data = array('message' => 'Failed to consult the data, problems with the token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/queryOnePatients")
     * @Method("POST")
     */
    public function queryOnePatientsAction(Request $request) {
        /*Function responsible for connecting and bringing one patient data
          from the same clinical center*/

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

                        $patients_id = $request->request->get('_id');
                        $medical_center_country = $medical_center->getCountryid();

                        $general_configuration = $this->get('doctrine_mongodb')
                                               ->getRepository('AppBundle:GeneralConfiguration')
                                               ->find("5ae08f86c5dfa106dc92610a");

                        $sex = $general_configuration->getSex();
                        $civil_state = $general_configuration->getCivilState();

                        $country = $this->get('doctrine_mongodb')
                                 ->getRepository('AppBundle:Country')
                                 ->find($medical_center_country);

                        $type_identity = $country->getTypeIdentity();
                        $province = $country->getProvinces();

                        foreach ($array_patients as $array_patient) {
                            if ((string) $array_patient['_id'] == $patients_id) {
                                $array_patient_aux = $array_patient;
                            }
                        }

                        foreach ($sex as $sex_aux) {
                            if ($sex_aux['_id'] == $array_patient_aux['sex_id']) {
                                $sex_set = $sex_aux['name'];
                            }
                        }

                        foreach ($civil_state as $civil_state_aux) {
                            if ($civil_state_aux['_id'] == $array_patient_aux['civil_state_id']) {
                                $civil_state_set = $civil_state_aux['name'];
                            }
                        }

                        foreach ($type_identity as $type_identity_aux) {
                            if ($type_identity_aux['_id'] == $array_patient_aux['type_identity']) {
                                $type_identity_set = $type_identity_aux['name'];
                            }
                        }

                        foreach ($province as $province_aux) {
                            if ($province_aux['_id'] == $array_patient_aux['province_id']) {
                                $province_set = $province_aux['name'];
                                foreach ($province_aux['district'] as $district_aux) {
                                    if ($district_aux['_id'] == $array_patient_aux['district_id']) {
                                        $district_set = $district_aux['name'];
                                    }
                                }
                            }
                        }

                        $array_patient_info[] = array(
                            '_id' => (string)$array_patient_aux['_id'],
                            'type_identity' => $type_identity_set,
                            'dni' => $array_patient_aux['dni'],
                            'names' => $array_patient_aux['names'],
                            'surnames' => $array_patient_aux['surnames'],
                            'province' => $province_set,
                            'district' => $district_set,
                            'address' => $array_patient_aux['address'],
                            'phone' => $array_patient_aux['phone'],
                            'email' => $array_patient_aux['email'],
                            'sex' => $sex_set,
                            'civil_state' => $civil_state_set,
                            'photo' => $array_patient_aux['photo'],
                            'birth_date' => $array_patient_aux['birth_date'],
                            'branchoffices_register' => $array_patient_aux['branchoffices_register'],
                            'active' => $array_patient_aux['active'],
                            'created_at' => $array_patient_aux['created_at'],
                            'created_by' => $array_patient_aux['created_by'],
                            'updated_at' => $array_patient_aux['updated_at'],
                            'updated_by' => $array_patient_aux['updated_by']
                        );

                        return new JsonResponse($array_patient_info);
                    }
                } else {
                    $data = array('message' => 'Failed to consult the data, problems with the token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/createPatients")
     * @Method("POST")
     */
    public function createPatientsAction(Request $request) {
        /*Function responsible for the creation of patients*/

        //$date_now = new \MongoDate();
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
        $date_now = new \MongoDate($ts);
        ///////////////////////PARA LA FECHA CON SU RESPECTIVA ZONA HORARIA
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
                                    foreach ($valor_medical_center->branch_office as $valor_aux) {
                                        $branchoffice = $valor_aux->_id;
                                    }
                                }
                            }
                        }

                        $medical_center = $this->get('doctrine_mongodb')
                                        ->getRepository('AppBundle:MedicalCenter')
                                        ->find($medical_center_id);

                        $array_patients = $medical_center->getPatients();

                        /*Receiving data*/
                        $id_now = new \MongoId(); // Creating ID
                        $type_identity = $request->request->get("type_identity");
                        $dni = $request->request->get("dni");
                        $names = $request->request->get("names");
                        $surnames = $request->request->get("surnames");
                        $province = $request->request->get("province_id");
                        $district = $request->request->get("district_id");
                        $address = $request->request->get("address");
                        $phone = $request->request->get("phone");
                        $email = $request->request->get("email");
                        $sex = $request->request->get("sex_id");
                        $civil_state = $request->request->get("civil_state_id");
                        $photo = $request->request->get("photo");
                        $birth_date = $request->request->get("birth_date");
                        $branchoffices_register = $branchoffice;
                        $active = $request->request->get("active");

                        foreach ($array_patients as $aux) {
                            if ($aux['dni'] == $dni) {
                                if ($aux['type_identity'] == $type_identity) {
                                    return new JsonResponse(2);
                                }
                            }
                        }

                        /*Validating variables*/
                        if ($type_identity == "") {
                            return new Response('Select type of identity');
                        } else if ($dni == "") {
                            return new Response('Enter your DNI');
                        } else if ($names == "") {
                            return new Response('Enter your names');
                        } else if ($surnames == "") {
                            return new Response('Enter your surnames');
                        } else if ($province == "") {
                            return new Response('Select province');
                        } else if ($district == "") {
                            return new Response('Select district');
                        } else if ($address == "") {
                            return new Response('Enter your address');
                        } else if ($phone[0] == null) {
                            return new Response('Enter your phone');
                        } else if ($email[0] == null) {
                            return new Response('Enter your email');
                        } else if ($sex == "") {
                            return new Response('Select sex');
                        } else if ($civil_state == "") {
                            return new Response('Select civil state');
                        } else if ($birth_date == "") {
                            return new Response('Enter your birthdate');
                        }else {

                            $array_patients[] = array(
                                "_id" => $id_now,
                                "type_identity" => $type_identity,
                                "dni" => $dni,
                                "names" => $names,
                                "surnames" => $surnames,
                                "province_id" => $province,
                                "district_id" => $district,
                                "address" => $address,
                                "phone" => $phone,
                                "email" => $email,
                                "sex_id" => $sex,
                                "civil_state_id" => $civil_state,
                                "photo" => $photo,
                                "birth_date" => $birth_date,
                                "branchoffices_register" => $branchoffices_register,
                                "active" => $active,
                                "created_at" => $date_now,
                                "created_by" => $user_id,
                                "updated_at" => $date_now,
                                "updated_by" => $user_id
                            );

                            $medical_center->setPatients($array_patients);
                            $medical_center->setUpdatedAt($date_now);
                            $medical_center->setUpdatedBy($user_id);

                            $dm = $this->get('doctrine_mongodb')->getManager();
                            $dm->persist($medical_center);
                            $dm->flush();

                            return new Response(1);
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
     * @Route("/api/editPatients")
     * @Method("POST")
     */
    public function editPatientsAction(Request $request) {
        /*Function responsible editing patients*/

        //$date_now = new \MongoDate();
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
        $date_now = new \MongoDate($ts);
        ///////////////////////PARA LA FECHA CON SU RESPECTIVA ZONA HORARIA
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
                                    foreach ($valor_medical_center->branch_office as $valor_aux) {
                                        $branchoffice = $valor_aux->_id;
                                    }
                                }
                            }
                        }

                        $medical_center = $this->get('doctrine_mongodb')
                                        ->getRepository('AppBundle:MedicalCenter')
                                        ->find($medical_center_id);

                        $array_patients = $medical_center->getPatients();

                        /*Receiving data*/
                        $id_patient = $request->request->get("_id");
                        $type_identity = $request->request->get("type_identity");
                        $dni = $request->request->get("dni");
                        $names = $request->request->get("names");
                        $surnames = $request->request->get("surnames");
                        $province = $request->request->get("province_id");
                        $district = $request->request->get("district_id");
                        $address = $request->request->get("address");
                        $phone = $request->request->get("phone");
                        $email = $request->request->get("email");
                        $sex = $request->request->get("sex_id");
                        $civil_state = $request->request->get("civil_state_id");
                        $photo = $request->request->get("photo");
                        $birth_date = $request->request->get("birth_date");
                        $branchoffices_register = $branchoffice;
                        $active = $request->request->get("active");

                        /*Validating variables*/
                        if ($type_identity == "") {
                            return new Response('Select type of identity');
                        } else if ($dni == "") {
                            return new Response('Enter your DNI');
                        } else if ($names == "") {
                            return new Response('Enter your names');
                        } else if ($surnames == "") {
                            return new Response('Enter your surnames');
                        } else if ($province == "") {
                            return new Response('Select province');
                        } else if ($district == "") {
                            return new Response('Select district');
                        } else if ($address == "") {
                            return new Response('Enter your address');
                        } else if ($phone[0] == null) {
                            return new Response('Enter your phone');
                        } else if ($email[0] == null) {
                            return new Response('Enter your email');
                        } else if ($sex == "") {
                            return new Response('Select sex');
                        } else if ($civil_state == "") {
                            return new Response('Select civil state');
                        } else if ($birth_date == "") {
                            return new Response('Enter your birthdate');
                        }else {

                            $count = 0;
                            foreach ($array_patients as $array_patients_edit) {
                                if ((string)$array_patients_edit['_id'] == $id_patient) {
                                    $array_patients[$count]['type_identity'] = $type_identity;
                                    $array_patients[$count]['dni'] = $dni;
                                    $array_patients[$count]['names'] = $names;
                                    $array_patients[$count]['surnames'] = $surnames;
                                    $array_patients[$count]['province_id'] = $province;
                                    $array_patients[$count]['district_id'] = $district;
                                    $array_patients[$count]['address'] = $address;
                                    $array_patients[$count]['phone'] = $phone;
                                    $array_patients[$count]['email'] = $email;
                                    $array_patients[$count]['sex_id'] = $sex;
                                    $array_patients[$count]['civil_state_id'] = $civil_state;
                                    $array_patients[$count]['photo'] = $photo;
                                    $array_patients[$count]['birth_date'] = $birth_date;
                                    $array_patients[$count]['branchoffices_register'] = $branchoffices_register;
                                    $array_patients[$count]['active'] = $active;
                                    $array_patients[$count]['updated_at'] = $date_now;
                                    $array_patients[$count]['updated_by'] = $user_id;

                                    $medical_center->setPatients($array_patients);
                                    $medical_center->setUpdatedAt($date_now);
                                    $medical_center->setUpdatedBy($user_id);

                                    $dm = $this->get('doctrine_mongodb')->getManager();
                                    $dm->persist($medical_center);
                                    $dm->flush();

                                    return new Response(1);
                                }
                                $count++;
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
}
