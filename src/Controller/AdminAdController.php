<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index($page, PaginationService $pagination): Response{

        $pagination -> setEntityClass(Ad::class)
                    -> setCurrentPage($page);
        

        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Affiche le formaulaire d'edition
     *
     * @Route("/admin/ads/{id}/edit", name ="admin_ads_edit")
     * @param Ad $ad
     * @return Response
     */
    public function edit(Request $request, EntityManagerInterface $manager ,Ad $ad){
          
        $form = $this -> createForm(AdType::class, $ad);
        
        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()){
            $manager -> persist($ad);
            $manager -> flush();

            $this -> addFlash('success'," L'annonce <strong>". $ad -> getTitle() ." </strong> a bien été modifiée." );
        }


        return $this -> render('admin/ad/edit.html.twig', [
            'ad' => $ad, 
            'form' => $form -> createView()
        ]);
    }

     /**
      * Supprime une annonce
      *
      *@Route("/admin/ads/{id}/delete", name="admin_ads_delete")
      * @param Ad $as
      * @param EntityManagerInterface $manager
      * @return void
      */
    public function delete(Ad $ad , EntityManagerInterface $manager){
        if( count($ad -> getBookings()) >0) {
            $this -> addFlash('warning', "Vous ne pouvez pas supprimer <strong>" . $ad -> getTitle() ." </strong> car elle possède des résevations.");
        }
        else {
            $manager -> remove($ad);
            $manager -> flush();

            $this ->addFlash('success', "L'annonce <strong>" . $ad -> getTitle() . "</strong> à été suprimée.");
        }

        return $this-> redirectToRoute('admin_ads_index');
    }   
}
