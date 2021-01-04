<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo ){
        
        $ads = $repo-> findAll(); 
    
        return $this->render('ad/index.html.twig',  
        [
            'ads' => $ads
        ]);
    }


    /**
     * Permet de créer une annonce
     *
     * @route("/ads/new", name="ads_create")
     * @IsGranted("ROLE_USER")
     * 
     * @return response
     */
    public function create(Request $request, EntityManagerInterface $manager){
        
        $ad = new Ad();

        $form = $this -> createForm(AdType::class, $ad);

        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){

            foreach($ad -> getImages() as $image){
                $image -> setAd($ad);
                $manager -> persist($image);
            }
            
            $ad -> setAuthor($this -> getUser());
            $manager -> persist($ad);
            $manager -> flush();

            $this -> addFlash('success', "Votre annonce : <strong>{$ad -> getTitle()}</strong> a bien été enregistrée.");

            return $this -> redirectToRoute('ads_show', [
                'slug' => $ad -> getSlug()
            ]);
        }

        return $this -> render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Modification annonce
     *@Route("ads/{slug}/edit", name="ads_edit")
     *@Security("is_granted('ROLE_USER') and user === ad.getAuthor()",
     *          message = "Vous n'avez pas le droit de modifier cette annonce")
     * 
     * @return void
     */

    public function edit(Ad $ad, EntityManagerInterface $manager , Request $request) {

        $form = $this -> createForm(AdType::class, $ad);

        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){

            foreach($ad -> getImages() as $image){
                $image -> setAd($ad);
                $manager -> persist($image);
            }

            $manager -> persist($ad);
            $manager -> flush();

            $this -> addFlash('success', "Votre annonce : <strong>{$ad -> getTitle()}</strong> a bien été enregistrée.");

            return $this -> redirectToRoute('ads_show', [
                'slug' => $ad -> getSlug()
            ]);
        }

        return $this -> render('ad/edit.html.twig', [
            'form' => $form -> createView(),
            'ad' => $ad
        ]);
    }


    /**
     * 
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad){
        return $this -> render('ad/show.html.twig', [
            'ad' => $ad
        ]);

    }

    /**
     * Supprimer l'annonce
     * 
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()",
     *          message = "Vous n'avez pas le droit de supprimer cette annonce !")
     *
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function delete(Ad $ad , EntityManagerInterface $manager){
        $manager -> remove($ad);
        $manager -> flush();

        $this -> addFlash('success', 'Votre annonce à bien été supprimé ');

        return $this -> redirectToRoute("ads_index");
    }

}
 