<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\LigneCommandeRepository;
use App\Repository\LivreurRepository;
use App\Repository\ProduitRepository;
use App\Repository\ZoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private ProduitRepository $produitRepository,
        private ZoneRepository $zoneRepository,
        private LivreurRepository $livreurRepository,
        private LigneCommandeRepository $ligneCommandeRepository
    ) {
    }

    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        $today = new \DateTime();
        
        return $this->render('dashboard/index.html.twig', [
            'commandesEnCours' => $this->commandeRepository->countEnCours(),
            'commandesLivrees' => $this->commandeRepository->countByEtat('TERMINEE'),
            'commandesAnnulees' => $this->commandeRepository->countByEtat('ANNULEE'),
            'recetteJournaliere' => $this->commandeRepository->getRecetteJournaliere($today),
            'topBurgers' => [],
            'commandesRecentes' => $this->commandeRepository->findRecent(10),
        ]);
    }
}
