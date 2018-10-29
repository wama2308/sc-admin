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

                                    foreach ($arrayRoles as $travelArrayRoles) {
                                        $arrayEnd[] = array(
                                            "value" => (string) $travelArrayRoles["_id"],
                                            "label" => $travelArrayRoles["rol"]
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
                        $sucursal = $request->request->get("sucursal");
                        $rol = $request->request->get("rol");
                        $modules = $request->request->get("modules");
                        $onlyModules = $request->request->get("onlyModules");

                        if ($email == "") {

                            return new Response('Ingrese el email');
                        } else if (($sucursal == "") || ($sucursal == null)) {

                            return new Response('Seleccione la sucursal');
                        } else if ((($rol == "") || ($rol == null)) && (($modules == "") || ($modules == null))) {

                            return new Response('Seleccione el rol o los modulos');
                        } else {

                            /////////////////////////////////ROLES Y MODULOS
                            if (!empty($modules)) {

                                foreach ($onlyModules as $travelOnlyModules) {

                                    foreach ($modules as $travelSelected) {

                                        $partesSelected = explode("-", $travelSelected);

                                        if ($travelOnlyModules == $partesSelected[0]) {

                                            $arrayPemits[] = $partesSelected[2];
                                        }
                                    }
                                    if (!empty($arrayPemits)) {

                                        $arrayModules[] = array(
                                            "name" => $travelOnlyModules,
                                            "type" => 1,
                                            "permits" => $arrayPemits
                                        );

                                        unset($arrayPemits);
                                    }
                                }
                                $arrayPermission = $arrayModules;
                            }

                            if (!empty($rol)) {

                                $arrayRol = array(
                                    "_id" => $rol["value"],
                                    "name" => $rol["label"],
                                    "type" => 0
                                );

                                $arrayPermission[] = $arrayRol;
                            }
                            /////////////////////////////////ROLES Y MODULOS

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
                                                $arrayBranchOffices = $travelMedicalCenter["branch_office"];
                                                foreach ($sucursal as $key => $travelSucursal) {

                                                    foreach ($travelMedicalCenter["branch_office"] as $travelBranchOffice) {
                                                        if ($travelBranchOffice["is_default"] == "1") {
                                                            $acumIsDefaultSucursal++;
                                                        }

                                                        if ($travelBranchOffice["_id"] == $travelSucursal["value"]) {

                                                            $acumSucursalesIgual++;
                                                        } else {

                                                            $acumSucursalesDesIgual = $acumSucursalesIgual;
                                                        }
                                                    }

                                                    if (($acumSucursalesIgual == 0) && ($acumSucursalesDesIgual == 0)) {

                                                        if ($acumIsDefaultSucursal == 1) {
                                                            $isDefaultSucursal = 0;
                                                        } else {
                                                            $isDefaultSucursal = 1;
                                                        }

                                                        $arrayBranchOffices[] = array(
                                                            "_id" => $travelSucursal["value"],
                                                            "name" => $travelSucursal["label"],
                                                            "is_default" => $isDefaultSucursal,
                                                            "permission" => $arrayPermission
                                                        );
                                                    }
                                                    $acumSucursalesIgual = 0;
                                                    $acumSucursalesDesIgual = 0;
                                                }
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

                                    foreach ($sucursal as $key => $travelSucursal) {

                                        if ($key == 0) {
                                            $is_default = 1;
                                        } else {
                                            $is_default = 0;
                                        }

                                        $arrayBranchOffices[] = array(
                                            "_id" => $travelSucursal["value"],
                                            "name" => $travelSucursal["label"],
                                            "is_default" => $is_default,
                                            "permission" => $arrayPermission
                                        );
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

                                    return new Response('Operacion exitosa');
                                } else if ($acum == 1) {

                                    $arrayMedicalCenter[$posMedical] = array(
                                        "_id" => $medicalCenterId,
                                        "name" => $nameMedicalCenter,
                                        "is_default" => $aisDefaultMedicalCenterSucursal,
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

                                    return new Response('Operacion exitosa');
                                }
                            } else {
                                foreach ($sucursal as $key => $travelSucursal) {

                                    if ($key == 0) {
                                        $is_default = 1;
                                    } else {
                                        $is_default = 0;
                                    }

                                    $arrayBranchOffices[] = array(
                                        "_id" => $travelSucursal["value"],
                                        "name" => $travelSucursal["label"],
                                        "is_default" => $is_default,
                                        "permission" => $arrayPermission
                                    );
                                }

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

                                return new Response('Operacion exitosa');
                            }
                        }

                        return new Response("11111");
                    }
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

}
