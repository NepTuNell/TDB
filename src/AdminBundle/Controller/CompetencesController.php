<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\Referentiel;
use AdminBundle\Entity\Competences;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Competences controller.
 *
 * @Route("admin/competences")
 */
class CompetencesController extends Controller
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
     * Lists all competences entities.
     *
     * @Route("/", name="admin_competences_list", methods={"GET"})
     */
    public function list()
    {

        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }
        
        $em = $this->getDoctrine()->getManager();

        $competences = $em->getRepository('AdminBundle:Competences')->findAll();

        return $this->render('Admin/Competences/show.html.twig', array(
            'competences' => $competences,
        ));

    }

    /**
     * Creates a new competence entity.
     *
     * @Route("/edit", name="admin_competence_edit", methods={"GET", "POST"})
     * @Route("/edit/{competence}", name="admin_competence_edit", methods={"GET", "POST"})
     * 
     */
    public function new(Request $request, Competences $competence = null)
    {

        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }

        if(!$competence) {
            
            $competence = new Competences;
            $mod  = ["modStr" => "Enregistrer",
                    "textStr" => "Création d'une compétence"];
        } else {
            
            $mod  = ["modStr" => "Modifier",
                    "textStr" => "Modification de la compétence"];
        }

        $form = $this->createForm('AdminBundle\Form\CompetencesType', $competence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($competence);
            $em->flush();

            return $this->redirectToRoute('admin_competences_list'); 

        }

        return $this->render('Admin/Competences/edit.html.twig', array(
            'competence' => $competence,
            'mod'  => $mod,
            'form' => $form->createView(),
        ));

    }

    /**
     * Undocumented function
     * 
     * @Route("/delete/{competence}", name="admin_competence_delete", methods={"GET"})
     * 
     * @param Referentiel $referentiel
     * @return void
     */
    public function delete(Competences $competence)
    {

        if ( !$this->isGranted('ROLE_ADMIN') ) {
            throw $this->createAccessDeniedException('Vous essayer d\'accéder à des ressources protégées !');
        }

        $this->manager->remove($competence);
        $this->manager->flush();

        return $this->redirectToRoute('admin_competences_list');

    }



}
