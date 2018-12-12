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

class ApiRestUserController extends Controller {
//  validation_code medical center master: 0 =  inicio, 1 = codigo enviado, 2 = codigo vencido
//  validation_code reset_password: 0 =  inicio, 1 = codigo enviado, 2 = codigo vencido
//  validation_code unlock_user: 0 =  inicio, 1 = codigo enviado, 2 = codigo vencido
//  enabled user_front: 0 =  inactivo, 1 = activo, 2 = bloqueado

    /**
     * @Route("/api/CheckMaster/{id}")     
     * @Method("GET")
     */
    public function wamaIdAction($id) {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $provinces = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->find($id);

        $jsonContent = $serializer->serialize($provinces, 'json');

        return new Response($jsonContent);
    }

    /**
     * @Route("/api/prueba/")     
     * @Method("GET")
     */
    public function wamaAction() {

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

        $provinces = $this->get('doctrine_mongodb')->getRepository('AppBundle:Country')->findAll();

        $jsonContent = $serializer->serialize($provinces, 'json');

        return new Response($jsonContent);
    }

    /**
     * @Route("/api/CheckMaster")     
     * @Method("POST")
     */
    public function saveAction(Request $request) {

        $data = $request->request->get("country");
        $email = $request->request->get("email");
        var_dump($data);
        var_dump($email);
        $country = new Country();
        $country->setName($data);
        $dm = $this->get('doctrine_mongodb')->getManager();
//        $dm->persist($country);
//        $dm->flush();

        return new Response('BELLO');
    }

    /**
     * @Route("/api/RegisterUserMaster")     
     * @Method("POST")
     */
    public function registerUserMasterAction(Request $request) {

        $fechaNow = new \MongoDate();
        $email = $request->request->get("email");
        $username = $request->request->get("username");
        $password = $request->request->get("password");
        $salt = '$bgr$/';
        $passwordEnc = sha1(md5($salt . $password));
        $secret_question1 = $request->request->get("secret_question1");
        $secret_question2 = $request->request->get("secret_question2");
        $secret_question3 = $request->request->get("secret_question3");
        $secret_answer1 = $request->request->get("secret_answer1");
        $secret_answer2 = $request->request->get("secret_answer2");
        $secret_answer3 = $request->request->get("secret_answer3");

        $check_mail = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('master.email' => $email));
        $check_user_front = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $email));

        if (($email == "") || ($password == "") || ($secret_question1 == "") || ($secret_question2 == "") || ($secret_question3 == "") || ($secret_answer1 == "") || ($secret_answer2 == "") || ($secret_answer3 == "")) {

            return new Response('Campos vacios');
        } else if (!$check_mail) {

            return new Response('¡Email no encontrado!');
        } else if ($check_user_front) {

            return new Response('¡Este email ya esta registrado!');
        } else {

            $medical_center_id = $check_mail[0]->getId();
            $name = $check_mail[0]->getName();

            //CREACION DE LOS ARRAYS
            $arrayPermission[] = array(
                '_id' => 'MASTER',
                'name' => 'MASTER',
                'type' => 0
            );

            $arrayBranchOffice[] = array(
                '_id' => 0,
                'name' => 'Sucursal',
                'is_default' => 0,
                'permission' => $arrayPermission
            );

            $arrayMedicalCenter[] = array(
                "_id" => $medical_center_id,
                "name" => $name,
                "is_default" => 1,
                "branch_office" => $arrayBranchOffice,
                "active" => true,
                "created_at" => $fechaNow,
                "created_by" => "0",
                "updated_at" => $fechaNow,
                "updated_by" => "0");

            $arrayProfile[] = array(
                'name' => 'internal',
                'medical_center' => $arrayMedicalCenter
            );

            $userFront = new UsersFront();
            $userFront->setEmail($email);
            $userFront->setEnabled(1);
            $userFront->setPassword($passwordEnc);
            $userFront->setProfileIsDefault("internal");
            $userFront->setProfile($arrayProfile);
            $userFront->setSecretQuestion1($secret_question1);
            $userFront->setSecretQuestion2($secret_question2);
            $userFront->setSecretQuestion3($secret_question3);
            $userFront->setSecretAnswer1($secret_answer1);
            $userFront->setSecretAnswer2($secret_answer2);
            $userFront->setSecretAnswer3($secret_answer3);
            $userFront->setCreatedAt($fechaNow);
            $userFront->setCreatedBy("0");
            $userFront->setUpdatedAt($fechaNow);
            $userFront->setUpdatedBy("0");
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->persist($userFront);
            $dm->flush();

            return new Response('Operacion exitosa');
        }
    }

    /**
     * @Route("/api/CheckMaster")     
     * @Method("PUT")
     */
    public function checkMailMasterAction(Request $request) {

        $date = new \MongoDate();
        $email = $request->request->get("email");

        $user = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findOneBy(['email' => $email]);

        if ($user) {
            return new Response('¡Este email ya esta registrado!');
        } else {

            $check_mail = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('master.email' => $email));

            if (!$check_mail) {

                return new Response('¡Email no encontrado!');
            } else {

                $id = $check_mail[0]->getId();

                $medical_center_master = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);
                $arrayMaster = $medical_center_master->getMaster();
                $name_medical_center = $medical_center_master->getName();
                $arrayEmailMaster = $arrayMaster[0]["email"];
                $arrayCodeValidationMaster = $arrayMaster[0]["validation_code"];
                $arrayStatusMaster = $arrayMaster[0]["status"];

                if (($arrayCodeValidationMaster != "") && ($arrayStatusMaster == "1")) {

                    return new Response('¡Ya le fue enviado un codigo de validacion, por favor revise su correo e ingrese el codigo!');
                } else if (($arrayCodeValidationMaster != "") && ($arrayStatusMaster == "2")) {

                    $key = '';
                    $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
                    $max = strlen($pattern) - 1;
                    for ($i = 0; $i < 6; $i++)
                        $key .= $pattern{mt_rand(0, $max)};

                    $cod_enc = sha1($key);

                    $arrayMaster[0]["validation_code"] = $cod_enc;
                    $arrayMaster[0]["status"] = "1";
                    $arrayMaster[0]["created_at"] = $date;

                    $medical_center_master->setMaster($arrayMaster);
                    $dm = $this->get('doctrine_mongodb')->getManager();
                    $dm->persist($medical_center_master);
                    $dm->flush();


                    $mailer = $this->container->get('mailer');

                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                            ->setUsername('smartclinicsoft@gmail.com')
                            ->setPassword('smartclinic1');

                    $mailer = \Swift_Mailer::newInstance($transport);
                    $message = \Swift_Message::newInstance('Test')
                            ->setSubject('Codigo de Validacion')
                            ->setFrom('smartclinicsoft@gmail.com')
                            ->setTo($email)
                            ->setBody('Estimado(a) cliente: ' . $name_medical_center . ', su codigo de validacion para el registro en Smart Clinic es: ' . $key . '');
                    $this->get('mailer')->send($message);

                    return new Response('Operacion exitosa');
                } else if (($arrayCodeValidationMaster == "") && ($arrayStatusMaster == "0")) {

                    $key = '';
                    $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
                    $max = strlen($pattern) - 1;
                    for ($i = 0; $i < 6; $i++)
                        $key .= $pattern{mt_rand(0, $max)};

                    $cod_enc = sha1($key);

                    $arrayMaster[0]["validation_code"] = $cod_enc;
                    $arrayMaster[0]["status"] = "1";
                    $arrayMaster[0]["created_at"] = $date;

                    $medical_center_master->setMaster($arrayMaster);
                    $dm = $this->get('doctrine_mongodb')->getManager();
                    $dm->persist($medical_center_master);
                    $dm->flush();


                    $mailer = $this->container->get('mailer');

                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                            ->setUsername('smartclinicsoft@gmail.com')
                            ->setPassword('smartclinic1');

                    $mailer = \Swift_Mailer::newInstance($transport);
                    $message = \Swift_Message::newInstance('Test')
                            ->setSubject('Codigo de Validacion')
                            ->setFrom('smartclinicsoft@gmail.com')
                            ->setTo($email)
                            ->setBody('Estimado(a) cliente: ' . $name_medical_center . ', su codigo de validacion para el registro en Smart Clinic es: ' . $key . '');
                    $this->get('mailer')->send($message);

                    return new Response('Operacion exitosa');
                }
            }
        }
    }

    /**
     * @Route("/api/CheckCodeValidation")     
     * @Method("PUT")
     */
    public function CheckCodeValidationAction(Request $request) {

        $date = new \MongoDate();
        $email = $request->request->get("email");
        $validation_code = $request->request->get("validation_code");
        $enc_valitation_code = sha1($validation_code);

        $check_validation_code = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->findBy(array('master.email' => $email, 'master.validation_code' => $enc_valitation_code));
        if (!$check_validation_code) {

            return new Response('Codigo invalido');
        } else {

            $id = $check_validation_code[0]->getId();
            $medical_center_master = $this->get('doctrine_mongodb')->getRepository('AppBundle:MedicalCenter')->find($id);

            $arrayMaster = $medical_center_master->getMaster();
            $arrayEmailMaster = $arrayMaster[0]["email"];
            $arrayCodeValidationMaster = $arrayMaster[0]["validation_code"];
            $arrayStatusMaster = $arrayMaster[0]["status"];

            $arrayDateMaster = date('Y-m-d H:i:s', $arrayMaster[0]["created_at"]->sec);
            $fechaActual = date('Y-m-d h:i:s');

            $fecha1 = new DateTime($arrayDateMaster);
            $fecha2 = new DateTime($fechaActual);
            $fecha = $fecha1->diff($fecha2);
            $horas = $fecha->h;
            $dias = $fecha->d;

            if ($dias >= 1) {

                $arrayMaster[0]["status"] = "2";

                $medical_center_master->setMaster($arrayMaster);
                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->persist($medical_center_master);
                $dm->flush();
                return new Response('¡Codigo vencido, debe solicitar un nuevo codigo de validacion!');
            } else {

                return new Response('Email y codigo valido');
            }
        }
    }

    /**
     * @Route("/api/login")     
     * @Method("POST")
     */
    public function apiLoginAction(Request $request) {

        $email = $request->request->get("_username");
        $password = $request->request->get("_password");

        $salt = '$bgr$/';
        $passwordEnc = sha1(md5($salt . $password));

        $user = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findOneBy(['email' => $email]);
//        var_dump($user);

        if (!$user) {
            return new JsonResponse('Usuario invalido', 403);
        }

        $userPassword = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findOneBy(['email' => $email, 'password' => $passwordEnc]);
//        var_dump($userPassword);
        if (!$userPassword) {
            return new JsonResponse('Password incorrecta', 403);
        }
//        return new Response('HOLA');
        $token = $this->get('lexik_jwt_authentication.encoder')
                ->encode([
            'username' => $user->getEmail(), 'id' => $user->getId(),
            'profile_is_default' => $user->getProfileIsDefault(),
            'profile' => $user->getProfile()
        ]);

        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/api/requestCodeRecoverPassword")     
     * @Method("PUT")
     */
    public function requestCodeRecoverPasswordAction(Request $request) {

        $date = new \MongoDate();
        $email = $request->request->get("email");

        $user = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $email));
        if (!$user) {

            return new Response('¡Este email no esta registrado!');
        } else {

            $id = $user[0]->getId();
            $user_front = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($id);

            $arrayResetPassword = $user_front->getResetPassword();
            $arrayCode = $arrayResetPassword[0]["validation_code"];
            $arrayStatus = $arrayResetPassword[0]["status"];

//            var_dump($arrayResetPassword);
            if ($arrayResetPassword == null) {

                $key = '';
                $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
                $max = strlen($pattern) - 1;
                for ($i = 0; $i < 6; $i++)
                    $key .= $pattern{mt_rand(0, $max)};


                $cod_enc = sha1($key);

                $arrayResetPassword[0]["validation_code"] = $cod_enc;
                $arrayResetPassword[0]["status"] = "1";
                $arrayResetPassword[0]["created_at"] = $date;

                $user_front->setResetPassword($arrayResetPassword);
                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->persist($user_front);
                $dm->flush();

                $mailer = $this->container->get('mailer');

                $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                        ->setUsername('smartclinicsoft@gmail.com')
                        ->setPassword('smartclinic1');

                $mailer = \Swift_Mailer::newInstance($transport);
                $message = \Swift_Message::newInstance('Test')
                        ->setSubject('Restablecer Password Smart Clinic')
                        ->setFrom('smartclinicsoft@gmail.com')
                        ->setTo($email)
                        ->setBody('Su codigo de validacion para restablecer su password en Smart Clinic es: ' . $key . '');
                $this->get('mailer')->send($message);
            } else if (($arrayCode != "") && ($arrayStatus == "1")) {

                return new Response('¡Ya le fue enviado un codigo de validacion para restablecer su password, por favor revise su correo!');
            } else if (($arrayCode != "") && ($arrayStatus == "2")) {

                $key = '';
                $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
                $max = strlen($pattern) - 1;
                for ($i = 0; $i < 6; $i++)
                    $key .= $pattern{mt_rand(0, $max)};


                $cod_enc = sha1($key);

                $arrayResetPassword[0]["validation_code"] = $cod_enc;
                $arrayResetPassword[0]["status"] = "1";
                $arrayResetPassword[0]["created_at"] = $date;

                $user_front->setResetPassword($arrayResetPassword);
                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->persist($user_front);
                $dm->flush();

                $mailer = $this->container->get('mailer');

                $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                        ->setUsername('smartclinicsoft@gmail.com')
                        ->setPassword('smartclinic1');

                $mailer = \Swift_Mailer::newInstance($transport);
                $message = \Swift_Message::newInstance('Test')
                        ->setSubject('Restablecer Password Smart Clinic')
                        ->setFrom('smartclinicsoft@gmail.com')
                        ->setTo($email)
                        ->setBody('Su codigo de validacion para restablecer su password en Smart Clinic es: ' . $key . '');
                $this->get('mailer')->send($message);
            }

            return new Response('Operacion exitosa');
        }
    }

    /**
     * @Route("/api/CheckCodeValidationResetPassword")     
     * @Method("PUT")
     */
    public function CheckCodeValidationResetPasswordAction(Request $request) {

        $date = new \MongoDate();
        $email = $request->request->get("email");
        $validation_code = $request->request->get("validation_code");
        $enc_valitation_code = sha1($validation_code);

        $check_validation_code = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $email, 'reset_password.validation_code' => $enc_valitation_code));
        if (!$check_validation_code) {

            return new Response('Codigo invalido');
        } else {

            $id = $check_validation_code[0]->getId();
            $user_front_master = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($id);

            $arrayResetPassword = $user_front_master->getResetPassword();

            $arrayDate = date('Y-m-d H:i:s', $arrayResetPassword[0]["created_at"]->sec);
            $fechaActual = date('Y-m-d h:i:s');

            $fecha1 = new DateTime($arrayDate);
            $fecha2 = new DateTime($fechaActual);
            $fecha = $fecha1->diff($fecha2);
            $horas = $fecha->h;
            $dias = $fecha->d;

            if ($dias >= 1) {

                $arrayResetPassword[0]["status"] = "2";

                $user_front_master->setResetPassword($arrayResetPassword);
                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->persist($user_front_master);
                $dm->flush();
                return new Response('¡Codigo vencido, debe solicitar un nuevo codigo de validacion  para restablecer su password!');
            } else {

                return new Response('Email y codigo valido');
            }
        }
    }

    /**
     * @Route("/api/ResetPassword")     
     * @Method("POST")
     */
    public function ResetPasswordAction(Request $request) {

        $fechaNow = new \MongoDate();
        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $salt = '$bgr$/';
        $passwordEnc = sha1(md5($salt . $password));


        $check_user_front = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $email));

        if ($email == "") {

            return new Response('Ingrese el email');
        } else if ($password == "") {

            return new Response('Ingrese la contraseña');
        } else if (!$check_user_front) {

            return new Response('¡Email no encontrado!');
        } else {

            $user_front_id = $check_user_front[0]->getId();
            $user_front_data = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($user_front_id);

            $user_front_data->setPassword($passwordEnc);
            $user_front_data->setUpdatedAt($fechaNow);
            $user_front_data->setUpdatedBy($user_front_id);
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->flush();

            return new Response('Operacion exitosa');
        }
    }

    /**
     * @Route("/api/LoadSelects")     
     * @Method("GET")
     */
    public function LoadSelectsAction(Request $request) {

        $fechaNow = new \MongoDate();
//        $option = $request->request->get("option");

        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());

        $serializer = new Serializer($normalizers, $encoders);

//        if ($option == "questions") {

        $GeneralConfiguration = $this->get('doctrine_mongodb')->getRepository('AppBundle:GeneralConfiguration')->find("5ae08f86c5dfa106dc92610a");
        $ArraySecretQuestions = $GeneralConfiguration->getSecretQuestions();

        foreach ($ArraySecretQuestions as $travelArraySecretQuestions) {
            $arrayQuestions[] = array(
                "value" => $travelArraySecretQuestions,
                "label" => $travelArraySecretQuestions,
            );
        }


        $jsonContent = $serializer->serialize($arrayQuestions, 'json');

        return new Response($jsonContent);
//        }
    }

    /**
     * @Route("/api/BlockUser")     
     * @Method("PUT")
     */
    public function BlockUserAction(Request $request) {

        $fechaNow = new \MongoDate();
        $email = $request->request->get("email");

        $check_user_front = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $email));

        if (!$check_user_front) {

            return new Response('¡Email no encontrado!');
        } else {

            $user_front_id = $check_user_front[0]->getId();
            $user_front_data = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($user_front_id);

            $user_front_data->setEnabled(2);
            $user_front_data->setUpdatedAt($fechaNow);
            $user_front_data->setUpdatedBy($user_front_id);
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->flush();

            $mailer = $this->container->get('mailer');

            $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                    ->setUsername('smartclinicsoft@gmail.com')
                    ->setPassword('smartclinic1');

            $mailer = \Swift_Mailer::newInstance($transport);
            $message = \Swift_Message::newInstance('Test')
                    ->setSubject('Usuario bloqueado Smart Clinic')
                    ->setFrom('smartclinicsoft@gmail.com')
                    ->setTo($email)
                    ->setBody('Su usuario ha sido temporalmente bloqueado, para desbloquear ingrese en la opcion ¡Desbloqueo de Usuario!');
            $this->get('mailer')->send($message);

            return new Response('Operacion exitosa');
        }
    }

    /**
     * @Route("/api/RequestCodeUnlockUser")     
     * @Method("PUT")
     */
    public function RequestCodeUnlockUserAction(Request $request) {

        $date = new \MongoDate();
        $email = $request->request->get("email");
        $secret_question1 = $request->request->get("secret_question1");
        $secret_question2 = $request->request->get("secret_question2");
        $secret_question3 = $request->request->get("secret_question3");
        $secret_answer1 = $request->request->get("secret_answer1");
        $secret_answer2 = $request->request->get("secret_answer2");
        $secret_answer3 = $request->request->get("secret_answer3");

        $check_user_front = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $email));

        if (!$check_user_front) {

            return new Response('¡Email no encontrado!');
        } else {

            $user_front_id = $check_user_front[0]->getId();
            $secret_question1_data = $check_user_front[0]->getSecretQuestion1();
            $secret_question2_data = $check_user_front[0]->getSecretQuestion2();
            $secret_question3_data = $check_user_front[0]->getSecretQuestion3();
            $secret_answer1_data = $check_user_front[0]->getSecretAnswer1();
            $secret_answer2_data = $check_user_front[0]->getSecretAnswer2();
            $secret_answer3_data = $check_user_front[0]->getSecretAnswer3();

            if (($secret_question1 == $secret_question1_data) && ($secret_question2 == $secret_question2_data) && ($secret_question3 == $secret_question3_data) && ($secret_answer1 == $secret_answer1_data) && ($secret_answer2 == $secret_answer2_data) && ($secret_answer3 == $secret_answer3_data)) {

                $user_front = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($user_front_id);

                $arrayUnlockUser = $user_front->getUnlockUser();
                $arrayCode = $arrayUnlockUser[0]["validation_code"];
                $arrayStatus = $arrayUnlockUser[0]["status"];

                if ($arrayUnlockUser == null) {

                    $key = '';
                    $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
                    $max = strlen($pattern) - 1;
                    for ($i = 0; $i < 6; $i++)
                        $key .= $pattern{mt_rand(0, $max)};

                    $cod_enc = sha1($key);

                    $arrayUnlockUser[0]["validation_code"] = $cod_enc;
                    $arrayUnlockUser[0]["status"] = "1";
                    $arrayUnlockUser[0]["created_at"] = $date;

                    $user_front->setUnlockUser($arrayUnlockUser);
                    $dm = $this->get('doctrine_mongodb')->getManager();
                    $dm->persist($user_front);
                    $dm->flush();

                    $mailer = $this->container->get('mailer');

                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                            ->setUsername('smartclinicsoft@gmail.com')
                            ->setPassword('smartclinic1');

                    $mailer = \Swift_Mailer::newInstance($transport);
                    $message = \Swift_Message::newInstance('Test')
                            ->setSubject('Codigo para desbloqueo de usuario Smart Clinic')
                            ->setFrom('smartclinicsoft@gmail.com')
                            ->setTo($email)
                            ->setBody('Su codigo de validacion para desbloquear su usuario en smart clinic es: ' . $key . '');
                    $this->get('mailer')->send($message);

                    return new Response('Operacion exitosa');
                } else if (($arrayCode != "") && ($arrayStatus == "1")) {

                    return new Response('¡Ya le fue enviado un codigo de validacion para el desbloqueo de su usuario, por favor revise su correo!');
                } else if (($arrayCode != "") && ($arrayStatus == "2")) {
                    $key = '';
                    $pattern = '1234567890abcdefghijklmnopqrstuvwxyz';
                    $max = strlen($pattern) - 1;
                    for ($i = 0; $i < 6; $i++)
                        $key .= $pattern{mt_rand(0, $max)};

                    $cod_enc = sha1($key);

                    $arrayUnlockUser[0]["validation_code"] = $cod_enc;
                    $arrayUnlockUser[0]["status"] = "1";
                    $arrayUnlockUser[0]["created_at"] = $date;

                    $user_front->setUnlockUser($arrayUnlockUser);
                    $dm = $this->get('doctrine_mongodb')->getManager();
                    $dm->persist($user_front);
                    $dm->flush();

                    $mailer = $this->container->get('mailer');

                    $transport = \Swift_SmtpTransport::newInstance('smtp.gmail.com', 587)
                            ->setUsername('smartclinicsoft@gmail.com')
                            ->setPassword('smartclinic1');

                    $mailer = \Swift_Mailer::newInstance($transport);
                    $message = \Swift_Message::newInstance('Test')
                            ->setSubject('Codigo para desbloqueo de usuario Smart Clinic')
                            ->setFrom('smartclinicsoft@gmail.com')
                            ->setTo($email)
                            ->setBody('Su codigo de validacion para desbloquear su usuario en smart clinic es: ' . $key . '');
                    $this->get('mailer')->send($message);

                    return new Response('Operacion exitosa');
                }
            } else {

                return new Response('¡Datos incorrectos!');
            }
        }
    }

    /**
     * @Route("/api/CheckCodeValidationUnlockUser")     
     * @Method("PUT")
     */
    public function CheckCodeValidationUnlockUserAction(Request $request) {

        $date = new \MongoDate();
        $email = $request->request->get("email");
        $validation_code = $request->request->get("validation_code");
        $enc_valitation_code = sha1($validation_code);

        $check_validation_code = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $email, 'unlock_user.validation_code' => $enc_valitation_code));
        if (!$check_validation_code) {

            return new Response('Codigo invalido');
        } else {

            $id = $check_validation_code[0]->getId();
            $user_front_master = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($id);

            $arrayUnlockUser = $user_front_master->getUnlockUser();

            $arrayDate = date('Y-m-d H:i:s', $arrayUnlockUser[0]["created_at"]->sec);
            $fechaActual = date('Y-m-d h:i:s');

            $fecha1 = new DateTime($arrayDate);
            $fecha2 = new DateTime($fechaActual);
            $fecha = $fecha1->diff($fecha2);
            $horas = $fecha->h;
            $dias = $fecha->d;

            if ($dias >= 1) {

                $arrayUnlockUser[0]["status"] = "2";

                $user_front_master->setUnlockUser($arrayUnlockUser);
                $dm = $this->get('doctrine_mongodb')->getManager();
                $dm->persist($user_front_master);
                $dm->flush();
                return new Response('¡Codigo vencido, debe solicitar un nuevo codigo de validacion  para restablecer su password!');
            } else {

                return new Response('Email y codigo valido');
            }
        }
    }

    /**
     * @Route("/api/UnlockUser")     
     * @Method("PUT")
     */
    public function UnlockUserAction(Request $request) {

        $fechaNow = new \MongoDate();
        $email = $request->request->get("email");
        $password = $request->request->get("password");
        $salt = '$bgr$/';
        $passwordEnc = sha1(md5($salt . $password));


        $check_user_front = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->findBy(array('email' => $email));

        if (($email == "") || ($password == "")) {

            return new Response('Campos vacios');
        } else if (!$check_user_front) {

            return new Response('¡Email no encontrado!');
        } else {

            $user_front_id = $check_user_front[0]->getId();
            $user_front_data = $this->get('doctrine_mongodb')->getRepository('AppBundle:UsersFront')->find($user_front_id);

            $user_front_data->setEnabled(1);
            $user_front_data->setPassword($passwordEnc);
            $user_front_data->setUpdatedAt($fechaNow);
            $user_front_data->setUpdatedBy($user_front_id);
            $dm = $this->get('doctrine_mongodb')->getManager();
            $dm->flush();

            return new Response('Operacion exitosa');
        }
    }

}
