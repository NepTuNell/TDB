<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Referentiel;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Referentiel controller.
 *
 * @Route("admin/referentiel")
 */
class ReferentielController extends Controller
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
     * @param ObjectManager $manager
     */
    public function __construct (ObjectManager $manager) 
    {

        $this->manager = $manager;

    }

    /**
     * Lists all referentiel entities.
     *
     * @Route("/", name="admin_referentiel_list")
     * @Method("GET")
     */
    public function list()
    {

        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }
        
        $em = $this->getDoctrine()->getManager();

        $referentiels = $em->getRepository('AdminBundle:Referentiel')->findAll();

        return $this->render('Admin/Referentiels/show.html.twig', array(
            'referentiels' => $referentiels,
        ));
    }

    /**
     * Creates a new referentiel entity.
     *
     * @Route("/edit", name="admin_referentiel_edit", methods={"GET", "POST"})
     * @Route("/edit/{referentiel}", name="admin_referentiel_edit", methods={"GET", "POST"})
     * 
     */
    public function new(Request $request, Referentiel $referentiel = null)
    {

        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }

        if(!$referentiel) {
            
            $referentiel = new Referentiel;
            $mod  = ["modStr" => "Enregistrer",
                    "textStr" => "Création d'une compétence obligatoire"];
        } else {
            
            $mod  = ["modStr" => "Modifier",
                    "textStr" => "Modification d'une compétence obligatoire"];
        }

        $form = $this->createForm('AdminBundle\Form\ReferentielType', $referentiel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($referentiel);
            $em->flush();

            return $this->redirectToRoute('admin_referentiel_list'); 

        }

        return $this->render('Admin/Referentiels/edit.html.twig', [
            'referentiel' => $referentiel,
            'mod'  => $mod,
            'form' => $form->createView(),
        ]);

    }

    /**
     * Undocumented function
     * 
     * @Route("/delete/{referentiel}", name="admin_referentiel_delete", methods={"GET"})
     * 
     * @param Referentiel $referentiel
     * @return void
     */
    public function delete(Referentiel $referentiel)
    {

        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }

        $this->manager->remove($referentiel);
        $this->manager->flush();

        return $this->redirectToRoute('admin_referentiel_list');

    }



}
