<?php

namespace SioBundle\Controller;

use SioBundle\Entity\User;
use SioBundle\Entity\Picture;
use SioBundle\Services\Mailer;
use SioBundle\Entity\Situation;
use SioBundle\Entity\SituationDetails;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Referentieldetail controller.
 *
 * @Route("situation/details")
 */
class SituationDetailsController extends Controller
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
     * @Route("/{situation}/new", options={"expose"=true}, name="situation_details_new", methods={"GET", "POST"})
     * @param Request $request
     * @param Situation $situation
     * @return void
     */
    public function newSituation(Request $request, Situation $situation)
    {
       
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $error             = false;
        $errors            = null;
        $message           = null;
        $situationDetails  = new SituationDetails;
        $form              = $this->createForm('SioBundle\Form\SituationDetailsType', $situationDetails);
        $form->handleRequest($request);
        
        if ( "POST" === $request->getMethod() ) {
            
            if ( $form->isSubmitted() && $form->isValid() ) {
                
                if ( count($situationDetails->getCompetences()) !== 0 ) {

                    $situationDetails->setSituation($situation);  
                    
                    foreach ($situationDetails->getPictures() as $picture) {
                        
                        $picture->setSituationDetails($situationDetails);
                        
                    }
                    
                    if ( $this->isGranted('ROLE_ADMIN') ) {

                        $this->addFlash('success', 'Descriptif mis à jour !');
                        $situationDetails->setValidated(true);

                    } else {

                        $this->addFlash('success', 'Votre descriptif à bien été enregistré. Un administrateur le validera sous 24 heures !');
                        $situationDetails->setValidated(false);

                    }

                    $this->manager->persist($situationDetails);
                    $this->manager->flush();

                    /**
                     * Passage en édition
                     */
                    return $this->RedirectToRoute('situation_details_edit', ['id' => $situationDetails->getId()]);
                    
                } else {

                    $message = ['Veuillez sélectionner au moins une compétence !'];
                    $error   = true;

                }
                
            } else {
                
                $errors = $this->get('validator')->validate($situationDetails);
                $error  = true;
                
            }
            
        }
        
        if ( $error ) {

            foreach ( $situationDetails->getPictures() as $picture ) {
                $situationDetails->removePicture($picture);
            }

            $form = $this->createForm('SioBundle\Form\SituationDetailsType', $situationDetails);

        }

        return $this->render('Situationdetails/new.html.twig', array(
            'situation' => $situation,
            'form'      => $form->createView(),
            'errors'    => $errors,
            'message'   => $message
        ));

    }

    
    /**
     * Undocumented function
     * 
     * @Route("/{id}/edit", options={"expose"=true}, name="situation_details_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param SituationDetails $situationDetails
     * @return void
     */
    public function editSituation (Request $request, SituationDetails $situationDetails)
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }

        $errors  = null;
        $message = null;
        $form    = $this->createForm('SioBundle\Form\SituationDetailsType', $situationDetails);
        $form->handleRequest($request);
     
        if ( "POST" === $request->getMethod() ) {
            
            if ( $form->isSubmitted() && $form->isValid() ) {
               
                if ( count($situationDetails->getCompetences()) !== 0 ) {

                    foreach ($situationDetails->getPictures() as $picture) {
                    
                        $picture->setSituationDetails($situationDetails);
                        
                    }
                    
                    if ( $this->isGranted('ROLE_ADMIN') ) {
    
                        $this->addFlash('success', 'Descriptif mis à jour !');
                        $situationDetails->setValidated(true);
    
                    } else {
    
                        $this->addFlash('success', 'Votre descriptif à bien été enregistré. Un administrateur le validera sous 24 heures !');
                        $situationDetails->setValidated(false);
    
                    }
               
                    $this->manager->persist($situationDetails);
                    $this->manager->flush();
    
                } else {

                    $message = ['Veuillez sélectionner au moins une compétence !'];       

                }
                
            } else {
                
                $errors = $this->get('validator')->validate($situationDetails);
                
            }
            
        }

        // Réactualisation de l'objet et du formulaire
        $this->manager->refresh($situationDetails);
        $form = $this->createForm('SioBundle\Form\SituationDetailsType', $situationDetails);
        
        return $this->render('Situationdetails/edit.html.twig', array(
            'form'            => $form->createView(),
            'situationDetail' => $situationDetails,
            'errors'          => $errors,
            'message'         => $message
        ));
        
    }
    
    
    /**
     * Undocumented function
     * 
     * @Route("/{user}/{situation}/show", name="situation_details_show", options={"expose"=true}, methods={"POST", "GET"})
     * @param Situation $situation
     * @param User $user
     * @return void
     */
    public function showSituation (Situation $situation, User $user)
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $userConnect   = $this->get('security.token_storage')->getToken()->getUser();
        $situationList = $this->manager->getRepository(Situation::class)->findBy([
            'user'  =>  $user->getId(),
        ]);
        
        $situationDetailsListe = $this->manager->getRepository(SituationDetails::class)->findBy([
            'situation'    =>  $situation->getId(),
            'validated'    =>  1
        ]);  
         
        foreach( $situationDetailsListe as $situationDetail ) 
        {

            $pictureCollection = $this->manager->getRepository(Picture::class)->findBy([
                'situationDetails' => $situationDetail,
            ]);
            
            foreach ( $pictureCollection as $picture )
            {
                
                $situationDetail->addPicture($picture);
                
            }

        }
         
        if ( $user->getId() === $situation->getUser()->getId() ) {
                
            return $this->render('Templates/userSituationDetails.html.twig', [
                'situationList'         => $situationList,
                'situationDetailsListe' => $situationDetailsListe,
                'situation'             => $situation,
                'user'                  => $user
            ]);
            
        }   
        
    }
    

    /**
     * Undocumented function
     * 
     * @Route("/{id}/delete", options={"expose"=true}, name="situation_details_delete", methods={"POST", "GET"})
     * @param Request $request
     * @param SituationDetails $situationDetail
     * @return void
     */
    public function deleteSituation (SituationDetails $situationDetail)
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $user  = $this->get('security.token_storage')->getToken()->getUser();
        $error = "Ok";

        if ( $situationDetail->getSituation()->getUser()->getId() === $user->getId() ) {
            
            foreach ( $situationDetail->getPictures() as $picture )
            {
                $root = $picture->getUploadRootDir().'\\'.$picture->getFileName();
                
                if ( file_exists($root) ) {
                    unlink($root);
                }
                
            }
            
            try {

                $this->manager->remove($situationDetail);
                $this->manager->flush();

            } catch ( Exception $e ) {

                $error = 'Erreur détectée : '.$e->getMessage();

            }
            
            return $this->json($error, 200);

        }
        
    }
   
    
    /**
     * Undocumented function
     * 
     * @Route("/{id}/delete/picture", name="situation_details_delete_picture", methods={"GET"})
     * @param Picture $picture
     * @return void
     */
    public function deletePicture (Picture $picture) 
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $situationDetail = $picture->getSituationDetails()->getId();
        
        $root = $picture->getUploadRootDir().'\\'.$picture->getFileName();
                
        if ( file_exists($root) ) {
            unlink($root);
        }
                
        $this->manager->remove($picture);
        $this->manager->flush();
        
        
        return $this->redirectToRoute('situation_details_edit', [
            'id' => $situationDetail,
        ]);

    }
    
}
