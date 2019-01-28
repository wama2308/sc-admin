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
        /* Function responsible for connecting and bringing all patient data
          from the same clinical center */

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

                        if (!empty($array_patients)) {
                            foreach ($array_patients as $valor_patients) {
                                $array_aux_patients[] = array(
                                    'label' => $valor_patients['type_identity'] . "-" . $valor_patients['dni'] . " " .
                                    $valor_patients['names'] . " " . $valor_patients['surnames'],
                                    'value' => (string) $valor_patients['_id']
                                );
                            }
                        } else {
                            $data = array('message' => 'There are no registered patients');
                            return new JsonResponse($data);
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
        /* Function responsible for connecting and bringing one patient data
          from the same clinical center */

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

                        foreach ($array_patients as $array_patient) {
                            if ($array_patient['_id'] == $patients_id) {
                                return new JsonResponse($array_patient);
                            }
                        }

                        $data = array('message' => 'There is no registered user under that id');
                        return new JsonResponse($data);
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
        /* Function responsible for the creation of patients */

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

                        $array_patients = $medical_center->getPatients();

                        /* Receiving data */
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
                                    $data = array('message' => 'The DNi is already registered');
                                    return new JsonResponse($data, 403);
                                }
                            }
                        }

                        /* Validating variables */
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
                        } else {

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

                            return new Response('Successful operation');
                        }
                    }
                    return new Response('Successful operation');
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
        /* Function responsible editing passages */

        $fecha_now = new \MongoDate();
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

                        /* Receiving data */
                        $position = $request->request->get("posicion");
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

                        /* Validating variables */
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
                        } else {

                            $array_patients[$position]["dni"] = $dni;
                            $array_patients[$position]["type_identity"] = $type_identity;
                            $array_patients[$position]["names"] = $names;
                            $array_patients[$position]["surnames"] = $surnames;
                            $array_patients[$position]["province"] = $province;
                            $array_patients[$position]["district_id"] = $district;
                            $array_patients[$position]["address_id"] = $address;
                            $array_patients[$position]["phone"] = $phone;
                            $array_patients[$position]["email"] = $email;
                            $array_patients[$position]["sex_id"] = $sex;
                            $array_patients[$position]["civil_state_id"] = $civil_state;
                            $array_patients[$position]["photo"] = $photo;
                            $array_patients[$position]["birth_date"] = $birth_date;
                            $array_patients[$position]["branchoffices_register"] = $branchoffices_register;
                            $array_patients[$position]["active"] = $active;
                            $array_patients[$position]["update_at"] = $fecha_now;
                            $array_patients[$position]["update_by"] = $user_id;

                            $medical_center->setPatients($array_patients);
                            $medical_center->setUpdatedAt($fecha_now);
                            $medical_center->setUpdatedBy($user_id);

                            $dm = $this->get('doctrine_mongodb')->getManager();
                            $dm->persist($medical_center);
                            $dm->flush();

                            return new Response('Successful operation');
                        }
                    }
                    return new Response('Successful operation');
                } else {
                    $data = array('message' => 'Failed to consult the data, problems with the token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadServicesSelect")     
     * @Method("GET")
     */
    public function LoadServicesSelectAction(Request $request) {

        $token = $request->headers->get('access-token');

        if ($token == "") {
            $data = array('message' => 'Token invalido');
            return new JsonResponse($data, 403);
        } else {

            $data_token = $this->get('lexik_jwt_authentication.encoder')->decode($token);

            if ($data_token == false) {

                $data = array('message' => 'Authentication Required');
                return new JsonResponse($data, 403);
            } else {

                $user_id = $data_token["id"];
                $user = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findOneBy(['_id' => $user_id]);
                if ($user) {
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($valorMedicalCenter->_id);
                                    $countryId = $medicalcenter->getCountryid();
                                    $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($countryId);
                                    $currencySymbol = $country->getCurrencySymbol();

                                    $ArrayLicenses = $medicalcenter->getLicenses();
                                    foreach ($ArrayLicenses as $TravelArrayLicenses) {

                                        $arrayDate = date('Y-m-d H:i:s', $TravelArrayLicenses["expiration_date"]->sec);
                                        $fechaActual = date('Y-m-d h:i:s');

                                        if ($arrayDate >= $fechaActual) {

                                            if (!empty($TravelArrayLicenses["services"])) {
                                                $servicesArray = $TravelArrayLicenses["services"];

                                                $acumServices = 0;
                                                $licenseId = "";
                                                $serviceId = "";
                                                $serviceName = "";
                                                $category = "";
                                                $fields = "";
                                                $format = "";
                                                $amount = "";
                                                $status = "";

                                                foreach ($servicesArray as $keyServices => $travelServicesArray) {                                                    

                                                    if ($travelServicesArray["active"] == 1) {

                                                        $arrayServices[] = array(
                                                            "label" => $travelServicesArray["name"],
                                                            "value" => $travelServicesArray["_id"],                                                            
                                                            "monto" => $travelServicesArray["amount"]                                                            
                                                        );
                                                    }
                                                }

                                                $encoders = array(new XmlEncoder(), new JsonEncoder());
                                                $normalizers = array(new ObjectNormalizer());
                                                $serializer = new Serializer($normalizers, $encoders);
                                                $jsonContent = $serializer->serialize($arrayServices, 'json');
                                                return new Response($jsonContent);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

}
