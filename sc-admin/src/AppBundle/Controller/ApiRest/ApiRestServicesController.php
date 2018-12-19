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

class ApiRestServicesController extends Controller {

    /**
     * @Route("/api/LoadServicesPreloaded")     
     * @Method("GET")
     */
    public function LoadServicesPreloadedAction(Request $request) {

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
                                    $ArrayLicenses = $medicalcenter->getLicenses();

                                    foreach ($ArrayLicenses as $TravelArrayLicenses) {
                                        $arrayDate = date('Y-m-d H:i:s', $TravelArrayLicenses["expiration_date"]->sec);
                                        $fechaActual = date('Y-m-d h:i:s');

                                        if ($arrayDate >= $fechaActual) {

                                            $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($TravelArrayLicenses["license_id"]);
                                            $servicesLicense = $license->getExams();
                                            $licenseId = $license->getId();

                                            foreach ($servicesLicense as $travelServicesLicense) {

                                                $services = $this->get('doctrine_mongodb')->getRepository('AppBundle:Services')->find($travelServicesLicense);

                                                $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
                                                $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();

                                                foreach ($ArrayCategoryExams as $key => $TravelArrayCategoryExams) {

                                                    if ($key == $services->getCategory()) {
                                                        $category = $TravelArrayCategoryExams;
                                                    }
                                                }

                                                if ($services->getActive() == 1) {

                                                    $arrayServices[] = array(
                                                        "licenseId" => $licenseId,
                                                        "serviceId" => $services->getId(),
                                                        "serviceName" => $services->getName(),
                                                        "category" => $category,
                                                        "fields" => $services->getFields(),
                                                        "format" => $services->getFormat()
                                                    );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayServices, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadServicesPreloadedId")     
     * @Method("POST")
     */
    public function LoadServicesPreloadedIdAction(Request $request) {

        $token = $request->headers->get('access-token');
        $licenseIdPost = $request->request->get("licenseId");
        $serviceIdPost = $request->request->get("serviceId");

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

                                        if ($TravelArrayLicenses["license_id"] == $licenseIdPost) {

                                            $arrayDate = date('Y-m-d H:i:s', $TravelArrayLicenses["expiration_date"]->sec);
                                            $fechaActual = date('Y-m-d h:i:s');

                                            if ($arrayDate >= $fechaActual) {

                                                if (!empty($TravelArrayLicenses["services"])) {

                                                    $servicesArray = $TravelArrayLicenses["services"];

                                                    foreach ($servicesArray as $keyServices => $travelServicesArray) {

                                                        $category = array(
                                                            "label" => $travelServicesArray["category"],
                                                            "value" => $travelServicesArray["category"]
                                                        );

                                                        if ($travelServicesArray["_id"] == $serviceIdPost) {

                                                            if ($travelServicesArray["active"] == 1) {

                                                                $arrayServices = array(
                                                                    "licenseId" => $licenseIdPost,
                                                                    "serviceId" => $serviceIdPost,
                                                                    "serviceName" => $travelServicesArray["name"],
                                                                    "category" => $category,
                                                                    "fields" => $travelServicesArray["fields"],
                                                                    "format" => $travelServicesArray["format"],
                                                                    "amount" => $travelServicesArray["amount"],
                                                                    "currencySymbol" => $currencySymbol,
                                                                    "status" => 1
                                                                );
                                                            }
                                                        } else {

                                                            $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($TravelArrayLicenses["license_id"]);
                                                            $servicesLicense = $license->getExams();
                                                            $licenseId = $license->getId();

                                                            foreach ($servicesLicense as $travelServicesLicense) {

                                                                if ($travelServicesLicense == $serviceIdPost) {

                                                                    $services = $this->get('doctrine_mongodb')->getRepository('AppBundle:Services')->find($travelServicesLicense);

                                                                    $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
                                                                    $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();

                                                                    foreach ($ArrayCategoryExams as $key => $TravelArrayCategoryExams) {

                                                                        if ($key == $services->getCategory()) {
                                                                            $category = array(
                                                                                "label" => $TravelArrayCategoryExams,
                                                                                "value" => $TravelArrayCategoryExams
                                                                            );
                                                                        }
                                                                    }

                                                                    if ($services->getActive() == 1) {

                                                                        $arrayServices = array(
                                                                            "licenseId" => $licenseId,
                                                                            "serviceId" => $services->getId(),
                                                                            "serviceName" => $services->getName(),
                                                                            "category" => $category,
                                                                            "fields" => $services->getFields(),
                                                                            "format" => $services->getFormat(),
                                                                            "amount" => "0.00",
                                                                            "currencySymbol" => $currencySymbol,
                                                                            "status" => 0
                                                                        );
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else {

                                                    $license = $this->get('doctrine_mongodb')->getRepository('AppBundle:License')->find($TravelArrayLicenses["license_id"]);
                                                    $servicesLicense = $license->getExams();
                                                    $licenseId = $license->getId();

                                                    foreach ($servicesLicense as $travelServicesLicense) {

                                                        if ($travelServicesLicense == $serviceIdPost) {

                                                            $services = $this->get('doctrine_mongodb')->getRepository('AppBundle:Services')->find($travelServicesLicense);

                                                            $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
                                                            $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();

                                                            foreach ($ArrayCategoryExams as $key => $TravelArrayCategoryExams) {

                                                                if ($key == $services->getCategory()) {
                                                                    $category = array(
                                                                        "label" => $TravelArrayCategoryExams,
                                                                        "value" => $TravelArrayCategoryExams
                                                                    );
                                                                }
                                                            }

                                                            if ($services->getActive() == 1) {

                                                                $arrayServices = array(
                                                                    "licenseId" => $licenseId,
                                                                    "serviceId" => $services->getId(),
                                                                    "serviceName" => $services->getName(),
                                                                    "category" => $category,
                                                                    "fields" => $services->getFields(),
                                                                    "format" => $services->getFormat(),
                                                                    "amount" => "0.00",
                                                                    "currencySymbol" => $currencySymbol,
                                                                    "status" => 0
                                                                );
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $encoders = array(new XmlEncoder(), new JsonEncoder());
                    $normalizers = array(new ObjectNormalizer());

                    $serializer = new Serializer($normalizers, $encoders);

                    $jsonContent = $serializer->serialize($arrayServices, 'json');

                    return new Response($jsonContent);
                } else {

                    $data = array('message' => 'Error al consultar los datos, problemas con el token');
                    return new JsonResponse($data, 403);
                }
            }
        }
    }

    /**
     * @Route("/api/LoadSelectCategory")     
     * @Method("GET")
     */
    public function LoadSelectsCategoryAction(Request $request) {

        $fechaNow = new \MongoDate();

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArrayCategoryExams = $GeneralConfiguration->getCategoryexams();

        foreach ($ArrayCategoryExams as $travelArrayCategoryExams) {
            $arrayCategories[] = array(
                "value" => $travelArrayCategoryExams,
                "label" => $travelArrayCategoryExams,
            );
        }

        $jsonContent = $serializer->serialize($arrayCategories, 'json');

        return new Response($jsonContent);
    }

    /**
     * @Route("/api/EditService")     
     * @Method("POST")
     */
    public function EditServiceAction(Request $request) {

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
                    if ($data_token["profile_is_default"] == "internal") {
                        foreach ($data_token['profile'] as $valor) {

                            foreach ($valor->medical_center as $valorMedicalCenter) {
                                if ($valorMedicalCenter->is_default == "1") {
                                    $medicalcenter = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($valorMedicalCenter->_id);
                                    $arrayLicenses = $medicalcenter->getLicenses();

                                    $servicePost = $request->request->get("service");
                                    $categoryPost = $request->request->get("category");
                                    $amountPost = $request->request->get("amount");
                                    $formatPost = $request->request->get("format");
                                    $fieldsPost = $request->request->get("fields");
                                    $positionPost = $request->request->get("position");
                                    $licenseIdPost = $request->request->get("licenseId");
                                    $serviceIdPost = $request->request->get("serviceId");

                                    if ($servicePost == "") {

                                        return new Response('Ingrese el servicio');
                                    } else if ($categoryPost == "") {

                                        return new Response('Seleccione la categoria');
                                    } else if ($amountPost == "") {

                                        return new Response('Ingrese el monto');
                                    } else if ($formatPost == "") {

                                        return new Response('Ingrese el formato');
                                    } else {

                                        $ArrayLicenses = $medicalcenter->getLicenses();
                                        foreach ($ArrayLicenses as $key => $TravelArrayLicenses) {

                                            if ($TravelArrayLicenses["license_id"] == $licenseIdPost) {

                                                $arrayDate = date('Y-m-d H:i:s', $TravelArrayLicenses["expiration_date"]->sec);
                                                $fechaActual = date('Y-m-d h:i:s');

                                                if ($arrayDate >= $fechaActual) {

                                                    if (!empty($TravelArrayLicenses["services"])) {

                                                        $servicesArray = $TravelArrayLicenses["services"];

                                                        foreach ($servicesArray as $keyServices => $travelServicesArray) {

                                                            $category = array(
                                                                "label" => $travelServicesArray["category"],
                                                                "value" => $travelServicesArray["category"]
                                                            );

                                                            if ($travelServicesArray["_id"] == $serviceIdPost) {

                                                                if ($travelServicesArray["active"] == 1) {

                                                                    $arrayServices[$keyServices] = array(
                                                                        "_id" => $serviceIdPost,
                                                                        "name" => $servicePost,
                                                                        "category" => $categoryPost,
                                                                        "format" => $formatPost,
                                                                        "fields" => $fieldsPost,
                                                                        "amount" => $amountPost,
                                                                        "active" => true,
                                                                        "created_at" => $fechaNow,
                                                                        "created_by" => $user_id,
                                                                        "updated_at" => $fechaNow,
                                                                        "updated_by" => $user_id,
                                                                    );

                                                                    $arrayLicenses[$key] = array(
                                                                        "license_id" => $TravelArrayLicenses["license_id"],
                                                                        "expiration_date" => $TravelArrayLicenses["expiration_date"],
                                                                        "status" => $TravelArrayLicenses["status"],
                                                                        "modules" => $TravelArrayLicenses["modules"],
                                                                        "services" => $arrayServices,
                                                                        "created_at" => $TravelArrayLicenses["created_at"],
                                                                        "created_by" => $TravelArrayLicenses["created_by"],
                                                                        "updated_at" => $TravelArrayLicenses["updated_at"],
                                                                        "updated_by" => $TravelArrayLicenses["updated_by"]
                                                                    );
                                                                    $medicalcenter->setLicenses($arrayLicenses);
                                                                    $dm = $this->get('doctrine_mongodb')->getManager();
                                                                    //$dm->persist($GeneralConfiguration);
                                                                    $dm->flush();
                                                                    return new Response('Operacion exitosa');
                                                                }
                                                            } else {
                                                                $arrayServices = $TravelArrayLicenses["services"];
                                                                $arrayServices[] = array(
                                                                    "_id" => $serviceIdPost,
                                                                    "name" => $servicePost,
                                                                    "category" => $categoryPost,
                                                                    "format" => $formatPost,
                                                                    "fields" => $fieldsPost,
                                                                    "amount" => $amountPost,
                                                                    "active" => true,
                                                                    "created_at" => $fechaNow,
                                                                    "created_by" => $user_id,
                                                                    "updated_at" => $fechaNow,
                                                                    "updated_by" => $user_id,
                                                                );

                                                                $arrayLicenses[$key] = array(
                                                                    "license_id" => $TravelArrayLicenses["license_id"],
                                                                    "expiration_date" => $TravelArrayLicenses["expiration_date"],
                                                                    "status" => $TravelArrayLicenses["status"],
                                                                    "modules" => $TravelArrayLicenses["modules"],
                                                                    "services" => $arrayServices,
                                                                    "created_at" => $TravelArrayLicenses["created_at"],
                                                                    "created_by" => $TravelArrayLicenses["created_by"],
                                                                    "updated_at" => $TravelArrayLicenses["updated_at"],
                                                                    "updated_by" => $TravelArrayLicenses["updated_by"]
                                                                );

                                                                $medicalcenter->setLicenses($arrayLicenses);
                                                                $dm = $this->get('doctrine_mongodb')->getManager();
                                                                //$dm->persist($GeneralConfiguration);
                                                                $dm->flush();
                                                                return new Response('Operacion exitosa');
                                                            }
                                                        }
                                                    } else {

                                                        $arrayServices[] = array(
                                                            "_id" => $serviceIdPost,
                                                            "name" => $servicePost,
                                                            "category" => $categoryPost,
                                                            "format" => $formatPost,
                                                            "fields" => $fieldsPost,
                                                            "amount" => $amountPost,
                                                            "active" => true,
                                                            "created_at" => $fechaNow,
                                                            "created_by" => $user_id,
                                                            "updated_at" => $fechaNow,
                                                            "updated_by" => $user_id,
                                                        );

                                                        $arrayLicenses[$key] = array(
                                                            "license_id" => $TravelArrayLicenses["license_id"],
                                                            "expiration_date" => $TravelArrayLicenses["expiration_date"],
                                                            "status" => $TravelArrayLicenses["status"],
                                                            "modules" => $TravelArrayLicenses["modules"],
                                                            "services" => $arrayServices,
                                                            "created_at" => $TravelArrayLicenses["created_at"],
                                                            "created_by" => $TravelArrayLicenses["created_by"],
                                                            "updated_at" => $TravelArrayLicenses["updated_at"],
                                                            "updated_by" => $TravelArrayLicenses["updated_by"]
                                                        );

                                                        $medicalcenter->setLicenses($arrayLicenses);
                                                        $dm = $this->get('doctrine_mongodb')->getManager();
                                                        //$dm->persist($GeneralConfiguration);
                                                        $dm->flush();
                                                        return new Response('Operacion exitosa');
                                                    }
                                                }
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
