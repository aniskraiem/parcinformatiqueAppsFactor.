<?php

// src/Controller/SecurityController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Sejour;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Co;

class SecurityController extends AbstractController
{

    public function __construct(\Doctrine\ORM\EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/Login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/auth.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/simulation", name="simulation")
     */
    public function simulation(UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $user->setEmail("anis.anis@gmail.com");
        $user->setNom("ahmed");
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user, "appsfactor1"

            )
        );
        $user->setPrenom("ahmed");
        $user->setRoles("agent");
        $user->setUsername("ahmedd");
        $this->em->persist($user);
        $this->em->flush();
        return new \http\Env\Response("done");

    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');


    }


    /**
     * @Route("/Parent/logout", name="app_logout_parent")
     */
    public function logoutParent()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');


    }


    /**
     * @Route("/Accompagnateur/logout", name="app_logout_Accompagnateur")
     */
    public function logoutAcompa()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');


    }

    /**
     * @Route("/Partenaire/logout", name="app_logout_Partenaire")
     */
    public function logoutPartenaire()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');


    }

    /**
     * @Route("/Accompagnateur/login", name="app_back_Acommpa")
     */
    public function Acommpalogin(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->redirectToRoute('layoutAccueil');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('Accompagnateur/LoginAccompagnateur.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/ForgotPass",name="forgotPass")
     */
    function forgot_Password()
    {
        return $this->render('security/DemandePassword.html.twig');
    }

    /**
     * @Route("/forgotPassparent",name="forgotPassparent")
     */
    function forgot_Password2()
    {
        return $this->render('security/DemandePasswordParent.html.twig');
    }

    /**
     * @Route("/Accompagnateur/NewPassword",name="New_Password")
     */
    function Create_New_Password(Request $request)
    {
        $password = $request->get('password');
        $userId = $request->get('userID');
        $USerService = $this->get("App\Service\UserService");
        $user = $this->getDoctrine()
            ->getRepository(User::class)->find($userId);

        $USerService->updatPassw($user, $password);
        return new response("done");
    }

    /**
     * @Route("/Accompagnateur/request_password",name="request_password")
     */
    function request_password(Request $request)
    {
        $code = $request->get('code');
        $sejour = $this->em->getRepository(Sejour::class)->findOneBy(['codeSejour' => $code]);

        $user = null;
        if ($sejour != null) {

            $user = $sejour->getIdAcommp();
        }
        if ($user == null) {
            return $this->render('security/UsernotFound.html.twig');
        } else {
            $encript = hash("sha256", $user->getUsername() . $user->getId());
            $url_newPass = $this->get('router')->generate('directloginOM_token', array("token" => str_replace(".", " ", $user->getEmail()), 'userHash' => $encript), UrlGeneratorInterface::ABSOLUTE_URL);
            $USerService = $this->get("App\Service\UserService");
            $USerService->sendPasswordMail($user->getEmail(), $url_newPass);
            // dd($USerService->sendPasswordMail($user->getEmail(),$url_newPass));
            //dd($user->getEmail().' '.$url_newPass);
            return $this->render('security/DemandePasswordValide.html.twig');
        }
    }

    /**
     * @Route("/Parent/request_password_Parent",name="request_passwordParent")
     */
    function request_password_parent(Request $request)
    {
        $mail = $request->get('code');

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $mail]);


        if ($user == null) {
            return $this->render('security/UsernotFound.html.twig');
        } else {
            $encript = hash("sha256", $user->getUsername() . $user->getId());
            $url_newPass = $this->get('router')->generate('directloginOM_tokenv2', array("token" => str_replace(".", " ", $user->getEmail()), 'userHash' => $encript), UrlGeneratorInterface::ABSOLUTE_URL);
            $USerService = $this->get("App\Service\UserService");
            $USerService->sendPasswordMail($user->getEmail(), $url_newPass);
            // dd($USerService->sendPasswordMail($user->getEmail(),$url_newPass));
            //dd($user->getEmail().' '.$url_newPass);
            return $this->render('security/DemandePasswordValide.html.twig');
        }
    }


    /**
     * @Route("/Partenaire/request_password_Partenaire",name="request_password_parentenaire")
     */
    function request_password_parentenaire(Request $request)
    {


        ini_set("max_execution_time", -1);
        ini_set('memory_limit', '-1');


        $mail = $request->get('code');

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $mail]);


        if ($user == null) {
            return $this->render('security/UsernotFound.html.twig');
        } else {
            $encript = hash("sha256", $user->getUsername() . $user->getId());
            $url_newPass = $this->get('router')->generate('directloginOM_tokenv3', array("token" => str_replace(".", " ", $user->getEmail()), 'userHash' => $encript), UrlGeneratorInterface::ABSOLUTE_URL);
            $USerService = $this->get("App\Service\UserService");
            $USerService->sendPasswordMail($user->getEmail(), $url_newPass);
            // dd($USerService->sendPasswordMail($user->getEmail(),$url_newPass));
            //dd($user->getEmail().' '.$url_newPass);
            return $this->render('security/DemandePasswordValide.html.twig');
        }
    }


    /**
     * @Route("/directloginOM_tokenv2/{token}/{userHash}",name="directloginOM_tokenv2")
     */
    function directloginOM_tokenv2($token, $userHash)
    {
        $token = str_replace(" ", ".", $token);
        $user = $this->getDoctrine()
            ->getRepository(User::class)->findOneBy(array('email' => $token));
        if ((hash("sha256", $user->getUsername() . $user->getId()) == $userHash)) {
            return $this->render('security/DemandePasswordParentv.html.twig', ["userToSetPassword" => $user]);
        } else {
            return $this->redirectToRoute("app_back_Parent");
        }
    }


    /**
     * @Route("/directloginOM_token/{token}/{userHash}",name="directloginOM_token")
     */
    function directloginOM($token, $userHash)
    {
        $token = str_replace(" ", ".", $token);
        $user = $this->getDoctrine()
            ->getRepository(User::class)->findOneBy(array('email' => $token));
        if ((hash("sha256", $user->getUsername() . $user->getId()) == $userHash)) {
            return $this->render('security/NewPassword.html.twig', ["userToSetPassword" => $user]);
        } else {
            return $this->redirectToRoute("app_back_Acommpa");
        }
    }

    /**
     * @Route("/Parent/login", name="app_back_Parent")
     */
    public function Parentlogin(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->redirectToRoute('layoutAccueil');
        }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //name of twing of loging
        return $this->render('Parent/LoginParent.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }



    /**
     * @Route("/Parent/AccueilParent", name="AccueilParent")
     */
    // public function Accueiparents() {
    //      ;
//
    //    return $this->render('Parent/LoginParent.html.twig');
    // }

    /**
     * @Route("/Partenaire/login", name="app_login_back_Partenaire")
     */
    public function loginpartenair(AuthenticationUtils $authenticationUtils): Response
    {


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('partenaire/authentification.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }


    /**
     * @Route("/directloginOM_tokenv3/{token}/{userHash}",name="directloginOM_tokenv3")
     */
    function directloginOM_tokenv3($token, $userHash)
    {
        $token = str_replace(" ", ".", $token);
        $user = $this->getDoctrine()
            ->getRepository(User::class)->findOneBy(array('email' => $token));
        //               dd($user);
        if ((hash("sha256", $user->getUsername() . $user->getId()) == $userHash)) {
            return $this->render('security/DemandePasswordvpartenaire.html.twig', ["userToSetPassword" => $user]);
        } else {
            return $this->redirectToRoute("app_login_back_Partenaire");
        }
    }


    /**
     * @Route("/forgotPasspatenaire",name="patenaireforget")
     */
    function forgot_Password3()
    {
        return $this->render('security/DemandePasswordPartenaire.html.twig');
    }


}