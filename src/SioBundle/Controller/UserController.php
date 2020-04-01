<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SioBundle\Controller;

use SioBundle\Entity\User;
use SioBundle\Services\Mailer;
use SioBundle\Entity\Situation;
use AdminBundle\Entity\Competences;
use AdminBundle\Entity\Referentiel;
use SioBundle\Entity\SituationDetails;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;


/**
 * User controller.
 *
 * @Route("home")
 */
class UserController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var ObjectManager
     */
    private $manager;
    
    /**
     * Undocumented variable
     *
     * @var Mailer
     */
    private $mailer;

    /**
     * Undocumented function
     *
     * @param ObjectManager $manager
     * @param Mailer $mailer
     */
    public function __construct (ObjectManager $manager, Mailer $mailer) 
    {

        $this->manager = $manager;
        $this->mailer  = $mailer;

    }
    
    /**
     * Undocumented function
     * 
     * @Route("/", name="user_index", methods={"GET"})
     * @Route("/{user}", name="user_show", methods={"GET"})
     * @return void
     */
    public function index (User $user = null)
    {
        
        if (!$user) {

            $user = $this->get('security.token_storage')->getToken()->getUser();

        }

        $security = $this->get('security.authorization_checker');
        $repos    = $this->getDoctrine()->getRepository(Situation::class);
        $situationList = $repos->findBy([
            'user'  => $user,
        ]);  
            
        return $this->render('User/index.html.twig', [
  
            'situationList' => $situationList,
            'user'          => $user,
            
        ]);
            
    }
    

    /**
     * Undocumented function
     * 
     * @Route("/account/register", name="user_new", methods={"GET", "POST"})
     * @param Request $request
     * @return void
     */
    public function newUser(Request $request)
    {
        
        $user    = new User;
        $form    = $this->createForm('SioBundle\Form\UserType', $user);
        
        if("POST" === $request->getMethod()) {
            
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
                
                if (  $form["password"]->getData() === $form["confirm_password"]->getData() ) {
                    
                    /**
                     *  Création de la clé de confirmation
                     */
                    $registerKey = mt_rand(10, 255); 

                    /**
                     * Cryptage du mot de passe utilisateur
                     * Attribution du rôle utilisateur
                     * Enregistrement BDD
                     */
                    $hash = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
                    $user->setPassword($hash);
                    $user->setRegisterKey($registerKey);
                    $user->setRoles(['ROLE_USER']);
                    $this->manager->persist($user);
                    $this->manager->flush();

                    /**
                     * Envoi email confirmation de compte et vérification
                     */
                    $this->mailer->accountCreated($user);

                    $this->addFlash('success', 'Votre compte à bien été enregistré. Un email de confirmation vous a été envoyé !');
                    return $this->render('User/Registration/register.html.twig', 
                        array('form' => $form->createview())
                    );

                }
                
                $message[] = "Les mots de passe saisies ne sont pas identiques !";
                return $this->render('User/Registration/register.html.twig', [
                    'form'    => $form->createview(),
                    'message' => $message
                ]);
                
            } else  {
                
                $errors = $this->get('validator')->validate($user);
                
                return $this->render('User/Registration/register.html.twig', [
                    'form' => $form->createview(),
                    'errors' => $errors
                ]);
            
            }
                
        } else {
                
            return $this->render('User/Registration/register.html.twig', 
                array('form' => $form->createview())
            );
                
        } 
        
    }

    
    /**
     * Undocumented function
     * 
     * @Route("/myaccount/profile", name="user_edit", methods={"GET", "POST"})
     * @param Request $request
     * @return void
     */
    public function editUser (Request $request) 
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $user     = $this->get('security.token_storage')->getToken()->getUser();
        $form     = $this->createFormBuilder($user)
                         ->add('firstname', TextType::class)
                         ->add('lastname', TextType::class)
                         ->add('username', TextType::class)
                         ->add('email', EmailType::class)
                         ->add('password', PasswordType::class, [
                            'mapped' => false
                        ])
                        ->getForm();
        
        $form->handleRequest($request);
        
        if ( "POST" === $request->getMethod()) {
            
            if ($form->isSubmitted() && $form->isValid()) {
                            
                if ( password_verify($form["password"]->getData(), $user->getPassword()) ) {

                    $this->manager->persist($user);
                    $this->manager->flush();

                    return $this->redirectToRoute('user_index');
                } 
                    
                $this->manager->refresh($user);  
                
                $message[] = 'Mot de passe incorrect !';
                return $this->render('User/Profile/edit.html.twig', [
                    'form' => $form->createview(),
                    'message' =>  $message
                ]);
                
            } else {
                
                $errors = $this->get('validator')->validate($user);

                return $this->render('User/Profile/edit.html.twig', [
                    'form' => $form->createview(),
                    'errors' => $errors
                ]);
                
            }      
            
        } else {
            
            return $this->render('User/Profile/edit.html.twig', [
                'form' => $form->createview()
            ]);
            
        }
        
    }

    
    /**
     * Undocumented function
     * 
     * @Route("/myaccount/profile/description", name="user_edit_description", methods={"GET", "POST"})
     * @param Request $request
     * @return void
     */
    public function editUserDescription (Request $request) 
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $form = $this->createFormBuilder($user)
                     ->add('description', CKEditorType::class, [
                        'config_name' => 'my_config',
                     ])
                     ->getForm();
        
        $form->handleRequest($request);
        
        if ( 'POST' === $request->getMethod() ) {
            
            if ( $form->isSubmitted() && $form->isValid() ) {
                
                $this->manager->persist($user);
                $this->manager->flush();

                return $this->redirectToRoute('user_index');
              
            }
            
            $errors = $this->get('validator')->validate($user);

            return $this->render('User/Profile/editDescription.html.twig', [
                'form'   => $form->createview(),
                'errors' => $errors
            ]);
           
        }
        
        return $this->render('User/Profile/editDescription.html.twig', [
            'form'  => $form->createView(),
        ]);
        
    }
    
    
    /**
     * Undocumented function
     * 
     * @Route("/myaccount/profile/delete", name="user_delete", methods={"GET", "POST"})
     * @param Request $request
     * @return void
     */
    public function deleteUser (Request $request) 
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $user =  $this->get('security.token_storage')->getToken()->getUser();
       
        if ( 'POST' === $request->getMethod() )
        {
            
            if ( password_verify($request->request->get('password'), $user->getPassword()) ) {
                    
                $user->setIsActive(false);
                $this->manager->persist($user);
                $this->manager->flush();
                
            }
            
            $message[] = 'Mot de passe incorrect !';
            return $this->render('User/Security/deleteAccount.html.twig', [
                'message' => $message  
            ]);
            
        }
        
        return $this->render('User/Security/deleteAccount.html.twig');
    }
    

    /**
     * Undocumented function
     *
     * @Route("/myaccount/dashboard", name="user_dashboard", methods={"GET"})
     * @return void
     */
    public function dashboard()
    {

        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $competences  = $this->manager->getRepository(Competences::class)->findAll();
        $referentiels = $this->manager->getRepository(Referentiel::class)->findAll();
        $situations   = $this->manager->getRepository(Situation::class)->findBy([
            'user'  =>  $user
        ]);
        
        if ( !$competences || !$referentiels || !$situations ) {

            return $this->render('User/Profile/dashboard.html.twig', [
                'message'   =>  ['Veuillez créer au moins une situation pour accéder au tableau de bord !'],
            ]);

        }

        $elements = Array();

        foreach( $referentiels as $referentiel ){
            
            $exist = false;
            
            foreach( $situations as $situation ) {

                $situationReferentiels = new ArrayCollection();
                $situationReferentiels = $situation->getReferentiels();

                foreach( $situationReferentiels as $situationReferentiel ) {

                    if( $situationReferentiel === $referentiel ) {

                        $exist = true;
    
                    }

                }

            }

            if ( ! $exist ) {

                $elements[] = "".$referentiel->getLibelle();

            }

        }

        return $this->render('User/Profile/dashboard.html.twig', [

            'competences'               =>  $competences,
            'referentiels'              =>  $referentiels,
            'situations'                =>  $situations,
            'competencesObligatoires'   =>  $elements
    
        ]);

    }

    
    /*////////////////////////////////////////////
     *         MODIFICATION DOM AJAX            // 
     *////////////////////////////////////////////
    
    
    /**
     * Undocumented function
     * 
     * @Route("/choice/list", options={"expose"=true}, name="list_choice", methods={"POST", "GET"})
     * @return void
     */
    public function list ()
    {

        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $list = $this->getDoctrine()->getRepository(User::class)->findAll();        
            
        return $this->render('Templates/Navigation/userNav.html.twig', [
            'userList'  =>   $list,
            'user'      =>   $user  
        ]);
        
    }
    
}