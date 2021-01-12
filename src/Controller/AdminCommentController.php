<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController{
    /**
     * Affiche l'index de tous les commentaires
     * 
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     */
    public function index($page, PaginationService $pagination): Response
    {

        $pagination -> setEntityClass(Comment::class)
                    -> setCurrentPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }
    
    /**
     * Permet de modifier un commentaire
     *
     * @Route("/admin/comments/{id}/edit", name="admin_comments_edit")
     * @param Comment $comment
     * @return void
     */
    public function edit(Comment $comment, Request $request, EntityManagerInterface $manager){

        $form = $this -> createForm(AdminCommentType::class, $comment);

        $form -> handleRequest($request);

        if( $form-> isSubmitted() && $form -> isValid()) {
            $manager -> persist($comment);
            $manager -> flush();

            $this -> addFlash("success", " le commentaire <strong> N° " . $comment -> getId() . "</strong> de l'annonce : <strong>" . $comment -> getAd() -> getTitle() ."</strong> à bien été enregistré.");

        }

        return $this -> render ('admin/comment/edit.html.twig',[
            'comment' => $comment,
            'form' => $form -> createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     *
     * @Route("/admin/comments/{id}/delete", name="admin_comments_delete")
     * @param Comment $comment
     * @return void
     */
    public function delete(Comment $comment, EntityManagerInterface $manager){
            $manager -> remove($comment);
            $manager -> flush();
            
            $this -> addFlash('success', "Le commentaire de <strong>" . $comment -> getAuthor() -> getFullName() . "</strong> pour l'annonce : <strong>" . $comment->getAd()->getTitle() . "</strong> a bien été supprimé.");

         return $this -> redirectToRoute('admin_comments_index');

    }
}
