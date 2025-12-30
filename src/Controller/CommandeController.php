<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Livreur;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\BurgerRepository;
use App\Repository\ComplementRepository;
use App\Repository\MenuRepository;
use App\Repository\LivreurRepository;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commandes')]
class CommandeController extends AbstractController
{
    public function __construct(
        private CommandeRepository $commandeRepository,
        private BurgerRepository $burgerRepository,
        private ComplementRepository $complementRepository,
        private MenuRepository $menuRepository,
        private LivreurRepository $livreurRepository,
        private ZoneRepository $zoneRepository,
        private EntityManagerInterface $em
    ) {}

    #[Route('', name: 'app_commande_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $commandes = [];
        $stats = [
            'en_attente' => 0,
            'en_preparation' => 0,
            'prete' => 0,
            'en_livraison' => 0,
            'livree' => 0,
            'annulee' => 0,
        ];
        
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
            'pagination' => [
                'currentPage' => 1,
                'totalPages' => 1,
                'totalItems' => 0,
            ],
            'stats' => $stats,
            'etats' => Commande::ETATS,
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        
        $burgers = $this->burgerRepository->findAllActive();
        $complements = $this->complementRepository->findAllActive();
        $menus = $this->menuRepository->findAllActive();
        $zones = $this->zoneRepository->findAllActive();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $montantTotal = 0;
            
            // Traiter les burgers
            $burgersData = $request->request->all('burgers') ?? [];
            foreach ($burgersData as $burgerId => $data) {
                if (!empty($data['selected'])) {
                    $burger = $this->burgerRepository->find($burgerId);
                    if ($burger) {
                        $ligne = new LigneCommande();
                        $ligne->setBurger($burger);
                        $ligne->setQuantite((int)($data['qty'] ?? 1));
                        $ligne->setPrixUnitaire($burger->getPrix());
                        $ligne->calculerSousTotal();
                        $montantTotal += $ligne->getSousTotal();
                        $commande->addLigneCommande($ligne);
                    }
                }
            }
            
            // Traiter les compléments
            $complementsData = $request->request->all('complements') ?? [];
            foreach ($complementsData as $complementId => $data) {
                if (!empty($data['selected'])) {
                    $complement = $this->complementRepository->find($complementId);
                    if ($complement) {
                        $ligne = new LigneCommande();
                        $ligne->setComplement($complement);
                        $ligne->setQuantite((int)($data['qty'] ?? 1));
                        $ligne->setPrixUnitaire($complement->getPrix());
                        $ligne->calculerSousTotal();
                        $montantTotal += $ligne->getSousTotal();
                        $commande->addLigneCommande($ligne);
                    }
                }
            }
            
            // Traiter les menus
            $menusData = $request->request->all('menus') ?? [];
            foreach ($menusData as $menuId => $data) {
                if (!empty($data['selected'])) {
                    $menu = $this->menuRepository->find($menuId);
                    if ($menu) {
                        $ligne = new LigneCommande();
                        $ligne->setMenu($menu);
                        $ligne->setQuantite((int)($data['qty'] ?? 1));
                        $ligne->setPrixUnitaire($menu->getPrix());
                        $ligne->calculerSousTotal();
                        $montantTotal += $ligne->getSousTotal();
                        $commande->addLigneCommande($ligne);
                    }
                }
            }
            
            // Frais de livraison
            if ($commande->getZone()) {
                $commande->setFraisLivraison($commande->getZone()->getFraisLivraison());
                $montantTotal += $commande->getFraisLivraison();
            }
            
            $commande->setMontantTotal($montantTotal);
            $commande->setEtat(Commande::ETAT_EN_ATTENTE);
            
            $this->em->persist($commande);
            $this->em->flush();
            
            $this->addFlash('success', 'Commande #' . $commande->getNumero() . ' créée avec succès!');
            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }
        
        return $this->render('commande/form.html.twig', [
            'form' => $form,
            'commande' => $commande,
            'burgers' => $burgers,
            'complements' => $complements,
            'menus' => $menus,
            'zones' => $zones,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Request $request, Commande $commande): Response
    {
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);
        
        $burgers = $this->burgerRepository->findAllActive();
        $complements = $this->complementRepository->findAllActive();
        $menus = $this->menuRepository->findAllActive();
        $zones = $this->zoneRepository->findAllActive();
        
        if ($form->isSubmitted() && $form->isValid()) {
            $commande->setUpdatedAt(new \DateTimeImmutable());
            $this->em->flush();
            
            $this->addFlash('success', 'Commande modifiée avec succès.');
            return $this->redirectToRoute('app_commande_show', ['id' => $commande->getId()]);
        }
        
        return $this->render('commande/form.html.twig', [
            'form' => $form,
            'commande' => $commande,
            'burgers' => $burgers,
            'complements' => $complements,
            'menus' => $menus,
            'zones' => $zones,
        ]);
    }

    #[Route('/{id}/change-etat', name: 'app_commande_change_etat', methods: ['POST'])]
    public function changeEtat(Request $request, Commande $commande): Response
    {
        if (!$this->isCsrfTokenValid('etat' . $commande->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_commande_index');
        }
        
        $nouvelEtat = $request->request->get('etat');
        
        if (!array_key_exists($nouvelEtat, Commande::ETATS)) {
            $this->addFlash('error', 'État invalide');
            return $this->redirectToRoute('app_commande_index');
        }
        
        $commande->setEtat($nouvelEtat);
        
        // Gestion du statut du livreur
        if ($nouvelEtat === Commande::ETAT_EN_LIVRAISON && $commande->getLivreur()) {
            $commande->getLivreur()->setStatut(Livreur::STATUT_EN_LIVRAISON);
        }
        
        if (in_array($nouvelEtat, [Commande::ETAT_LIVREE, Commande::ETAT_ANNULEE]) && $commande->getLivreur()) {
            $commande->getLivreur()->setStatut(Livreur::STATUT_DISPONIBLE);
        }
        
        $commande->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
        
        $this->addFlash('success', 'État de la commande mis à jour.');
        return $this->redirectToRoute('app_commande_index');
    }

    #[Route('/{id}/assign-livreur', name: 'app_commande_assign_livreur', methods: ['GET'])]
    public function assignLivreurForm(Commande $commande): Response
    {
        $livreurs = $this->livreurRepository->findDisponibles();
        
        return $this->render('commande/assign_livreur.html.twig', [
            'commande' => $commande,
            'livreurs' => $livreurs,
        ]);
    }

    #[Route('/{id}/do-assign-livreur', name: 'app_commande_do_assign_livreur', methods: ['POST'])]
    public function doAssignLivreur(Request $request, Commande $commande): Response
    {
        if (!$this->isCsrfTokenValid('assign' . $commande->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide');
            return $this->redirectToRoute('app_commande_index');
        }
        
        $livreurId = $request->request->get('livreur_id');
        
        if ($livreurId) {
            $livreur = $this->livreurRepository->find($livreurId);
            if ($livreur) {
                // Libérer l'ancien livreur
                if ($commande->getLivreur()) {
                    $commande->getLivreur()->setStatut(Livreur::STATUT_DISPONIBLE);
                }
                
                $commande->setLivreur($livreur);
                $commande->setEtat(Commande::ETAT_EN_LIVRAISON);
                $livreur->setStatut(Livreur::STATUT_EN_LIVRAISON);
                
                $this->addFlash('success', 'Livreur ' . $livreur->getPrenom() . ' assigné à la commande.');
            }
        }
        
        $commande->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();
        
        return $this->redirectToRoute('app_commande_index');
    }

    #[Route('/{id}/delete', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande): Response
    {
        if ($this->isCsrfTokenValid('delete' . $commande->getId(), $request->request->get('_token'))) {
            if ($commande->getLivreur()) {
                $commande->getLivreur()->setStatut(Livreur::STATUT_DISPONIBLE);
            }
            
            $this->em->remove($commande);
            $this->em->flush();
            
            $this->addFlash('success', 'Commande supprimée.');
        }
        
        return $this->redirectToRoute('app_commande_index');
    }
}
