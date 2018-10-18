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
                                    
                                    $arrayModules[] = array(
                                        "name" => $travelOnlyModules,
                                        "permits" => $arrayPemits
                                    );
                                }
                            }

                            unset($arrayPemits);
                        }
                        //var_dump($arrayPemits);
                        $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($medicalCenterId);
                        $arrayRoles = $medicalcenter->getRoles();

                        $arrayRoles[] = array(
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

}
