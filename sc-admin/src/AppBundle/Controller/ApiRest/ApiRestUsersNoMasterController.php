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

class ApiRestUsersNoMasterController extends Controller {
//  validation_code medical center master: 0 =  inicio, 1 = codigo enviado, 2 = codigo vencido
//  validation_code reset_password: 0 =  inicio, 1 = codigo enviado, 2 = codigo vencido
//  validation_code unlock_user: 0 =  inicio, 1 = codigo enviado, 2 = codigo vencido
//  enabled user_front: 0 =  inactivo, 1 = activo, 2 = bloqueado

    /**
     * @Route("/api/LoadPermitsMedicalCenter")     
     * @Method("GET")
     */
    public function LoadPermitsMedicalCenterAction(Request $request) {

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
                $licenses = "";
                $user = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findOneBy(['_id' => $user_id]);
                if ($user) {
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($valorMedicalCenter->_id);
                                    $licenses = $medicalcenter->getLicenses();
                                    foreach ($licenses as $valor) {
                                        $arrayModules[] = array(
                                            "modules" => $valor["modules"]
                                        );
                                    }
                                }
                            }
                        }

                        foreach ($arrayModules as $array) {
                            //var_dump($array["modules"]);
                            foreach ($array["modules"] as $modules) {

                                foreach ($modules["permits"] as $permits) {
                                    //var_dump($permits["_id"]);
                                    $arrayPermits[] = array(
                                        "label" => $permits["permit"],
                                        "value" => $modules["name"] . "-" . (string) $permits["_id"] . "-" . $permits["permit"]
                                    );
                                }
                                $arrayEnd[] = array(
                                    "label" => $modules["name"],
                                    "options" => $arrayPermits
                                );
                                unset($arrayPermits);
                            }
                        }
                    }
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayEnd, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadModulesMedicalCenter")     
     * @Method("GET")
     */
    public function LoadModulesMedicalCenterAction(Request $request) {

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
                $licenses = "";
                $user = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findOneBy(['_id' => $user_id]);
                if ($user) {
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($valorMedicalCenter->_id);
                                    $licenses = $medicalcenter->getLicenses();
                                    foreach ($licenses as $valor) {
                                        $arrayModules[] = array(
                                            "modules" => $valor["modules"]
                                        );
                                    }
                                }
                            }
                        }

                        foreach ($arrayModules as $array) {
                            //var_dump($array["modules"]);
                            foreach ($array["modules"] as $modules) {

                                $arrayEnd[] = $modules["name"];
                            }
                        }
                    }
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayEnd, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/saveRol")     
     * @Method("POST")
     */
    public function saveRolAction(Request $request) {

        $fechaNow = new \MongoDate();

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
                    $rol = $request->request->get("rol");
                    $selected = $request->request->get("selected");
                    $onlyModules = $request->request->get("onlyModules");

                    if ($rol == "") {
                        return new Response('Ingrese el rol');
                    } else if ($selected == "") {
                        return new Response('Seleccione los modulos');
                    } else {

                        foreach ($onlyModules as $travelOnlyModules) {

                            foreach ($selected as $travelSelected) {

                                $partesSelected = explode("-", $travelSelected);

                                if ($travelOnlyModules == $partesSelected[0]) {

                                    $arrayPemits[] = $partesSelected[2];
                                }
                            }
                            if (!empty($arrayPemits)) {

                                $arrayModules[] = array(
                                    "name" => $travelOnlyModules,
                                    "permits" => $arrayPemits
                                );

                                unset($arrayPemits);
                            }
                        }
                        //var_dump($arrayPemits);
                        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                        $arrayRoles = $medicalcenter->getRoles();
                        $rolId = new \MongoId();

                        $arrayRoles[] = array(
                            "_id" => $rolId,
                            "rol" => $rol,
                            "modules" => $arrayModules,
                            "status" => true,
                            "created_at" => $fechaNow,
                            "created_by" => $user_id,
                            "updated_at" => $fechaNow,
                            "updated_by" => $user_id);

                        $medicalcenter->setRoles($arrayRoles);
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
     * @Route("/api/editRol")     
     * @Method("POST")
     */
    public function editRolAction(Request $request) {

        $fechaNow = new \MongoDate();

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
                    $rol = $request->request->get("rol");
                    $selected = $request->request->get("selected");
                    $onlyModules = $request->request->get("onlyModules");

                    if ($rol == "") {
                        return new Response('Ingrese el rol');
                    } else if ($selected == "") {
                        return new Response('Seleccione los modulos');
                    } else {

                        foreach ($onlyModules as $travelOnlyModules) {

                            foreach ($selected as $travelSelected) {

                                $partesSelected = explode("-", $travelSelected);

                                if ($travelOnlyModules == $partesSelected[0]) {

                                    $arrayPemits[] = $partesSelected[2];
                                }
                            }
                            if (!empty($arrayPemits)) {
                                $arrayModules[] = array(
                                    "name" => $travelOnlyModules,
                                    "permits" => $arrayPemits
                                );

                                unset($arrayPemits);
                            }
                        }

                        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                        $arrayRoles = $medicalcenter->getRoles();

                        $arrayRoles[$posicion]["rol"] = $rol;
                        $arrayRoles[$posicion]["modules"] = $arrayModules;
                        $medicalcenter->setRoles($arrayRoles);

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
     * @Route("/api/LoadRoles")     
     * @Method("GET")
     */
    public function LoadRolesAction(Request $request) {

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
                                    $arrayRoles = $medicalcenter->getRoles();
                                    $arrayEnd = array(
                                        "roles" => $arrayRoles
                                    );
                                }
                            }
                        }
                    }

                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayEnd, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadRolId")     
     * @Method("POST")
     */
    public function LoadRolIdAction(Request $request) {

        $fechaNow = new \MongoDate();

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
                    $arrayRoles = $medicalcenter->getRoles();

                    $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
                    $arrayModulesGeneral = $GeneralConfiguration->getModules();

                    foreach ($arrayRoles as $key => $travelArrayRoles) {
                        if ($key == $posicion) {

                            $nameRol = $travelArrayRoles["rol"];

                            foreach ($travelArrayRoles["modules"] as $travelArrayModules) {

                                $nameModule = $travelArrayModules["name"];

                                foreach ($travelArrayModules["permits"] as $travelArrayPermits) {

                                    foreach ($arrayModulesGeneral as $travelArrayModulesGeneral) {

                                        if ($nameModule == $travelArrayModulesGeneral["name"]) {

                                            foreach ($travelArrayModulesGeneral["permits"] as $travelArrayPermitsGeneral) {

                                                if ($travelArrayPermits == $travelArrayPermitsGeneral["permit"]) {


                                                    $permits = $travelArrayPermitsGeneral["_id"] . "-" . $travelArrayPermitsGeneral["permit"];
                                                }
                                            }
                                            $arrayPermits[] = $nameModule . "-" . $permits;
                                        }
                                    }

                                    //var_dump($arra);
                                }
                            }
                            $arrayEnd = array(
                                "rol" => $nameRol,
                                "modules" => $arrayPermits,
                            );
                            $encoders = array(new XmlEncoder(), new JsonEncoder());
                            $normalizers = array(new ObjectNormalizer());

                            $serializer = new Serializer($normalizers, $encoders);

                            $jsonContent = $serializer->serialize($arrayEnd, 'json');

                            return new Response($jsonContent);
                        }
                    }
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/ViewRolId")     
     * @Method("POST")
     */
    public function ViewRolIdAction(Request $request) {

        $fechaNow = new \MongoDate();

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
                    $rolId = $request->request->get("rolId");

                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                    $arrayRoles = $medicalcenter->getRoles();

                    $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
                    $arrayModulesGeneral = $GeneralConfiguration->getModules();

                    foreach ($arrayRoles as $key => $travelArrayRoles) {
                        if ($rolId == $travelArrayRoles["_id"]) {

                            $nameRol = $travelArrayRoles["rol"];

                            foreach ($travelArrayRoles["modules"] as $travelArrayModules) {

                                $nameModule = $travelArrayModules["name"];

                                foreach ($travelArrayModules["permits"] as $travelArrayPermits) {

                                    foreach ($arrayModulesGeneral as $travelArrayModulesGeneral) {

                                        if ($nameModule == $travelArrayModulesGeneral["name"]) {

                                            foreach ($travelArrayModulesGeneral["permits"] as $travelArrayPermitsGeneral) {

                                                if ($travelArrayPermits == $travelArrayPermitsGeneral["permit"]) {


                                                    $permits = $travelArrayPermitsGeneral["_id"] . "-" . $travelArrayPermitsGeneral["permit"];
                                                }
                                            }
                                            $arrayPermits[] = $nameModule . "-" . $permits;
                                        }
                                    }

                                    //var_dump($arra);
                                }
                            }
                            $arrayEnd = array(
                                "rol" => $nameRol,
                                "modules" => $arrayPermits,
                            );
                            $encoders = array(new XmlEncoder(), new JsonEncoder());
                            $normalizers = array(new ObjectNormalizer());

                            $serializer = new Serializer($normalizers, $encoders);

                            $jsonContent = $serializer->serialize($arrayEnd, 'json');

                            return new Response($jsonContent);
                        }
                    }
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/ViewRolName")     
     * @Method("POST")
     */
    public function ViewRolNameAction(Request $request) {

        $fechaNow = new \MongoDate();

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
                    $rolName = $request->request->get("rolName");

                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                    $arrayRoles = $medicalcenter->getRoles();

                    $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
                    $arrayModulesGeneral = $GeneralConfiguration->getModules();

                    foreach ($arrayRoles as $key => $travelArrayRoles) {
                        if ($rolName == $travelArrayRoles["rol"]) {




                            $arrayEnd = array(
                                "label" => $travelArrayRoles["rol"],
                                "value" => (string) $travelArrayRoles["_id"],
                            );
                            $encoders = array(new XmlEncoder(), new JsonEncoder());
                            $normalizers = array(new ObjectNormalizer());

                            $serializer = new Serializer($normalizers, $encoders);

                            $jsonContent = $serializer->serialize($arrayEnd, 'json');

                            return new Response($jsonContent);
                        }
                    }
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadSelectBranchOffices")     
     * @Method("GET")
     */
    public function LoadSelectBranchOfficesAction(Request $request) {

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
                                    $arrayBranchOffices = $medicalcenter->getBranchoffices();
                                    if (!empty($arrayBranchOffices)) {
                                        foreach ($arrayBranchOffices as $travelArrayBranchOffices) {
                                            $arrayEnd[] = array(
                                                "value" => (string) $travelArrayBranchOffices["_id"],
                                                "label" => $travelArrayBranchOffices["name"]
                                            );
                                        }
                                    } else {
                                        $arrayEnd[] = array();
                                    }
                                }
                            }
                        }
                    }
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayEnd, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/consultBranchOffices")     
     * @Method("GET")
     */
    public function consultBranchOfficesAction(Request $request) {

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
                                    $arrayBranchOffices = $medicalcenter->getBranchoffices();
                                }
                            }
                        }
                    }
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayBranchOffices, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/consultRoles")     
     * @Method("GET")
     */
    public function consultRolesAction(Request $request) {

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
                                    $arrayRoles = $medicalcenter->getRoles();
                                }
                            }
                        }
                    }
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayRoles, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadSelectRoles")     
     * @Method("GET")
     */
    public function LoadSelectRolesAction(Request $request) {

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
                                    $arrayRoles = $medicalcenter->getRoles();

                                    if (!empty($arrayRoles)) {
                                        foreach ($arrayRoles as $travelArrayRoles) {
                                            $arrayEnd[] = array(
                                                "value" => (string) $travelArrayRoles["_id"],
                                                "label" => $travelArrayRoles["rol"]
                                            );
                                        }
                                    } else {
                                        $arrayEnd[] = array(
                                            "value" => "0",
                                            "label" => "No hay roles registrados"
                                        );
                                    }
                                }
                            }
                        }
                    }
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayEnd, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/saveUserNoMaster")     
     * @Method("POST")
     */
    public function saveUserNoMasterAction(Request $request) {

        $fechaNow = new \MongoDate();

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

                    $medicalCenterId = "";
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalCenterId = $valorMedicalCenter->_id;
                                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                                    $nameMedicalCenter = $medicalcenter->getName();
                                }
                            }
                        }

                        $email = $request->request->get("email");
                        $sucursal = $request->request->get("listSuc");
                        $onlyModules = $request->request->get("onlyModules");
                        $groupSucursales = $request->request->get("groupSucursales");

                        if ($email == "") {

                            return new Response('Ingrese el email');
                        } else if (($sucursal == "") || ($sucursal == null)) {

                            return new Response('Seleccione la sucursal');
                        } else {

                            foreach ($groupSucursales as $key => $travelGroupSucursales) {

                                if ($key == 0) {
                                    $is_default = 1;
                                } else {
                                    $is_default = 0;
                                }
                                foreach ($sucursal as $keyList => $travelSucursal) {
                                    if ($travelGroupSucursales["label"] == $travelSucursal["label"]) {
                                        if ($travelSucursal["rol"] != "") {

                                            $arrayRol = array(
                                                "_id" => $travelSucursal["rol"]["value"],
                                                "name" => $travelSucursal["rol"]["label"],
                                                "type" => 0
                                            );

                                            $arrayRoles[] = $arrayRol;
                                            unset($arrayRol);
                                        }
                                    }
                                }
                                foreach ($onlyModules as $travelOnlyModules) {

                                    foreach ($sucursal as $keyList => $travelSucursal) {

                                        if ($travelGroupSucursales["label"] == $travelSucursal["label"]) {

                                            if ($travelSucursal["modulos"] != "") {

                                                $partesSelected = explode("-", $travelSucursal["modulos"]);

                                                if ($travelOnlyModules == $partesSelected[0]) {

                                                    $arrayPemits[] = $partesSelected[2];
                                                }
                                            }
                                        }
                                    }
                                    if (!empty($arrayPemits)) {

                                        $arrayModules[] = array(
                                            "name" => $travelOnlyModules,
                                            "type" => 1,
                                            "permits" => $arrayPemits
                                        );

                                        unset($arrayPemits);
                                    } else {
                                        $arrayModules = [];
                                    }
                                    $arrayPermission = $arrayModules;
                                }
                                unset($arrayModules);

                                if (empty($arrayRoles)) {
                                    $arrayRoles = [];
                                }

                                $resultado = array_merge($arrayRoles, $arrayPermission);

                                $arrayBranchOffices[] = array(
                                    "_id" => $travelGroupSucursales["value"],
                                    "name" => $travelGroupSucursales["label"],
                                    "is_default" => $is_default,
                                    "permission" => $resultado
                                );
                                unset($arrayRoles);
                            }
                            
                            $userExist = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findOneBy(array('email' => $email));
                            if ($userExist) {

                                $user_front = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($userExist->getId());
                                $arrayProfile = $user_front->getProfile();

                                $acum = 0;
                                $acumSucursalesIgual = 0;
                                $acumSucursalesDesIgual = 0;
                                $acumIsDefaultMedicalCenter = 0;
                                $acumIsDefaultSucursal = 0;

                                foreach ($userExist->getProfile() as $key => $travelUserExistProfile) {

                                    if ($travelUserExistProfile["name"] == "internal") {

                                        $pos = $key;

                                        $arrayMedicalCenter = $travelUserExistProfile["medical_center"];

                                        foreach ($travelUserExistProfile["medical_center"] as $keyMedical => $travelMedicalCenter) {

                                            if ($travelMedicalCenter["is_default"] == "1") {
                                                $acumIsDefaultMedicalCenter++;
                                            }

                                            if ($travelMedicalCenter["_id"] == $medicalCenterId) {

                                                $aisDefaultMedicalCenterSucursal = $travelMedicalCenter["is_default"];
                                                $posMedical = $keyMedical;
                                                $acum++;
                                            }
                                        }
                                    }
                                }

                                if ($acum == 0) {

                                    if ($acumIsDefaultMedicalCenter == 1) {
                                        $isDefaultMedicalCenter = 0;
                                    } else {
                                        $isDefaultMedicalCenter = 1;
                                    }

                                    $arrayMedicalCenter[] = array(
                                        "_id" => $medicalCenterId,
                                        "name" => $nameMedicalCenter,
                                        "is_default" => $isDefaultMedicalCenter,
                                        "branch_office" => $arrayBranchOffices
                                    );

                                    $arrayProfile[$pos] = array(
                                        "name" => "internal",
                                        "medical_center" => $arrayMedicalCenter
                                    );

                                    $user_front->setProfile($arrayProfile);
//                        
                                    $dm = $this->get('doctrine_mongodb')->getManager();
                                    //$dm->persist($user_front);
                                    $dm->flush();

                                    $html = "<h3>Se realizaron modificaciones en su usuario en Smart Clinic:</h3>";
                                    $html .= "<table style='width: 50%; text-align: left;'>";
                                    foreach ($arrayProfile as $key => $travelProfile) {
                                        foreach ($travelProfile["medical_center"] as $travelMedicalCenter) {
                                            $html .= "<tr>";
                                            $html .= "<td bgcolor='#E3E3E4'><strong>Centro Medico: </strong></td>";
                                            $html .= "<td bgcolor='#E3E3E4'>" . $travelMedicalCenter["name"] . "</td>";
                                            $html .= "</tr>";
                                            foreach ($travelMedicalCenter["branch_office"] as $travelBranchOffice) {
                                                $html .= "<tr>";
                                                $html .= "<td bgcolor='#E3E3E4'><strong>Sucursal: </strong></td>";
                                                $html .= "<td bgcolor='#E3E3E4'>" . $travelBranchOffice["name"] . "</td>";
                                                $html .= "</tr>";

                                                foreach ($travelBranchOffice["permission"] as $travelPermission) {
                                                    if ($travelPermission["type"] == 1) {
                                                        $permiso = "Modulo: ";
                                                    } else {
                                                        $permiso = "Rol: ";
                                                    }
                                                    $html .= "<tr>";
                                                    $html .= "<td bgcolor='#E3E3E4'><strong>" . $permiso . " </strong></td>";
                                                    $html .= "<td bgcolor='#E3E3E4'>" . $travelPermission["name"] . "</td>";
                                                    $html .= "</tr>";
                                                }
                                            }
                                        }
                                    }
                                    $html .= "</table>";
                                    //$html .= "<h3>Para continuar con el registro acceda al siguiente link:</h3>";
                                    //$html .= "<a href='http://smartclinics.online/sc-front/#/register-email'>http://smartclinics.online/sc-front/#/register-email</a>";

                                    $mailer = $this->container->get('mailer');
                                    //$transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
                                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                                            ->setUsername('smartclinicsoft@gmail.com')
                                            ->setPassword('smartclinic1');

                                    $mailer = \Swift_Mailer::newInstance($transport);
                                    $message = \Swift_Message::newInstance('Test')
                                            ->setSubject('Registro de Usuario')
                                            ->setFrom('smartclinicsoft@gmail.com')
                                            ->setTo($email)
                                            ->setBody($html, 'text/html');
                                    $this->get('mailer')->send($message);

                                    return new Response('Operacion exitosa');
                                } else {

                                    return new Response(1);
                                }
                            } else {

                                $arrayMedicalCenter[] = array(
                                    "_id" => $medicalCenterId,
                                    "name" => $nameMedicalCenter,
                                    "is_default" => 1,
                                    "branch_office" => $arrayBranchOffices
                                );

                                $arrayProfile[] = array(
                                    "name" => "internal",
                                    "medical_center" => $arrayMedicalCenter
                                );

                                $userFront = new UsersFront();
                                $userFront->setEmail($email);
                                $userFront->setEnabled(1);
                                $userFront->setPassword("");
                                $userFront->setProfileIsDefault("internal");
                                $userFront->setProfile($arrayProfile);
                                $userFront->setSecretQuestion1("");
                                $userFront->setSecretQuestion2("");
                                $userFront->setSecretQuestion3("");
                                $userFront->setSecretAnswer1("");
                                $userFront->setSecretAnswer2("");
                                $userFront->setSecretAnswer3("");
                                $userFront->setCreatedAt($fechaNow);
                                $userFront->setCreatedBy($data_token["id"]);
                                $userFront->setUpdatedAt($fechaNow);
                                $userFront->setUpdatedBy($data_token["id"]);
                                $dm = $this->get('doctrine_mongodb')->getManager();
                                $dm->persist($userFront);
                                $dm->flush();


                                $html = "<h3>Usted ha sido registrado en Smart Clinic:</h3>";
                                $html .= "<table style='width: 50%; text-align: left;'>";
                                foreach ($arrayProfile as $key => $travelProfile) {
                                    foreach ($travelProfile["medical_center"] as $travelMedicalCenter) {
                                        $html .= "<tr>";
                                        $html .= "<td bgcolor='#E3E3E4'><strong>Centro Medico: </strong></td>";
                                        $html .= "<td bgcolor='#E3E3E4'>" . $travelMedicalCenter["name"] . "</td>";
                                        $html .= "</tr>";
                                        foreach ($travelMedicalCenter["branch_office"] as $travelBranchOffice) {
                                            $html .= "<tr>";
                                            $html .= "<td bgcolor='#E3E3E4'><strong>Sucursal: </strong></td>";
                                            $html .= "<td bgcolor='#E3E3E4'>" . $travelBranchOffice["name"] . "</td>";
                                            $html .= "</tr>";

                                            foreach ($travelBranchOffice["permission"] as $travelPermission) {
                                                if ($travelPermission["type"] == 1) {
                                                    $permiso = "Modulo: ";
                                                } else {
                                                    $permiso = "Rol: ";
                                                }
                                                $html .= "<tr>";
                                                $html .= "<td bgcolor='#E3E3E4'><strong>" . $permiso . " </strong></td>";
                                                $html .= "<td bgcolor='#E3E3E4'>" . $travelPermission["name"] . "</td>";
                                                $html .= "</tr>";
                                            }
                                        }
                                    }
                                }
                                $html .= "</table>";
                                $html .= "<h3>Para continuar con el registro acceda al siguiente link:</h3>";
                                $html .= "<a href='http://smartclinics.online/sc-front/#/register-email'>http://smartclinics.online/sc-front/#/register-email</a>";

                                $mailer = $this->container->get('mailer');
                                //$transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
                                $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                                        ->setUsername('smartclinicsoft@gmail.com')
                                        ->setPassword('smartclinic1');

                                $mailer = \Swift_Mailer::newInstance($transport);
                                $message = \Swift_Message::newInstance('Test')
                                        ->setSubject('Registro de Usuario')
                                        ->setFrom('smartclinicsoft@gmail.com')
                                        ->setTo($email)
                                        ->setBody($html, 'text/html');
                                $this->get('mailer')->send($message);

                                return new Response('Operacion exitosa');
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

    /**
     * @Route("/api/editUserNoMaster")     
     * @Method("POST")
     */
    public function editUserNoMasterAction(Request $request) {

        $fechaNow = new \MongoDate();

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

                    $medicalCenterId = "";
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalCenterId = $valorMedicalCenter->_id;
                                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                                    $nameMedicalCenter = $medicalcenter->getName();
                                }
                            }
                        }

                        $id = $request->request->get("id");
                        $email = $request->request->get("email");
                        $sucursal = $request->request->get("listSuc");
                        $onlyModules = $request->request->get("onlyModules");
                        $groupSucursales = $request->request->get("groupSucursales");

                        if ($email == "") {

                            return new Response('Ingrese el email');
                        } else if (($sucursal == "") || ($sucursal == null)) {

                            return new Response('Seleccione la sucursal');
                        } else {

                            foreach ($groupSucursales as $key => $travelGroupSucursales) {

                                if ($key == 0) {
                                    $is_default = 1;
                                } else {
                                    $is_default = 0;
                                }
                                foreach ($sucursal as $keyList => $travelSucursal) {
                                    if ($travelGroupSucursales["label"] == $travelSucursal["label"]) {
                                        if ($travelSucursal["rol"] != "") {

                                            $arrayRol = array(
                                                "_id" => $travelSucursal["rol"]["value"],
                                                "name" => $travelSucursal["rol"]["label"],
                                                "type" => 0
                                            );

                                            $arrayRoles[] = $arrayRol;
                                            unset($arrayRol);
                                        }
                                    }
                                }
                                foreach ($onlyModules as $travelOnlyModules) {

                                    foreach ($sucursal as $keyList => $travelSucursal) {

                                        if ($travelGroupSucursales["label"] == $travelSucursal["label"]) {

                                            if ($travelSucursal["modulos"] != "") {

                                                $partesSelected = explode("-", $travelSucursal["modulos"]);

                                                if ($travelOnlyModules == $partesSelected[0]) {

                                                    $arrayPemits[] = $partesSelected[2];
                                                }
                                            }
                                        }
                                    }
                                    if (!empty($arrayPemits)) {

                                        $arrayModules[] = array(
                                            "name" => $travelOnlyModules,
                                            "type" => 1,
                                            "permits" => $arrayPemits
                                        );

                                        unset($arrayPemits);
                                    } else {
                                        $arrayModules = [];
                                    }
                                    $arrayPermission = $arrayModules;
                                }
                                unset($arrayModules);

                                if (empty($arrayRoles)) {
                                    $arrayRoles = [];
                                }

                                $resultado = array_merge($arrayRoles, $arrayPermission);
//
                                $arrayBranchOffices[] = array(
                                    "_id" => $travelGroupSucursales["value"],
                                    "name" => $travelGroupSucursales["label"],
                                    "is_default" => $is_default,
                                    "permission" => $resultado
                                );
                                unset($arrayRoles);
                            }
//                            $encoders = array(new XmlEncoder(), new JsonEncoder());
//                            $normalizers = array(new ObjectNormalizer());
//                            $serializer = new Serializer($normalizers, $encoders);
//                            $jsonContent = $serializer->serialize($arrayBranchOffices, 'json');
//                            return new Response($jsonContent);

                            $userExist = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($id);
                            if ($userExist) {

                                $acum = 0;
                                $acumIsDefaultMedicalCenter = 0;

                                $arrayProfile = $userExist->getProfile();
                                foreach ($arrayProfile as $key => $travelUserExistProfile) {

                                    if ($travelUserExistProfile["name"] == "internal") {

                                        $pos = $key;

                                        foreach ($travelUserExistProfile["medical_center"] as $keyMedical => $travelMedicalCenter) {

                                            if ($travelMedicalCenter["is_default"] == "1") {
                                                $acumIsDefaultMedicalCenter++;
                                            }

                                            if ($travelMedicalCenter["_id"] == $medicalCenterId) {

                                                $acum++;
                                            }
                                        }
                                    }
                                }
                                if ($acumIsDefaultMedicalCenter == 1) {
                                    $isDefaultMedicalCenter = 0;
                                } else {
                                    $isDefaultMedicalCenter = 1;
                                }

                                $arrayMedicalCenter[] = array(
                                    "_id" => $medicalCenterId,
                                    "name" => $nameMedicalCenter,
                                    "is_default" => $isDefaultMedicalCenter,
                                    "branch_office" => $arrayBranchOffices
                                );

                                $arrayProfile[$pos] = array(
                                    "name" => "internal",
                                    "medical_center" => $arrayMedicalCenter
                                );

                                $userExist->setEmail($email);
                                $userExist->setProfile($arrayProfile);
//                        
                                $dm = $this->get('doctrine_mongodb')->getManager();
                                //$dm->persist($user_front);
                                $dm->flush();

                                $html = "<h3>Se realizaron modificaciones en su usuario en Smart Clinic:</h3>";
                                $html .= "<table style='width: 50%; text-align: left;'>";
                                foreach ($arrayProfile as $key => $travelProfile) {
                                    foreach ($travelProfile["medical_center"] as $travelMedicalCenter) {
                                        $html .= "<tr>";
                                        $html .= "<td bgcolor='#E3E3E4'><strong>Centro Medico: </strong></td>";
                                        $html .= "<td bgcolor='#E3E3E4'>" . $travelMedicalCenter["name"] . "</td>";
                                        $html .= "</tr>";
                                        foreach ($travelMedicalCenter["branch_office"] as $travelBranchOffice) {
                                            $html .= "<tr>";
                                            $html .= "<td bgcolor='#E3E3E4'><strong>Sucursal: </strong></td>";
                                            $html .= "<td bgcolor='#E3E3E4'>" . $travelBranchOffice["name"] . "</td>";
                                            $html .= "</tr>";

                                            foreach ($travelBranchOffice["permission"] as $travelPermission) {
                                                if ($travelPermission["type"] == 1) {
                                                    $permiso = "Modulo: ";
                                                } else {
                                                    $permiso = "Rol: ";
                                                }
                                                $html .= "<tr>";
                                                $html .= "<td bgcolor='#E3E3E4'><strong>" . $permiso . " </strong></td>";
                                                $html .= "<td bgcolor='#E3E3E4'>" . $travelPermission["name"] . "</td>";
                                                $html .= "</tr>";
                                            }
                                        }
                                    }
                                }
                                $html .= "</table>";
                                //$html .= "<h3>Para continuar con el registro acceda al siguiente link:</h3>";
                                //$html .= "<a href='http://smartclinics.online/sc-front/#/register-email'>http://smartclinics.online/sc-front/#/register-email</a>";

                                $mailer = $this->container->get('mailer');
                                //$transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
                                $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                                        ->setUsername('smartclinicsoft@gmail.com')
                                        ->setPassword('smartclinic1');

                                $mailer = \Swift_Mailer::newInstance($transport);
                                $message = \Swift_Message::newInstance('Test')
                                        ->setSubject('Registro de Usuario')
                                        ->setFrom('smartclinicsoft@gmail.com')
                                        ->setTo($email)
                                        ->setBody($html, 'text/html');
                                $this->get('mailer')->send($message);

                                return new Response('Operacion exitosa');
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

    /**
     * @Route("/api/LoadAllUsersNoMaster")     
     * @Method("GET")
     */
    public function LoadAllUsersNoMasterAction(Request $request) {

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
                                    $medicalCenterId = $medicalcenter->getId();
                                }
                            }
                        }
                    }

                    $allUsersNoMaster = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('profile.medical_center._id' => $medicalCenterId, 'enabled' => 1));

                    foreach ($allUsersNoMaster as $travelAllUsersNoMaster) {
                        $email = $travelAllUsersNoMaster->getEmail();
                        $id = $travelAllUsersNoMaster->getId();
                        $estado = $travelAllUsersNoMaster->getEnabled();

                        foreach ($travelAllUsersNoMaster->getProfile() as $travelProfile) {

                            if ($travelProfile["name"] == "internal") {

                                foreach ($travelProfile["medical_center"] as $travelMedicalCenter) {

                                    if ($travelMedicalCenter["_id"] == $medicalCenterId) {
                                        $acumMAster = 0;
                                        foreach ($travelMedicalCenter["branch_office"] as $travelBranchOffice) {

                                            foreach ($travelBranchOffice["permission"] as $travelPermission) {

                                                if ($travelPermission["name"] == "MASTER") {
                                                    $acumMAster++;
                                                }
                                            }
                                        }
                                        if ($acumMAster == 0) {
                                            $arrayUsersNoMaster[] = array(
                                                "id" => $id,
                                                "email" => $email,
                                                "estado" => $estado
                                            );
                                        } else {
                                            $arrayUsersNoMaster = [];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $arrayEnd = array(
                        "users" => $arrayUsersNoMaster
                    );
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayEnd, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/ValidateEmailUserNoMaster")     
     * @Method("POST")
     */
    public function ValidateEmailUserNoMasterAction(Request $request) {

        $fechaNow = new \MongoDate();

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
                    $email = $request->request->get("email");

                    $emailUserNoMaster = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findOneBy(array('email' => $email));
                    if ($emailUserNoMaster) {
                        $estadoUser = $emailUserNoMaster->getEnabled();

                        foreach ($emailUserNoMaster->getProfile() as $travelProfile) {

                            if ($travelProfile["name"] == "internal") {
                                $profile = "Personal Interno";
                            } else if ($travelProfile["name"] == "external") {
                                $profile = "Personal Externo";
                            } else if ($travelProfile["name"] == "client") {
                                $profile = "Cliente";
                            }
                            $acumMedicalCenter = 0;
                            foreach ($travelProfile["medical_center"] as $travelMedicalCenter) {
                                $medicalCenter = $travelMedicalCenter["name"];
                                $medicalCenterId = $travelMedicalCenter["_id"];
                                if ($travelMedicalCenter["_id"] == $medicalCenterId) {
                                    $acumMedicalCenter++;
                                }

                                foreach ($travelMedicalCenter["branch_office"] as $travelBranchOffice) {

                                    $branchOffice = $travelBranchOffice["name"];
                                    $branchOfficeId = $travelBranchOffice["_id"];

                                    foreach ($travelBranchOffice["permission"] as $travelPermission) {

                                        if ($travelPermission["type"] == 1) {
                                            $permiso = "Modulo: " . $travelPermission["name"];
                                        } else {
                                            $permiso = "Rol: " . $travelPermission["name"];
                                        }
                                        $arrayPermission[] = array(
                                            "name" => $permiso
                                        );
                                    }
                                    $arrayBranchOffices[] = array(
                                        "branchOfficeId" => $branchOfficeId,
                                        "name" => $branchOffice,
                                        "permission" => $arrayPermission
                                    );
                                    unset($arrayPermission);
                                }
                                $arrayMedicalCenter[] = array(
                                    "medicalCenterId" => $medicalCenterId,
                                    "name" => $medicalCenter,
                                    "branchOffice" => $arrayBranchOffices
                                );
                                unset($arrayBranchOffices);
                            }
                        }
                        $arrayEnd = array(
                            "exist" => $acumMedicalCenter,
                            "estado" => $estadoUser,
                            "infoEmail" => $arrayMedicalCenter
                        );
                    } else {
                        $arrayEnd = null;
                    }

                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayEnd, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadIdUsersNoMaster")     
     * @Method("POST")
     */
    public function LoadIdUsersNoMasterAction(Request $request) {

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
                                    $medicalCenterId = $medicalcenter->getId();
                                }
                            }
                        }
                    }
                    $id = $request->request->get("id");
                    $userId = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($id);
                    $email = $userId->getEmail();

                    $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
                    $arrayModulesGeneral = $GeneralConfiguration->getModules();

                    foreach ($userId->getProfile() as $travelUserId) {
                        if ($travelUserId["name"] == "internal") {

                            foreach ($travelUserId["medical_center"] as $travelMedicalCenter) {

                                if ($travelMedicalCenter["_id"] == $medicalCenterId) {

                                    foreach ($travelMedicalCenter["branch_office"] as $travelBranchOffice) {

                                        foreach ($travelBranchOffice["permission"] as $travelPermission) {

                                            if ($travelPermission["type"] == 0) {

                                                $arrayRol = array(
                                                    "label" => $travelPermission["name"],
                                                    "value" => $travelPermission["_id"]
                                                );
                                                $arrayPermits = "";
                                                $modulosMostrar = "";

                                                $arrayBranhOffice[] = array(
                                                    "label" => $travelBranchOffice["name"],
                                                    "value" => $travelBranchOffice["_id"],
                                                    "rol" => $arrayRol,
                                                    "modulos" => $arrayPermits,
                                                    "moduloMostrar" => $modulosMostrar
                                                );
                                            } else {
                                                $arrayRol = "";
                                                $nameModule = $travelPermission["name"];
                                                foreach ($travelPermission["permits"] as $travelArrayPermits) {
                                                    //var_dump($travelArrayPermits);
                                                    foreach ($arrayModulesGeneral as $travelArrayModulesGeneral) {
                                                        //var_dump($travelArrayModulesGeneral["name"]);
                                                        if ($nameModule == $travelArrayModulesGeneral["name"]) {
                                                            //var_dump($nameModule);
                                                            foreach ($travelArrayModulesGeneral["permits"] as $travelArrayPermitsGeneral) {

                                                                if ($travelArrayPermits == $travelArrayPermitsGeneral["permit"]) {
                                                                    //var_dump($travelArrayPermitsGeneral["permit"]);
                                                                    $permits = $travelArrayPermitsGeneral["_id"] . "-" . $travelArrayPermitsGeneral["permit"];
                                                                    $arrayPermits = $nameModule . "-" . $permits;
                                                                    $modulosMostrar = $nameModule . "-" . $travelArrayPermitsGeneral["permit"];
                                                                    //var_dump($nameModule);
                                                                    $arrayBranhOffice[] = array(
                                                                        "label" => $travelBranchOffice["name"],
                                                                        "value" => $travelBranchOffice["_id"],
                                                                        "rol" => $arrayRol,
                                                                        "modulos" => $arrayPermits,
                                                                        "moduloMostrar" => $modulosMostrar
                                                                    );
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }

//                                            unset($arrayPermits);
//                                            unset($arrayRol);
                                        }
                                    }

                                    $arrayEnd = array(
                                        "email" => $email,
                                        "sucursal" => $arrayBranhOffice
                                    );
                                }
                            }
                            $encoders = array(new XmlEncoder(), new JsonEncoder());
                            $normalizers = array(new ObjectNormalizer());
                            $serializer = new Serializer($normalizers, $encoders);
                            $jsonContent = $serializer->serialize($arrayEnd, 'json');
                            return new Response($jsonContent);
                        }
                    }
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/DeleteUserNoMaster")     
     * @Method("POST")
     */
    public function DeleteUserNoMasterAction(Request $request) {

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
                                }
                            }
                        }
                    }

                    $userId = $request->request->get("userId");
                    $userFront = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($userId);
                    $userFront->setEnabled(0);
                    $dm = $this->get('doctrine_mongodb')->getManager();
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
     * @Route("/api/enviarEmail")     
     * @Method("PUT")
     */
    public function enviarEmailAction(Request $request) {

        $date = new \MongoDate();
        $email = $request->request->get("email");

        $check_mail = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('master.email' => $email));

        if (!$check_mail) {

            return new Response('¡Email no encontrado!');
        } else {

            $mailer = $this->container->get('mailer');

            //$transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
            $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                    ->setUsername('smartclinicsoft@gmail.com')
                    ->setPassword('smartclinic1');

            $mailer = \Swift_Mailer::newInstance($transport);
            $message = \Swift_Message::newInstance('Test')
                    ->setSubject('Codigo de Validacion')
                    ->setFrom('smartclinicsoft@gmail.com')
                    ->setTo($email)
                    ->setBody('
                        <h3>Usted ha sido registrado en Smart Clinic:</h3> 
                        <table style="width: 30%; text-align: left;">
                            <tr>
                              <td bgcolor="#E3E3E4"><strong>Centro Medico: </strong></td>
                              <td bgcolor="#E3E3E4">Policlinica</td>  
                            </tr>
                            <tr>
                              <td bgcolor="#E3E3E4"><strong>Sucursal: </strong></td>
                              <td bgcolor="#E3E3E4">Madre Teresa</td>                          
                            </tr>
                            <tr>
                              <td bgcolor="#E3E3E4"><strong>Rol: </strong></td>
                              <td bgcolor="#E3E3E4">Interno</td>                          
                            </tr>                        
                        </table>
                        <h3>Para continuar con el registro acceda al siguiente link:</h3>
                        http://smartclinics.online/sc-front/#/register-email', 'text/html');
            $this->get('mailer')->send($message);

            return new Response('Operacion exitosa');
        }
    }

    /**
     * @Route("/api/RolesPermisosValidateEmailUserNoMaster")     
     * @Method("POST")
     */
    public function RolesPermisosValidateEmailUserNoMasterAction(Request $request) {

        $fechaNow = new \MongoDate();

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
                    $email = $request->request->get("email");
                    $medicalCenterIdPost = $request->request->get("medicalCenterId");
                    $branchOfficeIdPost = $request->request->get("branchOfficeId");

                    $emailUserNoMaster = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findOneBy(array('email' => $email));
                    if ($emailUserNoMaster) {
                        $estadoUser = $emailUserNoMaster->getEnabled();

                        foreach ($emailUserNoMaster->getProfile() as $travelProfile) {

                            foreach ($travelProfile["medical_center"] as $travelMedicalCenter) {
                                if ($travelMedicalCenter["_id"] == $medicalCenterIdPost) {

                                    foreach ($travelMedicalCenter["branch_office"] as $travelBranchOffice) {

                                        if ($travelBranchOffice["_id"] == $branchOfficeIdPost) {

                                            foreach ($travelBranchOffice["permission"] as $travelPermission) {

                                                if ($travelPermission["type"] == 1) {
                                                    $permiso = "Modulo: " . $travelPermission["name"];
                                                } else {
                                                    $permiso = "Rol: " . $travelPermission["name"];
                                                }
                                                $arrayPermission[] = array(
                                                    "name" => $permiso
                                                );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        $arrayEnd = array(
                            "estado" => $estadoUser
                        );
                    } else {
                        $arrayEnd = null;
                    }

                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayPermission, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

}
