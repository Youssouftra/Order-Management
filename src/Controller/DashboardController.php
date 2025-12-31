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
        try {
            $today = new \DateTime();
            $commandesEnCours = $this->commandeRepository->countEnCours();
            $commandesLivrees = $this->commandeRepository->countByEtat('TERMINEE');
            $commandesAnnulees = $this->commandeRepository->countByEtat('ANNULEE');
            $recetteJournaliere = $this->commandeRepository->getRecetteJournaliere($today);
            $commandesRecentes = $this->commandeRepository->findRecent(10);
            
            return $this->render('dashboard/index.html.twig', [
                'commandesEnCours' => $commandesEnCours,
                'commandesLivrees' => $commandesLivrees,
                'commandesAnnulees' => $commandesAnnulees,
                'recetteJournaliere' => $recetteJournaliere,
                'topBurgers' => [],
                'commandesRecentes' => $commandesRecentes,
            ]);
        } catch (\Exception $e) {
            return $this->render('dashboard/index.html.twig', [
                'commandesEnCours' => 0,
                'commandesLivrees' => 0,
                'commandesAnnulees' => 0,
                'recetteJournaliere' => '0.00',
                'topBurgers' => [],
                'commandesRecentes' => [],
                'error' => $e->getMessage(),
            ]);
        }
    }
}
