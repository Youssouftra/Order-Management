<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produits')]
class ProduitController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepository,
        private EntityManagerInterface $em
    ) {}

    #[Route('/burgers', name: 'app_burger_index')]
    public function burgers(): Response
    {
        return $this->render('burger/index.html.twig', [
            'burgers' => $this->produitRepository->findBurgers(),
            'pagination' => ['currentPage' => 1, 'totalPages' => 1, 'totalItems' => 0],
        ]);
    }

    #[Route('/complements', name: 'app_complement_index')]
    public function complements(): Response
    {
        return $this->render('complement/index.html.twig', [
            'complements' => $this->produitRepository->findComplements(),
            'pagination' => ['currentPage' => 1, 'totalPages' => 1, 'totalItems' => 0],
        ]);
    }

    #[Route('/menus', name: 'app_menu_index')]
    public function menus(): Response
    {
        return $this->render('menu/index.html.twig', [
            'menus' => $this->produitRepository->findMenus(),
            'pagination' => ['currentPage' => 1, 'totalPages' => 1, 'totalItems' => 0],
        ]);
    }
}
