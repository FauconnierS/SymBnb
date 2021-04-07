<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users/{page<\d+>?1}", name="admin_users_index")
     */
    public function index($page, PaginationService $pagination): Response
    {
        $pagination->setEntityClass(User::class)
            ->setCurrentPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}
