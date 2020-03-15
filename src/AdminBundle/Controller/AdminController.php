<?php

namespace AdminBundle\Controller;

use DateTime;
use SioBundle\Entity\User;
use SioBundle\Entity\Picture;
use AdminBundle\Entity\Backup;
use SioBundle\Services\Mailer;
use SioBundle\Entity\Situation;
use SioBundle\Entity\SituationDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("admin")
 */
class AdminController extends Controller
{

    /*******************************
     *          ATTRIBUTS
     ******************************/
    
    /**
     * Undocumented variable
     *
     * @var EntityManagerInterface
     */
    private $manager;
    
    /**
     * Undocumented variable
     *
     * @var TokenStorageInterface
     */
    private $token;                                                                    
    
    /**
     * Undocumented variable
     *
     * @var UserPasswordEncoderInterface
     */
    private $security;
    
    /**
     * Undocumented variable
     *
     * @var [type]
     */
    private $mailer;


    
    /*******************************
     *          METHODES
     ******************************/

    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $manager
     * @param TokenStorageInterface $token
     * @param UserPasswordEncoderInterface $security
     * @param Mailer $mailer
     */
    public function __construct(EntityManagerInterface $manager, TokenStorageInterface $token, UserPasswordEncoderInterface $security, Mailer $mailer)
    {
        
        $this->manager  = $manager;
        $this->token    = $token;
        $this->security = $security;
        $this->mailer   = $mailer;
        
    }
    

    /**
     * Undocumented function
     * 
     * @Route("/dashboard", name="admin_dashboard", methods={"GET"})
     * @return void
     */
    public function dashboard ()
    {
       
        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }
        
        return $this->render('Admin/dashboard.html.twig');
        
    }
    

    /*****************************************************
     *  AFFICHAGE ET GESTION DES SITUATIONS UTILISATEUR 
     ****************************************************/
    

    /**
     * Undocumented function
     * 
     * @Route("/dashboard/posts", name="admin_view_posts", methods={"GET"})
     * @param integer $param
     * @return void
     */
    public function viewPosts (int $param = null)
    {
       
        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }

        return $this->render('Admin/userPost.html.twig');
        
    }
    
    
    /**
     * Undocumented function
     * 
     * @Route("/dashboard/posts/{param}", options={"expose"=true}, name="admin_view_posts_filter", methods={"POST", "GET"})
     * @param integer $param
     * @return void
     */
    public function viewPostsFilter (int $param = null)
    {
       
        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }
        
        switch ($param)
        {

            case 0:
                $userPosts = $this->manager->getRepository(SituationDetails::class)->findAll();
            break;

            case 1:
                $userPosts = $this->manager->getRepository(SituationDetails::class)->findBy([
                    'validated' => 1
                ]); 
            break;
                        
            case 2:
                $userPosts = $this->manager->getRepository(SituationDetails::class)->findBy([
                    'validated' => 0
                ]);
            break;

            default:
                $userPosts = $this->manager->getRepository(SituationDetails::class)->findAll();
            break;

        }

        return $this->render('Templates/Admin/posts.html.twig', [
            'situationDetailsList' => $userPosts
        ]);

    }


    /**
     * Undocumented function
     * 
     * @Route("/dashboard/post/{id}", options={"expose"=true}, name="admin_view_post_filter", methods={"POST"})
     * @param [type] $id
     * @return void
     */
    public function viewPostFilter ($id)
    {
         
        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }
        
        $situationDetails = $this->manager->getRepository(SituationDetails::class)->find($id);
        return $this->render('Templates/Admin/post.html.twig', [
            'situationDetails' => $situationDetails
        ]);
        
    }
    
    
    /**
     * Undocumented function
     * 
     * @Route("/dashboard/post/{id}/{param}", options={"expose"=true}, name="admin_control_post", methods={"GET"})
     * @param SituationDetails $situationDetails
     * @param [type] $param
     * @return void
     */
    public function postValidated (SituationDetails $situationDetails, $param)
    {
        
        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }
        
        $user = $situationDetails->getSituation()->getUser();

        if ($param == 1) {
            
            $situationDetails->setValidated(true);
            $this->manager->persist($situationDetails);
            $this->manager->flush();
            $this->mailer->situationDetailsActivated($user);
            
        } else {
            
            $situationDetails->setValidated(false);
            $this->manager->persist($situationDetails);
            $this->manager->flush();
            $this->mailer->situationDetailsDeactivated($user);

        }     
        
        return $this->redirectToRoute('admin_view_posts');
        
    }
    

    /*************************************************
     *  AFFICHAGE ET GESTION DES COMPTES UTILISATEUR 
     *************************************************/
    
    /**
     * Undocumented function
     * 
     * @Route("/dashboard/accounts", name="admin_view_accounts", methods={"GET"})
     * @return void
     */
    public function viewAccounts ()
    {
       
        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }

         
        $user = $this->token->getToken()->getUser();
        $query = $this->manager->createQueryBuilder() 
                                ->select('user')
                                ->from(User::class, 'user')
                                ->where('user.id <>'.$user->getId())
                                ->getQuery();
         
        $userList = $query->getResult();
         
                
        return $this->render('Admin/userAccount.html.twig', [
            'userList' => $userList,
        ]);
        
    }

    
    /**
     * Undocumented function
     * 
     * @Route("/dashboard/account/{user}/{param}", name="admin_control_account", methods={"GET"})
     * @param User $user
     * @param [type] $param
     * @return void
     */
    public function accountActivated (User $user, $param)
    {
        
        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }
        
        if ($param == 1) {
            
            $user->setIsActive(true);
            $this->manager->persist($user);
            $this->manager->flush();
            $this->mailer->accountActivated($user);
            
        } else {
            
            $user->setIsActive(false);
            $this->manager->persist($user);
            $this->manager->flush();
            $this->mailer->accountDeactivated($user);
            
        }     
        
        return $this->redirectToRoute('admin_view_accounts');
        
    }
    
    
    /**
     * Undocumented function
     * 
     * @Route("/dashboard/roles/{user}/{param}", name="admin_control_role", methods={"GET"})
     * @param User $user
     * @param [type] $param
     * @return void
     */
    public function accountRoles (User $user, $param)
    {
        
        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }
        
        if ( $param == 0 ) {
            
            $user->setRoles(['ROLE_USER']);
            $this->manager->persist($user);
            $this->manager->flush();
            
        } else {
            
            $user->setRoles(['ROLE_ADMIN']);
            $this->manager->persist($user);
            $this->manager->flush();
            
        }
        
        return $this->redirectToRoute('admin_view_accounts');
        
    }

    /**
     * @Route("/backup/bdd/list", name="admin_backup_restaure")
     * @Method({"GET", "POST"})
     */
    function bddBackUpList(Request $request)
    {
        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }
        
        $backupUseByForm = new Backup;
        $backupList = $this->manager->getRepository(Backup::class)->fetchAllBackup();
        $form = $this->createForm('AdminBundle\Form\BackupType', $backupUseByForm);
       
        $form->handleRequest($request);

        if ( "POST" === $request->getMethod() ) {

            if ( $form->isSubmitted() && $form->isValid() ) {
                
                $libelle = $form->get('backups')->getData()->getLibelle();
           
                // Restauration du nom des backups dans la bdd
                $backupsPath = "/home/jimmy/html/TDB/web/backup/BDD/";
                $backupsList = scandir($backupsPath);
                
                $picturesPath = "/home/jimmy/html/TDB/web/backup/images/";
                $picturesList = scandir($picturesPath);

                if ( null !== $libelle && "" !== $libelle ) {

                    exec('mysql -ujimmy -p2018 jimmy_tdb < /home/jimmy/html/TDB/web/backup/BDD/'.$libelle);
                    $this->addFlash('success', 'La base de donnée a été restaurée !');

                    // Restauration des backups existant dans la base de données
                    foreach ( $backupsList as $file ) {

                        $result = $this->manager->getRepository(Backup::class)->fetchBackup($file);
                        
                        if ( 1 !== $result && "." !== $file && ".." !== $file ) {
                            $date = new DateTime(substr($file, 7, 10));
                            $backup = new Backup();
                            $backup->setLibelle($file);
                            $backup->setDate($date);
                            $this->manager->persist($backup);
                            $this->manager->flush();
                        }
                    
                    }

                    // Restauration des images dans le dossier public img
                    foreach ( $picturesList as $picture ) {

                        // Nom de l'image
                        $pos1 = strripos($picture, '.');
                        $name = substr($picture, 0, $pos1);
                        $ext  = substr($picture, $pos1);
                        
                        // Id de l'image
                        $pos2  = strripos($name, '_')+1;
                        $id    = substr($name, $pos2);

                        // Lecture de l'image dans la base de donnée
                        $imgExist = $this->manager->getRepository(Picture::class)->find($id);
                        
                        // Si elle existe j'utilise rsync vers le répertoire des images affichées (pour être sûr de son existence)
                        if ( null !== $imgExist ) {
                            exec('rsync /home/jimmy/html/TDB/web/backup/images/"'.$name.$ext.'" /home/jimmy/html/TDB/web/uploads/img/"'.$name.$ext.'"');
                        }

                    }

                    $form = $this->createForm('AdminBundle\Form\BackupType', $backupUseByForm);

                }
                
            }

            $errors = $this->get('validator')->validate($backupUseByForm);

        }

        return $this->render('Admin/backup.html.twig', [
            'form'   =>  $form->createView(),
        ]);

    }

    /**
     * @Route("/backup/bdd/create", name="admin_backup_bdd")
     * @Method({"GET", "POST"})
     */
    function dump_MySQL()
    {

        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }

        sleep(1);
        $date  = new DateTime();

        do {
            $libelle = "backup_".$date->format('d-m-Y').'_'.$date->getTimestamp();
            $backupExist = $this->manager->getRepository(Backup::class)->findOneBy([
                'libelle'   => $libelle.'.sql'
            ]);
        } while ( null !== $backupExist );

        $libelle .= '.sql';
        $backup = new Backup;
        $backup->setLibelle($libelle);
        $backup->setDate($date);
        
        // Création backup BDD
        exec('mysqldump -ujimmy -p2018 --databases jimmy_tdb > /home/jimmy/html/TDB/web/backup/BDD/'.$libelle);

        // Backup des images
        exec('rsync -r /home/jimmy/html/TDB/web/uploads/img/ /home/jimmy/html/TDB/web/backup/images/');

        $this->manager->persist($backup);
        $this->manager->flush();

        return $this->redirectToRoute('admin_backup_restaure');

    }
    
}
