<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Entity\District;
use App\Form\ZoneType;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/zones')]
class ZoneController extends AbstractController
{
    public function __construct(
        private ZoneRepository $zoneRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('', name: 'app_zone_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search');
        $status = $request->query->get('status');
        $limit = 100;
        
        $isActiveFilter = null;
        if ($status === '1') $isActiveFilter = true;
        if ($status === '0') $isActiveFilter = false;

        $paginator = $this->zoneRepository->findPaginated($page, $limit, $search, $isActiveFilter);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $limit);

        return $this->render('zone/index.html.twig', [
            'zones' => $paginator,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
            ],
        ]);
    }

    #[Route('/new', name: 'app_zone_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $data = $request->request->all('zone');
        
        if (!$this->isCsrfTokenValid('zone', $data['_token'] ?? '')) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_zone_index');
        }

        $zone = new Zone();
        $zone->setNom($data['nom'] ?? '');
        $zone->setFraisLivraison((int) ($data['fraisLivraison'] ?? 0));
        $zone->setIsActive(true);
        
        $this->entityManager->persist($zone);
        
        // Handle districts
        if (!empty($data['quartiers'])) {
            $quartiers = array_map('trim', explode(',', $data['quartiers']));
            foreach ($quartiers as $quartierNom) {
                if (!empty($quartierNom)) {
                    $district = new District();
                    $district->setNom($quartierNom);
                    $district->setZone($zone);
                    $this->entityManager->persist($district);
                }
            }
        }
        
        $this->entityManager->flush();

        $this->addFlash('success', 'La zone a été créée avec succès.');
        return $this->redirectToRoute('app_zone_index');
    }

    #[Route('/{id}/edit', name: 'app_zone_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Zone $zone): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all('zone');
            
            if (!$this->isCsrfTokenValid('zone' . $zone->getId(), $data['_token'] ?? '')) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_zone_index');
            }

            $zone->setNom($data['nom'] ?? $zone->getNom());
            $zone->setFraisLivraison((int) ($data['fraisLivraison'] ?? $zone->getFraisLivraison()));
            $zone->setUpdatedAt(new \DateTimeImmutable());
            
            // Remove existing districts
            foreach ($zone->getDistricts() as $district) {
                $this->entityManager->remove($district);
            }
            
            // Add new districts
            if (!empty($data['quartiers'])) {
                $quartiers = array_map('trim', explode(',', $data['quartiers']));
                foreach ($quartiers as $quartierNom) {
                    if (!empty($quartierNom)) {
                        $district = new District();
                        $district->setNom($quartierNom);
                        $district->setZone($zone);
                        $this->entityManager->persist($district);
                    }
                }
            }
            
            $this->entityManager->flush();

            $this->addFlash('success', 'La zone a été modifiée avec succès.');
            return $this->redirectToRoute('app_zone_index');
        }

        $form = $this->createForm(ZoneType::class, $zone);

        return $this->render('zone/form.html.twig', [
            'form' => $form,
            'zone' => $zone,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_zone_delete', methods: ['POST'])]
    public function delete(Request $request, Zone $zone): Response
    {
        if ($this->isCsrfTokenValid('delete' . $zone->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($zone);
            $this->entityManager->flush();

            $this->addFlash('success', 'La zone a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_zone_index');
    }

    #[Route('/{id}/toggle', name: 'app_zone_toggle', methods: ['POST'])]
    public function toggle(Request $request, Zone $zone): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $zone->getId(), $request->request->get('_token'))) {
            $zone->setIsActive(!$zone->isActive());
            $zone->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $status = $zone->isActive() ? 'activée' : 'désactivée';
            $this->addFlash('success', "La zone a été {$status}.");
        }

        return $this->redirectToRoute('app_zone_index');
    }
}
