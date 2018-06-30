<?php

namespace AppBundle\Controller;

use AppBundle\Document\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
//use Symfony\Component\Form\Extension\Core\Type\TextareaType;
//use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
//use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\AbstractType;


class UserController extends Controller {

    /**
     * @Route("/users/index", name="users_list")
     * @Method("GET")
     */
    public function listAction(Request $request) {

        $users = $this->get('doctrine_mongodb')->getRepository('AppBundle:User')->findAll();
        //var_dump($countries);
        return $this->render('@App/user/index.html.twig', array('users' => $users));
    }

    /**
     * @Route("/users/edit-profile/{id}", name="profile_edit")
     * @Method({"GET", "POST"})
     */
    public function editProfileAction($id, Request $request) {
        $user = $this->get('doctrine_mongodb')->getRepository('AppBundle:User')->find($id);

        $user->setUsername($user->getUsername());
        $user->setEmail($user->getEmail());
        $user->setPassword($user->getPassword());

        $form = $this->createFormBuilder($user)
                ->add('username', TextType::class, array('label' => 'Username', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('email', EmailType::class, array('label' => 'Email', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('password', PasswordType::class, array('label' => 'Current Password', 'always_empty' => false, 'mapped' => false, 'data' => $user->getPassword(), 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
                ->add('save', SubmitType::class, array('label' => 'Update', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $username = $form['username']->getData();
            $email = $form['email']->getData();
            $password = $form['password']->getData();

            $encoderFactory = $this->get('security.encoder_factory');
            $encoder = $encoderFactory->getEncoder($user);

            $salt = 'salt'; // this should be different for every user
            $password = $encoder->encodePassword($password, $salt);

            $user->setSalt($salt);


            $dm = $this->get('doctrine_mongodb')->getManager();
            $user = $dm->getRepository('AppBundle:User')->find($id);

            $user->setUsername($username);
            $user->setEmail($email);
            $user->setPassword($password);

            $dm->flush();

            $this->addFlash('notice', 'User Updated');

            return $this->redirectToRoute('users_list');
        }

        return $this->render('@App/user/editProfile.html.twig', array('country' => $user, 'form' => $form->createView()));
    }

    /**
     * @Route("/users/change-password/{id}", name="change_password_edit")
     * @Method({"GET", "POST"})
     */
    public function changePasswordAction($id, Request $request) {
        $user = $this->get('doctrine_mongodb')->getRepository('AppBundle:User')->find($id);
        
        $constraintsOptions = array(
            'message' => 'fos_user.current_password.invalid',
        );

        if (!empty($options['validation_groups'])) {
            $constraintsOptions['groups'] = array(reset($options['validation_groups']));
        }
       

        $user->setPassword($user->getPassword());

        $form = $this->createFormBuilder($user)
                ->add('current_password', PasswordType::class, array(
                    'label' => 'form.current_password',
                    'translation_domain' => 'FOSUserBundle',
                    'mapped' => false,
                    'constraints' => array(
                        new NotBlank(),
                        new UserPassword($constraintsOptions),
                        
                    ),
                    'attr' => array(
                        'autocomplete' => 'current-password',
                    ),
                    'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')
                ))
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'options' => array(
                        'translation_domain' => 'FOSUserBundle',
                        'attr' => array(
                            'autocomplete' => 'new-password',
                        ),
                    ),
                    'first_options' => array('label' => 'form.new_password', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')),
                    'second_options' => array('label' => 'form.new_password_confirmation', 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')),
                    'invalid_message' => 'fos_user.password.mismatch',
                ))
                ->add('save', SubmitType::class, array('label' => 'Change', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form['plainPassword']->getData();
            
            $encoderFactory = $this->get('security.encoder_factory');
            $encoder = $encoderFactory->getEncoder($user);
            
            $salt = 'salt'; // this should be different for every user
            $password = $encoder->encodePassword($password, $salt);
            
            $user->setSalt($salt);
            

            $dm = $this->get('doctrine_mongodb')->getManager();
            $user = $dm->getRepository('AppBundle:User')->find($id);

            $user->setPassword($password);

            $dm->flush();

            $this->addFlash('notice', 'Password Changed');

            return $this->redirectToRoute('users_list');
        }

        return $this->render('@App/user/changePassword.html.twig', array('country' => $user, 'form' => $form->createView()));
    }

    /**
     * @Route("/users/delete-user/{id}", name="delete_user")
     * @Method("GET")
     */
    public function deleteAction($id) {

        $dm = $this->get('doctrine_mongodb')->getManager();
        $user = $dm->getRepository('AppBundle:User')->find($id);

        $user->setEnabled(false);

        $dm->flush();

        $this->addFlash('error', 'User Removed');

        return $this->redirectToRoute('users_list');
    }

}
