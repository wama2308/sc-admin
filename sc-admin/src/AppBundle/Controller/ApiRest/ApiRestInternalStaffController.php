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

class ApiRestInternalStaffController extends Controller {

    /**
     * @Route("/api/queryInternalStaff")
     * @Method("GET")
     */
    public function queryInternalStaffAction(Request $request) {
        /*Responsible function to connect and show all the staff
          of the same medical center*/

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

                        $array_staff = $medical_center->getInternalStaff();
                        $medical_center_country = $medical_center->getCountryid();

                        $country = $this->get('doctrine_mongodb')
                                 ->getRepository('AppBundle:Country')
                                 ->find($medical_center_country);

                        $type_identity = $country->getTypeIdentity();

                        if (!empty($array_staff)) {
                            foreach ($array_staff as $valor_staff) {
                                foreach ($type_identity as $type_identity_aux) {
                                    if ($type_identity_aux['_id'] == $valor_staff['type_identity']) {
                                        $type_identity_set = $type_identity_aux['name'];
                                    }
                                }
                                $array_aux_staff[] = array(
                                    'label' => $type_identity_set . "-" . $valor_staff['dni'] . " " .
                                    $valor_staff['names'] . " " . $valor_staff['surnames'],
                                    'value' => (string) $valor_staff['_id']
                                );
                            }
                        } else {
                            return new JsonResponse(0);
                        }

                        $encoders = array(new XmlEncoder(), new JsonEncoder());
                        $normalizers = array(new ObjectNormalizer());
                        $serializer = new Serializer($normalizers, $encoders);

                        $jsonContent = $serializer->serialize($array_aux_staff, 'json');
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
     * @Route("/api/queryOneInternalStaff")
     * @Method("POST")
     */
    public function queryOneInternalStaffAction(Request $request) {
        /*Function responsible for connecting and displaying a
          staff of the same medical center*/

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

                        $array_staff = $medical_center->getInternalStaff();

                        $staff_id = $request->request->get('_id');
                        $medical_center_country = $medical_center->getCountryid();

                        $country = $this->get('doctrine_mongodb')
                                 ->getRepository('AppBundle:Country')
                                 ->find($medical_center_country);

                        foreach ($array_staff as $array_staff_aux) {
                            if ((string) $array_staff_aux['_id'] == $staff_id) {
                                $array_staff = $array_staff_aux;
                            }
                        }

                        $general_configuration = $this->get('doctrine_mongodb')
                                               ->getRepository('AppBundle:GeneralConfiguration')
                                               ->find("5ae08f86c5dfa106dc92610a");

                        $sex = $general_configuration->getSex();
                        $civil_state = $general_configuration->getCivilState();
                        $profession = $general_configuration->getProfession();
                        $specialization = $general_configuration->getSpecialization();
                        $type_identity = $country->getTypeIdentity();

                        foreach ($sex as $sex_aux) {
                            if ($sex_aux['_id'] == $array_staff_aux['sex_id']) {
                                $sex_set = $sex_aux['name'];
                            }
                        }

                        foreach ($civil_state as $civil_state_aux) {
                            if ($civil_state_aux['_id'] == $array_staff_aux['civil_state_id']) {
                                $civil_state_set = $civil_state_aux['name'];
                            }
                        }

                        foreach ($type_identity as $type_identity_aux) {
                            if ($type_identity_aux['_id'] == $array_staff_aux['type_identity']) {
                                $type_identity_set = $type_identity_aux['name'];
                            }
                        }

                        foreach ($profession as $profession_aux) {
                            if ($profession_aux['_id'] == $array_staff_aux['profession_id']) {
                                $profession_set = $profession_aux['name'];
                            }
                        }

                        foreach ($specialization as $specialization_aux) {
                            if ($specialization_aux['_id'] == $array_staff_aux['specialization_id']) {
                                $specialization_set = $specialization_aux['name'];
                            }
                        }

                        $country = $this->get('doctrine_mongodb')
                                 ->getRepository('AppBundle:Country')
                                 ->find($medical_center_country);

                        $province = $country->getProvinces();

                        foreach ($province as $province_aux) {
                            if ($province_aux['_id'] == $array_staff_aux['province_id']) {
                                $province_set = $province_aux['name'];
                                foreach ($province_aux['district'] as $district_aux) {
                                    if ($district_aux['_id'] == $array_staff_aux['district_id']) {
                                        $district_set = $district_aux['name'];
                                    }
                                }
                            }
                        }

                        $array_staff_info[] = array(
                            '_id' => (string)$array_staff_aux['_id'],
                            'type_identity' => $type_identity_set,
                            'dni' => $array_staff_aux['dni'],
                            'names' => $array_staff_aux['names'],
                            'surnames' => $array_staff_aux['surnames'],
                            'province' => $province_set,
                            'district' => $district_set,
                            'address' => $array_staff_aux['address'],
                            'phone' => $array_staff_aux['phone'],
                            'email' => $array_staff_aux['email'],
                            'sex' => $sex_set,
                            'civil_state' => $civil_state_set,
                            'photo' => $array_staff_aux['photo'],
                            'birth_date' => $array_staff_aux['birth_date'],
                            'profession' => $profession_set,
                            'specialization' => $specialization_set,
                            'branchoffices_register' => $array_staff_aux['branchoffices_register'],
                            'active' => $array_staff_aux['active']
                        );

                        return new JsonResponse($array_staff_info);
                    }
                } else {
                    $data = array('message' => 'Failed to consult the data, problems with the token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/createInternalStaff")
     * @Method("POST")
     */
    public function createInternalStaffAction(Request $request) {
        /*Function responsible for the creation of staff*/

        $date_now = new \MongoDate();
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

                        $array_internal_staff = $medical_center->getInternalStaff();
                        $dni = $request->request->get("dni");
                        $type_identity = $request->request->get("type_identity");

                        foreach ($array_internal_staff as $aux) {
                            if ($aux['dni'] == $dni) {
                                if ($aux['type_identity'] == $type_identity) {
                                    return new JsonResponse(2);
                                }
                            }
                        }

                        /*Receiving data*/
                        $id_now = new \MongoId(); // Creating ID
                        $names = $request->request->get("names");
                        $surnames = $request->request->get("surnames");
                        $province = $request->request->get("province_id");
                        $district = $request->request->get("district_id");
                        $profession = $request->request->get("profession_id");
                        $specialization = $request->request->get("specialization_id");
                        $position = $request->request->get("position_id");
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
                        } else if ($profession == "") {
                            return new Response('Select profession');
                        } else if ($specialization == "") {
                            return new Response('Select specialization');
                        } else if ($position == "") {
                            return new Response('Select position');
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

                            $array_internal_staff[] = array(
                                "_id" => $id_now,
                                "type_identity" => $type_identity,
                                "dni" => $dni,
                                "names" => $names,
                                "surnames" => $surnames,
                                "province_id" => $province,
                                "district_id" => $district,
                                "profession_id" => $profession,
                                "specialization_id" => $specialization,
                                "position_id" => $position,
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

                            $medical_center->setInternalStaff($array_internal_staff);
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
     * @Route("/api/editInternalStaff")
     * @Method("POST")
     */
    public function editInternalStaffAction(Request $request) {
        /*Function responsible editing internal staff*/

        $date_now = new \MongoDate();
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

                        $array_internal_staff = $medical_center->getInternalStaff();

                        /*Receiving data*/
                        $id_internal_staff = $request->request->get("_id");
                        $type_identity = $request->request->get("type_identity");
                        $dni = $request->request->get("dni");
                        $names = $request->request->get("names");
                        $surnames = $request->request->get("surnames");
                        $province = $request->request->get("province_id");
                        $district = $request->request->get("district_id");
                        $profession = $request->request->get("profession_id");
                        $specialization = $request->request->get("specialization_id");
                        $position = $request->request->get("position_id");
                        $address = $request->request->get("address");
                        $phone = $request->request->get("phone");
                        $email = $request->request->get("email");
                        $sex = $request->request->get("sex_id");
                        $civil_state = $request->request->get("civil_state_id");
                        $photo = $request->request->get("photo");
                        $birth_date = $request->request->get("birth_date");
                        $branchoffices_register = $request->request->get("branchoffices_register");
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
                        } else if ($profession == "") {
                            return new Response('Select profession');
                        } else if ($specialization == "") {
                            return new Response('Select specialization');
                        } else if ($position == "") {
                            return new Response('Select position');
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
                            foreach ($array_internal_staff as $array_internal_staff_edit) {
                                if ((string)$array_internal_staff_edit['_id'] == $id_internal_staff) {
                                    $array_internal_staff[$count]['type_identity'] = $type_identity;
                                    $array_internal_staff[$count]['dni'] = $dni;
                                    $array_internal_staff[$count]['names'] = $names;
                                    $array_internal_staff[$count]['surnames'] = $surnames;
                                    $array_internal_staff[$count]['province_id'] = $province;
                                    $array_internal_staff[$count]['district_id'] = $district;
                                    $array_internal_staff[$count]['position_id'] = $position;
                                    $array_internal_staff[$count]['profession_id'] = $profession;
                                    $array_internal_staff[$count]['specialization_id'] = $specialization;
                                    $array_internal_staff[$count]['address'] = $address;
                                    $array_internal_staff[$count]['phone'] = $phone;
                                    $array_internal_staff[$count]['email'] = $email;
                                    $array_internal_staff[$count]['sex_id'] = $sex;
                                    $array_internal_staff[$count]['civil_state_id'] = $civil_state;
                                    $array_internal_staff[$count]['photo'] = $photo;
                                    $array_internal_staff[$count]['birth_date'] = $birth_date;
                                    $array_internal_staff[$count]['branchoffices_register'] = $branchoffices_register;
                                    $array_internal_staff[$count]['active'] = $active;
                                    $array_internal_staff[$count]['updated_at'] = $date_now;
                                    $array_internal_staff[$count]['updated_by'] = $user_id;

                                    $medical_center->setInternalStaff($array_internal_staff);
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
