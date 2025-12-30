<?php

namespace App\Controller;

use App\Entity\Livreur;
use App\Form\LivreurType;
use App\Repository\LivreurRepository;
use App\Service\Impl\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livreurs')]
class LivreurController extends AbstractController
{
    public function __construct(
        private LivreurRepository $livreurRepository,
        private EntityManagerInterface $entityManager,
        private CloudinaryService $cloudinaryService
    ) {
    }

    #[Route('', name: 'app_livreur_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search');
        $statut = $request->query->get('statut');
        $status = $request->query->get('status');
        $limit = 100;
        
        $isActiveFilter = null;
        if ($status === '1') $isActiveFilter = true;
        if ($status === '0') $isActiveFilter = false;

        $paginator = $this->livreurRepository->findPaginated($page, $limit, $search, $statut, $isActiveFilter);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $limit);

        return $this->render('livreur/index.html.twig', [
            'livreurs' => $paginator,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
            ],
            'statuts' => Livreur::STATUTS,
        ]);
    }

    #[Route('/new', name: 'app_livreur_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        // Handle modal form submission (simple HTML form)
        if ($request->isMethod('POST') && !$request->request->has('livreur_form')) {
            $token = $request->request->get('_token');
            
            if (!$this->isCsrfTokenValid('livreur_new', $token)) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_livreur_index');
            }

            $livreur = new Livreur();
            $livreur->setPrenom($request->request->get('prenom', ''));
            $livreur->setNom($request->request->get('nom', ''));
            $livreur->setTelephone($request->request->get('telephone', ''));
            $livreur->setStatut('disponible');
            $livreur->setIsActive(true);
            
            $this->entityManager->persist($livreur);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le livreur a été créé avec succès.');
            return $this->redirectToRoute('app_livreur_index');
        }

        // Handle Symfony form
        $livreur = new Livreur();
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $imageUrl = $this->cloudinaryService->upload($photoFile, 'brasil-burger/livreurs');
                if ($imageUrl) {
                    $livreur->setPhoto($imageUrl);
                }
            }

            $this->entityManager->persist($livreur);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le livreur a été créé avec succès.');
            return $this->redirectToRoute('app_livreur_index');
        }

        return $this->render('livreur/form.html.twig', [
            'form' => $form,
            'livreur' => $livreur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livreur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livreur $livreur): Response
    {
        // Handle modal form submission (simple HTML form)
        if ($request->isMethod('POST') && !$request->request->has('livreur_form')) {
            $token = $request->request->get('_token');
            
            if (!$this->isCsrfTokenValid('livreur_edit' . $livreur->getId(), $token)) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_livreur_index');
            }

            $livreur->setPrenom($request->request->get('prenom', $livreur->getPrenom()));
            $livreur->setNom($request->request->get('nom', $livreur->getNom()));
            $livreur->setTelephone($request->request->get('telephone', $livreur->getTelephone()));
            $livreur->setUpdatedAt(new \DateTimeImmutable());
            
            $this->entityManager->flush();

            $this->addFlash('success', 'Le livreur a été modifié avec succès.');
            return $this->redirectToRoute('app_livreur_index');
        }

        // Handle Symfony form
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                if ($livreur->getPhoto()) {
                    $publicId = $this->cloudinaryService->getPublicIdFromUrl($livreur->getPhoto());
                    if ($publicId) {
                        $this->cloudinaryService->delete($publicId);
                    }
                }
                
                $imageUrl = $this->cloudinaryService->upload($photoFile, 'brasil-burger/livreurs');
                if ($imageUrl) {
                    $livreur->setPhoto($imageUrl);
                }
            }

            $livreur->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->addFlash('success', 'Le livreur a été modifié avec succès.');
            return $this->redirectToRoute('app_livreur_index');
        }

        return $this->render('livreur/form.html.twig', [
            'form' => $form,
            'livreur' => $livreur,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_livreur_delete', methods: ['POST'])]
    public function delete(Request $request, Livreur $livreur): Response
    {
        if ($this->isCsrfTokenValid('delete' . $livreur->getId(), $request->request->get('_token'))) {
            if ($livreur->getPhoto()) {
                $publicId = $this->cloudinaryService->getPublicIdFromUrl($livreur->getPhoto());
                if ($publicId) {
                    $this->cloudinaryService->delete($publicId);
                }
            }

            $this->entityManager->remove($livreur);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le livreur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_livreur_index');
    }

    #[Route('/{id}/toggle', name: 'app_livreur_toggle', methods: ['POST'])]
    public function toggle(Request $request, Livreur $livreur): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $livreur->getId(), $request->request->get('_token'))) {
            $livreur->setIsActive(!$livreur->isActive());
            $livreur->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $status = $livreur->isActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Le livreur a été {$status}.");
        }

        return $this->redirectToRoute('app_livreur_index');
    }

    #[Route('/{id}/change-statut', name: 'app_livreur_change_statut', methods: ['POST'])]
    public function changeStatut(Request $request, Livreur $livreur): Response
    {
        if ($this->isCsrfTokenValid('statut' . $livreur->getId(), $request->request->get('_token'))) {
            $newStatut = $request->request->get('statut');
            if (array_key_exists($newStatut, Livreur::STATUTS)) {
                $livreur->setStatut($newStatut);
                $livreur->setUpdatedAt(new \DateTimeImmutable());
                $this->entityManager->flush();

                $this->addFlash('success', "Le statut du livreur a été mis à jour.");
            }
        }

        return $this->redirectToRoute('app_livreur_index');
    }
}
