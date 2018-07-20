<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller\ApiRest;

use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Description of ApiRestLogin
 *
 * @author tecnocam
 */
class ApiRestLogin {

    /**
     * @Route("/login/{email}/{pass}", name="login")
     */
    public function loginAction($email, $pass) {
        $user = $this->getDoctrine()->getRepository('AppBundle:User')
                ->findOneBy(['email' => $email]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        // Check Password
        if (!$this->get('security.password_encoder')->isPasswordValid($user, $pass)) {
            throw $this->createAccessDeniedException();
        }

        // Create JWT token
        $token = $this->get('lexik_jwt_authentication.encoder')
                ->encode(['username' => $user->getUsername()]);

        // Return tocken
        return new JsonResponse(['token' => $token]);
    }

}
