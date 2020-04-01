<?php

namespace SioBundle\Controller;

use SioBundle\Entity\Situation;
use AdminBundle\Entity\Referentiel;
use SioBundle\Repository\ReferentielRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;


/**
 * Situation controller.
 *
 * @Route("situation")
 */
class SituationController extends Controller
{
    
    /**
     * Undocumented variable
     *
     * @var ObjectManager
     */
    private $manager;
    
    /**
     * Undocumented function
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct (EntityManagerInterface $manager) 
    {

        $this->manager = $manager;

    }
    

    /**
     * Undocumented function
     * 
     * @Route("/new", name="situation_new", methods={"GET", "POST"})
     * @Route("/{id}/edit", name="situation_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Situation $situation
     * @return void
     */
    public function form (Request $request, Situation $situation = Null)
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        if(!$situation) {
            
            $situation = new Situation;
            $mod  = ["modStr" => "Enregistrer",
                    "textStr" => "Création d'une mission"];
        } else {
            
            $mod  = ["modStr" => "Modifier",
                    "textStr" => "Modification de la mission"];
        }
        
        $form = $this->createForm('SioBundle\Form\SituationType', $situation);
        $form->handleRequest($request);
        
        if ("POST" === $request->getMethod()) {
            
            if($form->isSubmitted() && $form->isValid()) {
                
                if ( count($situation->getReferentiels()) === 0) {

                    return $this->render('Situation/edit.html.twig', array(
                        'situationForm' => $form->createView(),
                        'mod'       => $mod,
                        'message'   => ['Veuillez sélectionner au moins une situation obligatoire !']
                    )); 

                }

                $user = $this->get('security.token_storage')->getToken()->getUser();
                $situation->setUser($user);
                $this->manager->persist($situation);
                $this->manager->flush();

                return $this->redirectToRoute('situation_liste');

            } 

            $errors = $this->get('validator')->validate($situation);

            return $this->render('Situation/edit.html.twig', array(
                'situationForm' => $form->createView(),
                'mod'      => $mod,
                'errors'   => $errors
            ));  
            
        } else {

            return $this->render('Situation/edit.html.twig', array(
                'situationForm' => $form->createView(),
                'mod'      => $mod,
            )); 
        }
        
    }
    

    /**
     * Undocumented function
     * 
     * @Route("/{id}/supprimer", name="situation_suppression", methods={"GET"})
     * @param Situation $situation
     * @return void
     */
    public function delete (Situation $situation)
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        if ( $user->getId() === $situation->getUser()->getId() ) {
            
            $this->manager->remove($situation);
            $this->manager->flush();
   
        }
        
        return $this->redirectToRoute('situation_liste');
        
    }
    
    
    /**
     * Undocumented function
     * 
     * @Route("/liste", name="situation_liste", methods={"GET"})
     * @return void
     */
    public function liste ()
    {
        
        if ( !$this->isGranted('ROLE_USER') || !$this->isGranted('IS_AUTHENTICATED_FULLY') ) {
            throw $this->createAccessDeniedException('Veuillez vous connecter !');
        }
        
        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        
        $situations = $this->manager->getRepository(Situation::class)->findBy([
            'user'  =>  $user,
        ]);

        return $this->render('Situation/show.html.twig', array(
                'situations' => $situations,         
        ));  

    }
    
}
