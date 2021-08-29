<?php

namespace App\Service;

/**
 * Created by PhpStorm.
 * User: Appsfact-02
 * Date: 14/11/2019
 * Time: 13:13
 */
use App\Entity\Produit;
use App\Entity\Ref;
use App\Entity\User;
use App\Entity\ParentSejour;
use App\Entity\Etablisment;
use App\Entity\Attachment;
use App\Entity\Adress;
use App\Entity\Comptebancaire;
use App\Entity\Sejour;
use App\Entity\Jourdescripdate;
use App\Entity\Documentpartenaire;
use Swift_Image;
use App\Entity\Emailing;
use App\Entity\SejourAttachment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserService {

    public function __construct(EntityManagerInterface $em, \Swift_Mailer $mailer, $router, $passwordEncoder, $templating, SessionInterface $session,ParameterBagInterface $params) {
        $this->em = $em;
        $this->mailer = $mailer;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->templating = $templating;
        $this->session = $session;
        $this->params = $params;
    }

    function creationNewUser($nom, $prenom, $etablisment, $fonction, $adressetablisment, $phone, $email, $password, $role) {

        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setNummobile($phone);
        $user->setFonction($fonction);
        $user->setAdresse($adressetablisment);
        $user->setNometablisment($etablisment);
        $user->setEmail(trim($email));

        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );
        $user->setPasswordNonCripted($password);

        $user->setDateCreation(new \DateTime());
        $user->addRole($role);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function creationNewAcommpa($nom, $prenom, $etablisment, $fonction, $adressetablisment, $phoneacc, $mail, $role, $password, $reponseemail) {

        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setUsername($mail);
        $user->setReponseemail(trim($reponseemail));

        // $user->setUsername($identifiant);
        $user->setFonction($fonction);
        $user->setNummobile($phoneacc);
        $user->setEmail(trim($mail));
        $user->setAdresse($adressetablisment);
        $user->setNometablisment($etablisment);
        //$user->setnometablisment();

        $user->setDateCreation(new \DateTime());
        $user->addRole($role);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );
        $this->em->persist($user);
        $this->em->persist($user);

        $this->em->flush();


        return $user;
    }

    function creationNewEtablisment($par, $nom, $prenom, $etablisment, $fonction, $adressetablisment, $phoneacc, $mail, $role, $password,$prixcnxparent,$prixcnxpartenaire,$reversecnxpart,$reverseventepart) {
        $type = "ECOLES/AUTRES";
        $EtabL = new Etablisment();
        $EtabL->setNometab($etablisment);
        $EtabL->setTypeetablisment($type);

        // $user->setUsername($identifiant);
        $EtabL->setFonctioncontact($fonction);
        $EtabL->setNumerotelp($phoneacc);
        $EtabL->setEmail(trim($mail));
        $EtabL->setAdresseetab($adressetablisment);
        $EtabL->setUser($par); //$user->setnometablisment();
        $EtabL->setPrixcnxparent($prixcnxparent);
        $EtabL->setPrixcnxpartenaire($prixcnxpartenaire);
        $EtabL->setReversecnxpart($reversecnxpart);
        $EtabL->setReverseventepart($reverseventepart);


        $this->em->persist($EtabL);
        $this->em->flush();


        return $EtabL;
    }

    public function updatUSER($id, $nom, $prenom, $adresse, $phone, $password, $statut) {
        $user = $this->em->getRepository(User::class)->find($id);
        $refstatut = $this->em->getRepository(Ref::class)->find($statut);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setAdresse($adresse);
        $user->setNummobile($phone);
        $user->setStatut($refstatut);


        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );

        $this->em->persist($user);
        $this->em->flush();


        return $user;
    }

    public function updatPassw($id, $password) {
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );
        $this->em->persist($user);
        $this->em->flush();


        return $user;
    }

    function GenerateTokken($user) {
        $userHash = hash("sha256", $user->getUsername() . $user->getId());
        return $userHash;
    }

    function generateUrlNewPassword($user) {

        $directUrl = $this->router->generate('directloginOM_token', array('token' => $user->getUsername(), 'userHash' => $this->GenerateTokken($user)), UrlGeneratorInterface::ABSOLUTE_URL);

        return $directUrl;
    }

    function sendPasswordMail($email, $url) {

        $message = (new \Swift_Message('Mot de passe oublié 5sur5sejour'))
            ->setFrom("5sur5sejour@5sur5sejour.com")
            ->setTo($email)
            ->setBcc(["contact@5sur5sejour.com"]);
        $image1 = $message->embed(Swift_Image::fromPath("http://res.cloudinary.com/apss-factory/image/upload/a_exif/v1582274063/qw7csxayektmq1leqjpp.png"));
        $image2 = $message->embed(Swift_Image::fromPath("http://res.cloudinary.com/apss-factory/image/upload/a_exif/v1582274055/ackjwfswxqtv4kuhethg.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->setBody(
            $this->templating->render(
            // templates/emails/registration.html.twig
                'emails/newPass.html.twig', ['Url' => $url , "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                ]
            ), 'text/html'
        );
        $signMail= $this->params->get('signMail');
        if($signMail == 'yes'){
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));

            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName , $selector);
            $message->attachSigner($signer);
        }
        $x = $this->mailer->send($message);
        return $x;
    }

    function getUserAcc($id) {

        $user = $this->em->getRepository(User::class)->find($id);
        return $user;
    }

    function getUserAccALL() {

        $user = $this->em->getRepository(User::class)->findByRole('ROLE_ACC');
        return $user;
    }

    function getUserbyRole($role) {

        $user = $this->em->getRepository(User::class)->findByRole($role);
        return $user;
    }

    function affectationRole($userId, $role) {
        $roles = "[$role]";
        $user = $this->em->getRepository(User::class)->find($userId);
        $user->setRoles($roles);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function affectationStatut($userId, $statutref) {
        $user = $this->em->getRepository(User::class)->find($userId);
        $statut = $this->em->getRepository(Ref::class)->find($statutref);
        $user->setStatut($statut);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function EnvoyerEmailNewUser($user) {
        $RefEmail = $this->em->getRepository(Ref::class)->find(20);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail));



        $sendTo = $user->getEmail();
        $senduser = $user;
        $message = (new \Swift_Message('Bienvenue à 5sur5 séjour '))
            ->setFrom('5sur5sejour@5sur5sejour.com')
            ->setTo($sendTo)
            ->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));

        $message->setBody(
            $this->templating->render(
                'emails/DemandeCreationUser.html.twig', ["Nomdestinataire" => $user->getNom(),
                    "Predestinataire" => $user->getPrenom(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    "identifiant" => $sendTo,
                    "senduser" => $senduser,
                    "roles"=>$user->getRole()]
            ), 'text/html'
        );

        $signMail= $this->params->get('signMail');
        if($signMail == 'yes'){
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName , $selector);
            $message->attachSigner($signer);
        }
        try {
            $this->mailer->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }

    function setadresslivraison($num, $rue, $ville, $pays, $codepostal, $iduser, $prenom, $nom, $organism, $prenomfacturation, $nomfacturation, $organismfacturation, $numfacturation, $ruefacturation, $villefacturation, $paysfacturation, $codepostalfacturation, $usernomAcommpa, $userprenomAcommpa, $userfonctionAcommpa, $useretablismentAcommpa, $useremailcommpa, $sejourthem, $adresssejour, $id) {
        $user = $this->em->getRepository(User::class)->find($iduser);
        $sejour = $this->em->getRepository(Sejour::class)->find($id);



        $Adress = new Adress;
        $Adress->setNumadress($num);
        $Adress->setRuevoi($rue);
        $Adress->setCodepostal($codepostal);
        $Adress->setVille($ville);
        $Adress->setPays($pays);
        $Adress->setOrganism($organism);
        $Adress->setNomadrres($nom);
        $Adress->setPrenomadress($prenom);

        $Adress->settype('livraison');
        $this->em->persist($Adress);
        $this->em->flush();

        $user->setadresslivraison($Adress);

        $AdressFacturation = new Adress;
        $AdressFacturation->setNumadress($numfacturation);
        $AdressFacturation->setRuevoi($ruefacturation);
        $AdressFacturation->setCodepostal($codepostalfacturation);
        $AdressFacturation->setVille($villefacturation);
        $AdressFacturation->setPays($paysfacturation);
        $AdressFacturation->setOrganism($organismfacturation);
        $AdressFacturation->setNomadrres($nomfacturation);
        $AdressFacturation->setPrenomadress($prenomfacturation);


        $AdressFacturation->settype('Facturation');
        $this->em->persist($AdressFacturation);
        $this->em->flush();


        $user->setAdressfactoration($AdressFacturation);



        $user->setNom($usernomAcommpa);
        $user->setPrenom($userprenomAcommpa);
        $user->setEmail(trim($useremailcommpa));
        $user->setFonction($userfonctionAcommpa);
        $user->setEtablisment($useretablismentAcommpa);

        $this->em->persist($user);
        $this->em->flush();


        $sejour->setThemSejour($sejourthem);
        $sejour->setAdresseSejour($adresssejour);
        $this->em->persist($sejour);
        $this->em->flush();

        return $user;
    }

    function setadressfacturation($nom, $rue, $ville, $pays, $codepostal, $iduser) {
        $user = $this->em->getRepository(User::class)->find($iduser);

        $Adress = new Adress;
        $Adress->setNumadress($nom);
        $Adress->setRuevoi($rue);
        $Adress->setCodepostal($codepostal);
        $Adress->setVille($ville);
        $Adress->setPays($pays);
        $Adress->settype('facturation');
        $this->em->persist($Adress);
        $this->em->flush();

        $user->setadresslivraison($Adress);


        return $user;
    }

    function setDescriptionAttch($id, $decription) {
        $attachment = $this->em->getRepository(Attachment::class)->find($id);

        $attachment->setDescreption($decription);
        $this->em->persist($attachment);
        $this->em->flush();




        return $attachment;
    }

    function supprimaudio($id) {
        $attachment = $this->em->getRepository(Attachment::class)->find($id);
        $sejattachment = $this->em->getRepository(SejourAttachment::class)->findBy(array('idAttchment' => $attachment));
        foreach ($sejattachment as $sejar) {
            $this->em->remove($sejar);
            $this->em->flush();
        }

        $this->em->remove($attachment);
        $this->em->flush();




        return ("done");
    }

    function ajouterdatesejourdescription($id, $description, $datedescription) {
        $sejour = $this->em->getRepository(Sejour::class)->find($id);

        $Jourdescripdate = new Jourdescripdate;


        $dat = date_create_from_format('m/d/Y', $datedescription);
        $Jourdescripdate->setDatejourphoto($dat);
        $Jourdescripdate->setDescription($description);
        $Jourdescripdate->setIdIdsejour($sejour);
        $this->em->persist($Jourdescripdate);
        $this->em->flush();



        return $Jourdescripdate;
    }

    function supprimdescription($iddescription) {
        $Jourdescripdate = $this->em->getRepository(Jourdescripdate::class)->find($iddescription);



        $this->em->remove($Jourdescripdate);
        $this->em->flush();


        return $Jourdescripdate;
    }
    function activationmail ($idparent){
        $user = $this->em->getRepository(User::class)->find($idparent);
        $user->setActivatemail(1);

        $this->em->persist($user);
        $this->em->flush();
        return $user;

    }
    function modificationdescription($iddescription, $description) {
        $Jourdescripdate = $this->em->getRepository(Jourdescripdate::class)->find($iddescription);
//dd($iddescription);
        $Jourdescripdate->setDescription($description);

        $this->em->persist($Jourdescripdate);
        $this->em->flush();


        return $Jourdescripdate;
    }

    function creationNewParent($nom, $prenom, $mailparent, $numtel, $role, $passwordparent, $notifsms,$notifmail) {



        ini_set("max_execution_time", -1);
        ini_set('memory_limit', '-1');

        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setUsername($mailparent);
        $user->setSmsnotif($notifsms);
        $user->setMailnotif($notifmail);

        // $user->setUsername($identifiant);

        $user->setNummobile($numtel);
        $user->setEmail(trim($mailparent));



        $user->setDateCreation(new \DateTime());
        $user->addRole($role);

        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $passwordparent
            )
        );

        $this->em->persist($user);
        $this->em->flush();

        //$ParentSejour = new ParentSejour();
        //$ParentSejour->setIdParent($user);
        //$ParentSejour->setDateCreation(new \DateTime());
        // $this->em->persist($ParentSejour);
        //$this->em->flush();

        $this->EnvoyerEmailNewParent($user);
        $this->EnvoyerEmailNewParentActivation($user);


        return $user;
    }

    function EnvoyerEmailNewParentActivation($user)  {


        $message = (new \Swift_Message('activation compte '))
            ->setFrom("5sur5sejour@5sur5sejour.com")
            ->setTo(trim($user->getEmail()))
            ->setBcc("contact@5sur5sejour.com");
        $image1 = $message->embed(Swift_Image::fromPath("http://res.cloudinary.com/apss-factory/image/upload/a_exif/v1582274063/qw7csxayektmq1leqjpp.png"));
        $image2 = $message->embed(Swift_Image::fromPath("http://res.cloudinary.com/apss-factory/image/upload/a_exif/v1582274055/ackjwfswxqtv4kuhethg.png"));

        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->setBody(
            $this->templating->render(
            // templates/emails/registration.html.twig
                'emails/Activatiocompte.html.twig', ["accompagnateur"=>$user , "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                ]
            ), 'text/html'
        );
        $signMail= $this->params->get('signMail');
        if($signMail == 'yes'){
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));

            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName , $selector);
            $message->attachSigner($signer);
        }
        $x = $this->mailer->send($message);
        return $x;



    }




    function EnvoyerEmailNewAcommpatActivation($sejour)  {


        $message = (new \Swift_Message('activation compte  '))
            ->setFrom("5sur5sejour@5sur5sejour.com")
            ->setTo($sejour->getIdAcommp()->getReponseemail())
            ->setBcc("contact@5sur5sejour.com");
        $image1 = $message->embed(Swift_Image::fromPath("http://res.cloudinary.com/apss-factory/image/upload/a_exif/v1582274063/qw7csxayektmq1leqjpp.png"));
        $image2 = $message->embed(Swift_Image::fromPath("http://res.cloudinary.com/apss-factory/image/upload/a_exif/v1582274055/ackjwfswxqtv4kuhethg.png"));

        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $message->setBody(
            $this->templating->render(
            // templates/emails/registration.html.twig
                'emails/Activatiocompteaccompa.html.twig', ["accompagnateur"=>$sejour->getIdAcommp(), "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "sejour"=>$sejour
                ]
            ), 'text/html'
        );
        $signMail= $this->params->get('signMail');
        if($signMail == 'yes'){
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));

            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName , $selector);
            $message->attachSigner($signer);
        }
        $x = $this->mailer->send($message);
        return $x;



    }









    function EnvoyerEmailNewParent($user) {

        $RefEmail = $this->em->getRepository(Ref::class)->find(26);

        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));


        $sendTo = $user->getEmail();

        $message = (new \Swift_Message('Création de votre compte parent 5sur5sejour'))
            ->setFrom('5sur5sejour@5sur5sejour.com')
            ->setTo($sendTo)
            ->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));

        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $imggarcon = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582558737/shutterstock_13123054_ghafyg.png"));

        $message->setBody(
            $this->templating->render(
                'emails/Inscriptionparent.html.twig', [
                    "Nomdestinataire" => $user->getNom(),
                    "Predestinataire" => $user->getPrenom(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    "imggarcon" => $imggarcon,
                    "identifiant" => $sendTo,
                    'accompagnateur' => $user
                ]
            ), 'text/html'
        );

        $signMail= $this->params->get('signMail');
        if($signMail == 'yes'){
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName , $selector);
            $message->attachSigner($signer);
        }
        try {
            $this->mailer->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }

    function verifmailold($mail) {
        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => $mail));

        return $user;
    }

    function notifparentsejour($mail, $sms, $idSejour, $iduser, $refaverti) {
        // $user = $this->em->getRepository(User::class)->find($iduser);
        // $sejour = $this->em->getRepository(Sejour::class)->find($idSejour);



        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $iduser]);

        $user->setSmsnotif($sms);
        $user->setMailnotif($mail);
        $this->em->persist($user);
        $parentsejour = $this->em->getRepository(ParentSejour::class)->findBy(['idParent' => $user]);
        foreach($parentsejour as $sejr){
            $sejr->setSmsnotif($sms);
            $sejr->setMailnotif($mail);
            $this->em->persist($sejr);
        }
        $this->em->flush();


        return $user;
    }

    function sendmailuserforfirstattach($sejId) {
        $liste = [];
        $parentsejour = $this->em->getRepository(ParentSejour::class)->findBy(array('idSejour' => $sejId, 'mailnotif' => 1));
        $RefEmail = $this->em->getRepository(Ref::class)->find(27);

        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));

        foreach ($parentsejour as $parent) {
            // $liste = $e->getName();
            //$user = $this->em->getRepository(User::class)->find($parent->getIdParent());
            array_push($liste, $parent->getIdParent()->getEmail());


            $sendTo = $parent->getIdParent()->getEmail();
            $message = (new \Swift_Message('Nouveau dépôt'))
                ->setFrom('5sur5sejour@5sur5sejour.com')
                ->setTo($sendTo)
                ->setBcc(["contact@5sur5sejour.com"]);


            $pathImage2 = $Email->getIdImage2()->getPath();
            $pathImage1 = $Email->getIdImage1()->getPath();
            $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
            $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
            $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
            $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
            $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
            $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
            $message->setBody(
                $this->templating->render(
                    'emails/MailAChaqueDepot.html.twig', [
                        "image1" => $image1,
                        "image2" => $image2,
                        "iconfooter" => $iconfooter,
                        "iconphoto" => $iconphoto,
                        "iconloca" => $iconloca,
                        "iconmsg" => $iconmsg,
                    ]
                ), 'text/html'
            );
            $signMail= $this->params->get('signMail');
            if($signMail == 'yes'){
                $domainName = $this->params->get('domaine');
                $selector = $this->params->get('selector');
                $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
                $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName , $selector);
                $message->attachSigner($signer);
            }

            try {
                $this->mailer->send($message);
            } catch (\Swift_SwiftException $ex) {
                $ex->getMessage();
            }

        }
        return $liste;
    }

    function listeEtablissement() {
        $liste = $this->em->getRepository(Etablisment::class)->findByRole('ROLE_PARTENAIRE');

        return $liste;
    }

    function listePartenaire() {
        $liste = $this->em->getRepository(User::class)->findByRole('ROLE_PARTENAIRE');

        return $liste;
    }

    function addtoPanier($id) {
        $panier = $this->session->get('Panier');
        if ($panier == null) {
            $panier = [];
        }
        $panier[] = $id;
        $this->session->set('Panier', $panier);
        return $panier;
    }

    function GetListePanier($id) {
        $panier = $this->session->get('Panier');

        $MonPanier = $this->em->getRepository(Produit::class)->findBy(array('id' => $panier), array('id' => 'DESC'));

        return $MonPanier;
    }

    function condition($id) {


        $sejour = $this->em->getRepository(Sejour::class)->find($id);
        $sejour->setCd(1);

        $this->em->persist($sejour);
        $this->em->flush();
        return $sejour;
    }


    function getidlistePartenaire($id) {


        $user = $this->em->getRepository(User::class)->find($id);


        return $user;
    }

    function creationCompteP($nomP, $prenomP, $identifiantP, $phoneP, $emailP, $roleP, $infoComplementaireP, $CompteBancaire, $userS, $password) {

        $user = new User();
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setRoles($roleP);

        $user->setInfocomple($infoComplementaireP);
        $user->setComptebanque($CompteBancaire);
        $user->setUsersecondaire($userS);
        $password=$this->genererPassword(10);
        $user->setPasswordNonCripted($password);

        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function creationComptePrincipale($nomP, $prenomP, $identifiantP, $phoneP, $emailP, $roleP, $infoComplementaireP, $CompteBancaire, $password) {

        // Compte Principale
        $user = new User();
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setRoles($roleP);

        $user->setInfocomple($infoComplementaireP);
        $user->setComptebanque($CompteBancaire);
        $password=$this->genererPassword(10);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function creationComptePrinc($nomP, $prenomP, $identifiantP, $phoneP, $emailP, $roleP, $infoComplementaireP, $password) {

        // Compte Principale
        $user = new User();
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setRoles($roleP);
        $user->setInfocomple($infoComplementaireP);
        $password=$this->genererPassword(10);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function creationCompteUSERP($nomP, $prenomP, $identifiantP, $phoneP, $emailP, $roleP, $userS, $infoComplementaireP, $password) {

        // Compte Principale
        $user = new User();
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setRoles($roleP);
        $user->setUsersecondaire($userS);
        $user->setInfocomple($infoComplementaireP);
        $password=$this->genererPassword(10);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function USERP($id, $userS) {
        // Compte Principale
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setUsersecondaire($userS);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function creationCompteS($nomS, $prenomS, $identifiantS, $phoneS, $emailS, $infoComplementaireS, $roleS, $password) {

        $user = new User();
        $user->setNom($nomS);
        $user->setPrenom($prenomS);
        $user->setUsername($identifiantS);
        $user->setNummobile($phoneS);
        $user->setEmail(trim($emailS));
        $user->setInfocomple($infoComplementaireS);
        $user->setRoles($roleS);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    function creationCompteBancaire($codebnaque, $codeguichet, $numcompt, $clerib, $iban, $codebic, $nom, $domicilation) {
        // Compte Bancaire
        $comptebancaire = new Comptebancaire();
        $comptebancaire->setCodebnaque($codebnaque);
        $comptebancaire->setCodeguichet($codeguichet);
        $comptebancaire->setNumcompt($numcompt);
        $comptebancaire->setClerib($clerib);
        $comptebancaire->setIban($iban);
        $comptebancaire->setCodebic($codebic);
        $comptebancaire->setNom($nom);
        $comptebancaire->setDomicilation($domicilation);

        $this->em->persist($comptebancaire);
        $this->em->flush();
        return $comptebancaire;
    }

    function USERPP($id, $comptebancaire) {
        // Compte Principale
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setComptebanque($comptebancaire);
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    public function ModifcompteBancaire($id,$codebnaque, $codeguichet, $numcompt, $clerib, $iban, $codebic, $nom, $domicilation) {

        $comptebancaire = $this->em->getRepository(Comptebancaire::class)->find($id);
        if ($comptebancaire == null) {
            $comptebancaire = new Comptebancaire();
        }
        $comptebancaire->setCodebnaque($codebnaque);
        $comptebancaire->setCodeguichet($codeguichet);
        $comptebancaire->setNumcompt($numcompt);
        $comptebancaire->setClerib($clerib);
        $comptebancaire->setIban($iban);
        $comptebancaire->setCodebic($codebic);
        $comptebancaire->setNom($nom);
        $comptebancaire->setDomicilation($domicilation);


        $this->em->persist($comptebancaire);
        $this->em->flush();
        return $comptebancaire;
    }

    public function updatepartenaireP($id, $nomP, $prenomP, $identifiantP, $phoneP, $emailP, $infoComplementaireP, $roleP, $usersecon) {

        $user = $this->em->getRepository(User::class)->find($id);
        $envoiMailEmailModifier=true;
        if($emailP !=$user->getEmail())
        {$envoiMailEmailModifier=true;}
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setInfocomple($infoComplementaireP);
        $user->setRoles($roleP);
        if ($usersecon) {
            $user->setUsersecondaire($usersecon);
        }
        $this->em->persist($user);
        $this->em->flush();

        if($envoiMailEmailModifier)
        {
            $this->EnvoyerEmailComptePartenaireModifier($user);
        }

        return $user;
    }

    function EnvoyerEmailComptePartenaireModifier($user)
    {

        $logo='';
        $nom='';
        if ($user->hasRole('ROLE_PARTENAIRE')){
            $logo = $user->getLogourl();
            $nom = $user->getNometablisment();

        }
        $RefEmail = $this->em->getRepository(Ref::class)->find(21);

        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail, 'statut' => 9));


        $message = (new \Swift_Message('Bienvenue à 5sur5 séjour '))
            ->setFrom('partenariat-5sur5sejour@5sur5sejour.com')
            ->setTo($user->getEmail())
            ->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $icon2 = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $icon3 = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));

        $message->setBody(
            $this->templating->render(
                'emails/ComptePartenaireModifier.html.twig', ["user"=>$user,
                    "image1" => $image1,
                    "image2" => $image2,
                    "icon2" => $icon2,
                    "icon3" => $icon3,
                    "iconfooter" => $iconfooter,
                    "iconmsg" => $iconmsg,
                    'logo'=>$logo,
                    'nom'=>$nom,

                ]
            ),
            'text/html'
        );
        $signMail= $this->params->get('signMail');
        if($signMail == 'yes'){
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey =  file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName , $selector);
            $message->attachSigner($signer);
        }
        try {
            $this->mailer->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }



    }

    public function updatepartenairePrinc($id, $nomP, $prenomP, $identifiantP, $phoneP, $emailP, $infoComplementaireP, $roleP) {

        $user = $this->em->getRepository(User::class)->find($id);
        $envoiMailEmailModifier=true;
        if($emailP !=$user->getEmail())
        {$envoiMailEmailModifier=true;}
        $user->setNom($nomP);
        $user->setPrenom($prenomP);
        $user->setUsername($identifiantP);
        $user->setNummobile($phoneP);
        $user->setEmail(trim($emailP));
        $user->setInfocomple($infoComplementaireP);
        $user->setRoles($roleP);
        $this->em->persist($user);
        $this->em->flush();

        if($envoiMailEmailModifier)
        {
            $this->EnvoyerEmailComptePartenaireModifier($user);
        }

        return $user;
    }

    public function updatepartenaireS($user, $nomS, $prenomS, $identifiantS, $phoneS, $emailS, $infoComplementaireS, $roleS) {
        if ($user) {
            //$user = $this->em->getRepository(User::class)->find($id);
            //$idusersecondaire= $user->getUsersecondaire();
            //$user = $this->em->getRepository(User::class)->find($idusersecondaire);
            $user->setNom($nomS);
            $user->setPrenom($prenomS);
            $user->setUsername($identifiantS);
            $user->setNummobile($phoneS);
            $user->setEmail(trim($emailS));
            $user->setInfocomple($infoComplementaireS);
            $user->setRoles($roleS);

            $this->em->persist($user);
            $this->em->flush();
        } else {
            if (($nomS != null) || ($prenomS != null) || ($identifiantS != null) || ($phoneS != null) || ($emailS != null) || ($infoComplementaireS != null) || ($roleS != null)) {
                $user = $this->creationCompteS($nomS, $prenomS, $identifiantS, $phoneS, $emailS, $infoComplementaireS, "ROLE_PARTENAIRE", 'azerty123');
            }
        }

        return $user;
    }

    public function listesejourPartenaireconnecter($user) {
        $sejours = $this->em->getRepository(Sejour::class)->NombreofsejourParten($user->getId());

        return $sejours;
    }

    function creationNewAcommpaviaenmasse($nom, $prenom, $etablisment, $fonction, $adressetablisment, $phoneacc, $mail, $role, $password, $AccompaLogo, $email) {

        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setUsername($mail);
        $user->setReponseemail(trim($email));
        // $user->setUsername($identifiant);
        $user->setFonction($fonction);
        $user->setNummobile($phoneacc);
        $user->setEmail(trim($mail));
        $user->setAdresse($adressetablisment);
        $user->setNometablisment($etablisment);
        // Firas : ajouter logo
        $user->setLogourl($AccompaLogo);
        //$user->setnometablisment();

        $user->setDateCreation(new \DateTime());
        $user->addRole($role);
        $user->setPasswordNonCripted($password);
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );

        $this->em->persist($user);
        $this->em->flush();


        return $user;
    }




    function   modifieradress_comande( $rue, $ville, $pays, $codepostal, $iduser, $prenom, $nom, $organism, $prenomfacturation, $nomfacturation, $organismfacturation,$ruefacturation, $villefacturation, $paysfacturation, $codepostalfacturation){


        $user = $this->em->getRepository(User::class)->find($iduser);



        $Adress = new Adress;

        $Adress->setRuevoi($rue);
        $Adress->setCodepostal($codepostal);
        $Adress->setVille($ville);
        $Adress->setPays($pays);
        $Adress->setOrganism($organism);
        $Adress->setNomadrres($nom);
        $Adress->setPrenomadress($prenom);

        $Adress->settype('livraison');
        $this->em->persist($Adress);
        $this->em->flush();

        $user->setadresslivraison($Adress);

        $AdressFacturation = new Adress;

        $AdressFacturation->setRuevoi($ruefacturation);
        $AdressFacturation->setCodepostal($codepostalfacturation);
        $AdressFacturation->setVille($villefacturation);
        $AdressFacturation->setPays($paysfacturation);
        $AdressFacturation->setOrganism($organismfacturation);
        $AdressFacturation->setNomadrres($nomfacturation);
        $AdressFacturation->setPrenomadress($prenomfacturation);


        $AdressFacturation->settype('Facturation');
        $this->em->persist($AdressFacturation);
        $this->em->flush();


        $user->setAdressfactoration($AdressFacturation);




        $this->em->persist($user);
        $this->em->flush();



        return $user;
    }


    function codesecuriter($code,$id) {
        $user = $this->em->getRepository(User::class)->find($id);

        $comptebancaire =  new Comptebancaire ;
        $comptebancaire->setNumcompt($code);
        $this->em->persist($comptebancaire);
        $this->em->flush();
        $user->setComptebanque($comptebancaire);
        $this->em->persist($user);
        $this->em->flush();



    }


    function genererPassword($longueur)
    {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longueurMax = strlen($caracteres);
        $chaineAleatoire = '';
        for ($i = 0; $i < $longueur; $i++)
        {
            $chaineAleatoire .= $caracteres[rand(0, $longueurMax - 1)];
        }
        return $chaineAleatoire;
    }


    function modifierDetailsAcco($idAcco,$nom,$prenom,$email,$tel)
    {
        $Acco = $this->em->getRepository(User::class)->find($idAcco);
        $Acco->setPrenom($prenom);
        $Acco->setNom($nom);
        $Acco->setNummobile($tel);
        $Acco->setReponseemail(trim($email));
        $this->em->persist($Acco);
        $this->em->flush();
    }



    function AjouterDocument_partenaitre($cloudImagescouleur,$nomdocument,$Etablisment_Find){

        $Documentpartenaire = new Documentpartenaire();
        $Documentpartenaire->setIdetablisment($Etablisment_Find);
        $Documentpartenaire->setNomdocument($nomdocument);
        $Documentpartenaire->setPath($cloudImagescouleur[0]["path"]);
        $this->em->persist($Documentpartenaire);
        $this->em->flush();

    }

    function conditioncnx($id) {


        $user = $this->em->getRepository(User::class)->find($id);
        $user->setCnxpartenaire(1);

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }
    function conditioncnxparent($id) {


        $user = $this->em->getRepository(User::class)->find($id);
        $user->setCnxparent(1);

        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }
    function findsejParent($id_parent){
        $ParentSejour = $this->em->getRepository(ParentSejour::class)->find($id_parent);
        return $ParentSejour;
    }








    function EnvoyerEmailNewUserediteuroradmin($user,$password) {
        $RefEmail = $this->em->getRepository(Ref::class)->find(20);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail));



        $sendTo = $user->getEmail();
        $senduser = $user;
        $message = (new \Swift_Message('Nouveau admin ou editeur'))
            ->setFrom('5sur5sejour@5sur5sejour.com')
            ->setTo($sendTo)
            ->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));

        $message->setBody(
            $this->templating->render(
                'emails/DesmandecreationAdminorEditeur.html.twig', ["Nomdestinataire" => $user->getNom(),
                    "Predestinataire" => $user->getPrenom(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    "identifiant" => $sendTo,
                    "senduser" => $senduser,
                    "roles"=>$user->getRole(),
                    "pass"=>$password]
            ), 'text/html'
        );

        try {
            $this->mailer->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }









    function ModifierPwdParent($idParent,$passwordparent) {




        $user =  $this->em->getRepository(User::class)->find($idParent);

        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $passwordparent
            )
        );

        $this->em->persist($user);
        $this->em->flush();


        $this->EnvoyerEmailPWDModifer($user);



        return $user;
    }

    function ModifierPwdPartenaire($idPatenaire,$passwordpartenaire) {




        $user =  $this->em->getRepository(User::class)->find($idPatenaire);

        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $passwordpartenaire
            )
        );

        $this->em->persist($user);
        $this->em->flush();


        $this->EnvoyerEmailPWDModifer($user);



        return $user;
    }


    function EnvoyerEmailPWDModifer($user) {
        $RefEmail = $this->em->getRepository(Ref::class)->find(20);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail));



        $sendTo = $user->getEmail();
        $senduser = $user;
        $message = (new \Swift_Message('Modification de votre mot de passe 5sur5sejour'))
            ->setFrom('5sur5sejour@5sur5sejour.com')
            ->setTo($sendTo)
            ->setBcc(["contact@5sur5sejour.com"]);
        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconphoto = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));
        $iconloca = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));

        $message->setBody(
            $this->templating->render(
                'emails/PwdModifer.html.twig', ["Nomdestinataire" => $user->getNom(),
                    "Predestinataire" => $user->getPrenom(),
                    "image1" => $image1,
                    "image2" => $image2,
                    "iconfooter" => $iconfooter,
                    "iconphoto" => $iconphoto,
                    "iconloca" => $iconloca,
                    "iconmsg" => $iconmsg,
                    "identifiant" => $sendTo,
                    "senduser" => $senduser,
                    "roles"=>$user->getRole(),
                ]
            ), 'text/html'
        );

        try {
            $this->mailer->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }
    }


    function creationAccompagnateurPlus($nom,$prenom,$fonction,$nomEtab,$addressEtab,$numTel,$mail,$password,$role)
    {
        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setNummobile($numTel);
        $user->setFonction($fonction);
        $user->setAdresse($addressEtab);
        $user->setNometablisment($nomEtab);
        $user->setEmail(trim($mail));
        $user->setReponseemail(trim($mail));
        $user->setUsername(trim($mail));
        $user->setPassword(
            $this->passwordEncoder->encodePassword(
                $user, $password
            )
        );
        $user->setAccompaplus("oui");
        $user->setPasswordNonCripted($password);

        $user->setDateCreation(new \DateTime());
        $user->addRole($role);
        $this->em->persist($user);
        $this->em->flush();
        return $user;

    }



    function EnvoyerEmailAccoPlusDevenirPartenaire($user) {
        $logo = '';
        $nom = '';
        if ($user->hasRole('ROLE_PARTENAIRE')) {
            $logo = $user->getLogourl();
            $nom = $user->getNometablisment();

        }
        $RefEmail = $this->em->getRepository(Ref::class)->find(17);
        $Email = $this->em->getRepository(Emailing::class)->findOneBy(array('typeemail' => $RefEmail));
        $sendTo = $user->getEmail();
        $message = (new \Swift_Message('Déjà 5 séjours ensemble, devenons partenaire ?'))
            ->setFrom('partenariat-5sur5sejour@5sur5sejour.com')
            ->setTo($sendTo)
            ->setBcc(["contact@5sur5sejour.com"]);

        $pathImage2 = $Email->getIdImage2()->getPath();
        $pathImage1 = $Email->getIdImage1()->getPath();
        $image1 = $message->embed(Swift_Image::fromPath("$pathImage1"));
        $image2 = $message->embed(Swift_Image::fromPath("$pathImage2"));
        $iconmsg = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Groupe_418_wbyysk.png"));
        $iconfooter = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719651/Picto5sur5_rapbfy.png"));
        $icon2 = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_420_uynuqz.png"));
        $icon3 = $message->embed(Swift_Image::fromPath("https://res.cloudinary.com/apss-factory/image/upload/v1582719650/Groupe_419_pqx0dx.png"));

        $message->setBody(
            $this->templating->render(
                'emails/AccompagnateurPlusDevenirPartenaire.html.twig', [
                    "image1" => $image1,
                    "image2" => $image2,
                    "icon2" => $icon2,
                    "icon3" => $icon3,
                    "iconfooter" => $iconfooter,
                    "iconmsg" => $iconmsg,
                    "identifiant" => $sendTo,
                    'logo' => $logo,
                    'nom' => $nom,
                ]
            ),
            'text/html'
        );
        $signMail = $this->params->get('signMail');
        if ($signMail == 'yes') {
            $domainName = $this->params->get('domaine');
            $selector = $this->params->get('selector');
            $PrivateKey = file_get_contents($this->params->get('pathDKIM'));
            $signer = new \Swift_Signers_DKIMSigner($PrivateKey, $domainName, $selector);
            $message->attachSigner($signer);
        }
        try {
            $this->mailer->send($message);
        } catch (\Swift_SwiftException $ex) {
            $ex->getMessage();
        }

    }

}
