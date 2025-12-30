<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\BurgerRepository;
use App\Repository\CommandeRepository;
use App\Repository\ComplementRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\LivreurRepository;
use App\Repository\MenuRepository;
use App\Repository\ZoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private BurgerRepository $burgerRepository,
        private ComplementRepository $complementRepository,
        private MenuRepository $menuRepository,
        private ZoneRepository $zoneRepository,
        private LivreurRepository $livreurRepository,
        private LigneCommandeRepository $ligneCommandeRepository
    ) {
    }

    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'commandesEnCours' => 0,
            'commandesLivrees' => 0,
            'commandesAnnulees' => 0,
            'recetteJournaliere' => 0,
            'topBurgers' => [],
            'commandesRecentes' => [],
        ]);
    }
}
