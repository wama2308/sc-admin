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
use \Datetime;
use AppBundle\Document\GeneralConfiguration;

class ApiRestMedicalCenterController extends Controller {

//  validation_code medical center master: 0 =  inicio, 1 = codigo enviado, 2 = codigo vencido
//  validation_code reset_password: 0 =  inicio, 1 = codigo enviado, 2 = codigo vencido
//  validation_code unlock_user: 0 =  inicio, 1 = codigo enviado, 2 = codigo vencido
//  enabled user_front: 0 =  inactivo, 1 = activo, 2 = bloqueado
    /**
     * @Route("/api/loadCountries")     
     * @Method("GET")
     */
    public function loadCountriesAction(Request $request) {

        $token = $request->headers->get('Authorization');

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

                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $countries = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findBy(array('provinces.name' => array('$exists' => true), 'active' => true));

                    $jsonContent = $serializer->serialize($countries, 'json');
                    return new Response($jsonContent);
                } else {
                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/loadCountries")     
     * @Method("POST")
     */
    public function loadCountriesIdAction(Request $request) {

        $token = $request->headers->get('Authorization');
        $country_id = $request->request->get('idCountry');

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

                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $country = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($country_id);

                    $jsonContent = $serializer->serialize($country, 'json');
                    return new Response($jsonContent);
                } else {
                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/loadGeneralConfiguration")     
     * @Method("GET")
     */
    public function loadGeneralConfigurationAction(Request $request) {

        $token = $request->headers->get('Authorization');
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

                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");

                    $jsonContent = $serializer->serialize($GeneralConfiguration, 'json');
                    return new Response($jsonContent);
                } else {
                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadMedicalCenter")     
     * @Method("GET")
     */
    public function LoadMedicalCenterAction(Request $request) {

        $token = $request->headers->get('Authorization');
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

                    $medicalCenterId = "";
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalCenterId = $valorMedicalCenter->_id;
                                }
                            }
                        }
                    }
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);

                    $jsonContent = $serializer->serialize($medicalcenter, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadLicense")     
     * @Method("GET")
     */
    public function LoadLicenseAction(Request $request) {

        $token = $request->headers->get('Authorization');
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

                    $medicalCenterId = "";
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalCenterId = $valorMedicalCenter->_id;
                                }
                            }
                        }
                    }

                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
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

                    $jsonContent = $serializer->serialize($arrayLicenseData, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/saveBranchOffices")     
     * @Method("POST")
     */
    public function saveBranchOfficesAction(Request $request) {

        $fechaNow = new \MongoDate();

        $token = $request->headers->get('Authorization');
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

                    $medicalCenterId = "";
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalCenterId = $valorMedicalCenter->_id;
                                }
                            }
                        }
                    }

//                  AQUI EMPIEZA LA LOGICA DEL EDIT  
                    $sucursal = $request->request->get("sucursal");
                    $code = $request->request->get("code");
                    $idCountry = $request->request->get("idCountry");
                    $provincesid = $request->request->get("provincesid");
                    $type = $request->request->get("type");
                    $sector = $request->request->get("sector");
                    $direccion = $request->request->get("direccion");
                    $log = $request->request->get("log");
                    $lat = $request->request->get("lat");
                    $contactos = $request->request->get("contactos");
                    $logo = $request->request->get("logo");
                    $foto1 = $request->request->get("foto1");
                    $foto2 = $request->request->get("foto2");
                    $foto3 = $request->request->get("foto3");
                    $facebook = $request->request->get("facebook");
                    $twitter = $request->request->get("twitter");
                    $instagram = $request->request->get("instagram");
                    $web = $request->request->get("web");

                    if ($sucursal == "") {
                        return new Response('Ingrese la sucursal');
                    } else if ($idCountry == "") {
                        return new Response('Seleccione el pais');
                    } else if ($provincesid == "") {
                        return new Response('Seleccione la provincia');
                    } else if ($type == "") {
                        return new Response('Seleccione el tipo');
                    } else if ($sector == "") {
                        return new Response('Seleccione el sector');
                    } else if ($direccion == "") {
                        return new Response('Ingrese la direccion');
                    } else if ($log == "") {
                        return new Response('Ingrese la longitud');
                    } else if ($lat == "") {
                        return new Response('Ingrese la latitud');
                    } else if ($contactos[0] == null) {
                        return new Response('Ingrese al menos un contacto');
                    } else {


                        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                        $arrayBranchoffices = $medicalcenter->getBranchoffices();

                        $arrayBranchoffices[] = array(
                            "name" => $sucursal,
                            "code" => $code,
                            "countryId" => $idCountry,
                            "provinceId" => $provincesid,
                            "type" => $type,
                            "sector" => $sector,
                            "direccion" => $direccion,
                            "log" => $log,
                            "lat" => $lat,
                            "contacto" => $contactos,
                            "logo" => $logo,
                            "foto1" => $foto1,
                            "foto2" => $foto2,
                            "foto3" => $foto3,
                            "facebook" => $facebook,
                            "twitter" => $twitter,
                            "instagram" => $instagram,
                            "web" => $web,
                            "status" => true,
                            "created_at" => $fechaNow,
                            "created_by" => $user_id);
//                        var_dump($arrayBranchoffices);
                        $medicalcenter->setBranchoffices($arrayBranchoffices);
//
                        $medicalcenter->setUpdatedAt($fechaNow);
                        $medicalcenter->setUpdatedBy($user_id);
//                        
                        $dm = $this->get('doctrine_mongodb')->getManager();
                        //$dm->persist($medicalcenter);
                        $dm->flush();

                        return new Response('Operacion exitosa');
                    }
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }
    
    /**
     * @Route("/api/editBranchOffices")     
     * @Method("POST")
     */
    public function editBranchOfficesAction(Request $request) {

        $fechaNow = new \MongoDate();

        $token = $request->headers->get('Authorization');
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

                    $medicalCenterId = "";
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalCenterId = $valorMedicalCenter->_id;
                                }
                            }
                        }
                    }

//                  AQUI EMPIEZA LA LOGICA DEL EDIT  
                    $posicion = $request->request->get("posicion");
                    $sucursal = $request->request->get("sucursal");
                    $code = $request->request->get("code");
                    $idCountry = $request->request->get("idCountry");
                    $provincesid = $request->request->get("provincesid");
                    $type = $request->request->get("type");
                    $sector = $request->request->get("sector");
                    $direccion = $request->request->get("direccion");
                    $log = $request->request->get("log");
                    $lat = $request->request->get("lat");
                    $contactos = $request->request->get("contactos");
                    $logo = $request->request->get("logo");
                    $foto1 = $request->request->get("foto1");
                    $foto2 = $request->request->get("foto2");
                    $foto3 = $request->request->get("foto3");
                    $facebook = $request->request->get("facebook");
                    $twitter = $request->request->get("twitter");
                    $instagram = $request->request->get("instagram");
                    $web = $request->request->get("web");

                    if ($sucursal == "") {
                        return new Response('Ingrese la sucursal');
                    } else if ($idCountry == "") {
                        return new Response('Seleccione el pais');
                    } else if ($provincesid == "") {
                        return new Response('Seleccione la provincia');
                    } else if ($type == "") {
                        return new Response('Seleccione el tipo');
                    } else if ($sector == "") {
                        return new Response('Seleccione el sector');
                    } else if ($direccion == "") {
                        return new Response('Ingrese la direccion');
                    } else if ($log == "") {
                        return new Response('Ingrese la longitud');
                    } else if ($lat == "") {
                        return new Response('Ingrese la latitud');
                    } else if ($contactos[0] == null) {
                        return new Response('Ingrese al menos un contacto');
                    } else {


                        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                        $arrayBranchoffices = $medicalcenter->getBranchoffices();

                        $arrayBranchoffices[$posicion]["name"] = $sucursal;
                        $arrayBranchoffices[$posicion]["code"] = $code;
                        $arrayBranchoffices[$posicion]["countryId"] = $idCountry;
                        $arrayBranchoffices[$posicion]["provinceId"] = $provincesid;
                        $arrayBranchoffices[$posicion]["type"] = $type;
                        $arrayBranchoffices[$posicion]["sector"] = $sector;
                        $arrayBranchoffices[$posicion]["direccion"] = $direccion;
                        $arrayBranchoffices[$posicion]["log"] = $log;
                        $arrayBranchoffices[$posicion]["lat"] = $lat;
                        $arrayBranchoffices[$posicion]["contacto"] = $contactos;
                        $arrayBranchoffices[$posicion]["logo"] = $logo;
                        $arrayBranchoffices[$posicion]["foto1"] = $foto1;
                        $arrayBranchoffices[$posicion]["foto2"] = $foto2;
                        $arrayBranchoffices[$posicion]["foto3"] = $foto3;
                        $arrayBranchoffices[$posicion]["facebook"] = $facebook;
                        $arrayBranchoffices[$posicion]["twitter"] = $twitter;
                        $arrayBranchoffices[$posicion]["instagram"] = $instagram;
                        $arrayBranchoffices[$posicion]["web"] = $web;                        
                        $arrayBranchoffices[$posicion]["updated_at"] = $fechaNow;
                        $arrayBranchoffices[$posicion]["updated_by"] = $user_id;
                        
                        $medicalcenter->setBranchoffices($arrayBranchoffices);
//
                        $medicalcenter->setUpdatedAt($fechaNow);
                        $medicalcenter->setUpdatedBy($user_id);
//                        
                        $dm = $this->get('doctrine_mongodb')->getManager();
                        $dm->persist($medicalcenter);
                        $dm->flush();

                        return new Response('Operacion exitosa');
                    }
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }
    
    /**
     * @Route("/api/deleteBranchOffices")     
     * @Method("POST")
     */
    public function deleteBranchOfficesAction(Request $request) {

        $fechaNow = new \MongoDate();

        $token = $request->headers->get('Authorization');
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

                    $medicalCenterId = "";
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalCenterId = $valorMedicalCenter->_id;
                                }
                            }
                        }
                    }

//                  AQUI EMPIEZA LA LOGICA DEL EDIT  
                    $posicion = $request->request->get("posicion");     

                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                    $arrayBranchoffices = $medicalcenter->getBranchoffices();

                    $arrayBranchoffices[$posicion]["status"] = false;                        
                    $arrayBranchoffices[$posicion]["updated_at"] = $fechaNow;
                    $arrayBranchoffices[$posicion]["updated_by"] = $user_id;

                    $medicalcenter->setBranchoffices($arrayBranchoffices);
//
                    $medicalcenter->setUpdatedAt($fechaNow);
                    $medicalcenter->setUpdatedBy($user_id);
//                        
                    $dm = $this->get('doctrine_mongodb')->getManager();
                    $dm->persist($medicalcenter);
                    $dm->flush();

                    return new Response('Operacion exitosa');
                    
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/editPerfilMedicalCenter")     
     * @Method("POST")
     */
    public function editPerfilMedicalCenterAction(Request $request) {

        $fechaNow = new \MongoDate();

        $token = $request->headers->get('Authorization');
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

                    $medicalCenterId = "";
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalCenterId = $valorMedicalCenter->_id;
                                }
                            }
                        }
                    }

//                  AQUI EMPIEZA LA LOGICA DEL EDIT  
                    $name = $request->request->get("name");
                    $idCountry = $request->request->get("idCountry");
                    $provinceid = $request->request->get("provinceid");

                    if ($name == "") {
                        return new Response('Ingrese el nombre del centro medico');
                    } else if ($idCountry == "") {
                        return new Response('Seleccione el pais');
                    } else if ($provinceid == "") {
                        return new Response('Seleccione la provincia');
                    } else {

                        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);

                        $medicalcenter->setCountryid($idCountry);
                        $medicalcenter->setProvinceid($provinceid);
                        $medicalcenter->setName($name);
//
                        $medicalcenter->setUpdatedAt($fechaNow);
                        $medicalcenter->setUpdatedBy($user_id);
//                        
                        $dm = $this->get('doctrine_mongodb')->getManager();
                        //$dm->persist($medicalcenter);
                        $dm->flush();

                        return new Response('Operacion exitosa');
                    }
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

}
