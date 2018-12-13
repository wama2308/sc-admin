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
                                    $ArrayLicenses = $medicalcenter->getLicenses();

                                    foreach ($ArrayLicenses as $TravelArrayLicenses) {

                                        if ($TravelArrayLicenses["license_id"] == $licenseIdPost) {

                                            $arrayDate = date('Y-m-d H:i:s', $TravelArrayLicenses["expiration_date"]->sec);
                                            $fechaActual = date('Y-m-d h:i:s');

                                            if ($arrayDate >= $fechaActual) {

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

}
