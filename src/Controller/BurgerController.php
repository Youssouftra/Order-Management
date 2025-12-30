<?php

namespace App\Controller;

use App\Entity\Burger;
use App\Form\BurgerType;
use App\Repository\BurgerRepository;
use App\Service\Impl\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/burgers')]
class BurgerController extends AbstractController
{
    public function __construct(
        private BurgerRepository $burgerRepository,
        private EntityManagerInterface $entityManager,
        private CloudinaryService $cloudinaryService
    ) {
    }

    #[Route('', name: 'app_burger_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search');
        $status = $request->query->get('status');
        $limit = 100;
        
        $isActiveFilter = null;
        if ($status === '1') $isActiveFilter = true;
        if ($status === '0') $isActiveFilter = false;

        $paginator = $this->burgerRepository->findPaginated($page, $limit, $search, $isActiveFilter);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $limit);

        return $this->render('burger/index.html.twig', [
            'burgers' => $paginator,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
            ],
        ]);
    }

    #[Route('/new', name: 'app_burger_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        // Handle modal form submission
        if ($request->isMethod('POST') && $request->request->has('burger')) {
            $data = $request->request->all('burger');
            
            if (!$this->isCsrfTokenValid('burger', $data['_token'] ?? '')) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_burger_index');
            }

            $burger = new Burger();
            $burger->setNom($data['nom'] ?? '');
            $burger->setDescription($data['description'] ?? '');
            $burger->setPrix((int) ($data['prix'] ?? 0));
            $burger->setImage($data['image'] ?? null);
            $burger->setIsActive(true);
            
            $this->entityManager->persist($burger);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le burger a été créé avec succès.');
            return $this->redirectToRoute('app_burger_index');
        }

        // Handle Symfony form
        $burger = new Burger();
        $form = $this->createForm(BurgerType::class, $burger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageUrl = $this->cloudinaryService->upload($imageFile, 'brasil-burger/burgers');
                if ($imageUrl) {
                    $burger->setImage($imageUrl);
                }
            }

            $this->entityManager->persist($burger);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le burger a été créé avec succès.');
            return $this->redirectToRoute('app_burger_index');
        }

        return $this->render('burger/form.html.twig', [
            'form' => $form,
            'burger' => $burger,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_burger_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Burger $burger): Response
    {
        $form = $this->createForm(BurgerType::class, $burger);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                if ($burger->getImage()) {
                    $publicId = $this->cloudinaryService->getPublicIdFromUrl($burger->getImage());
                    if ($publicId) {
                        $this->cloudinaryService->delete($publicId);
                    }
                }
                
                $imageUrl = $this->cloudinaryService->upload($imageFile, 'brasil-burger/burgers');
                if ($imageUrl) {
                    $burger->setImage($imageUrl);
                }
            }

            $burger->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->addFlash('success', 'Le burger a été modifié avec succès.');
            return $this->redirectToRoute('app_burger_index');
        }

        return $this->render('burger/form.html.twig', [
            'form' => $form,
            'burger' => $burger,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_burger_delete', methods: ['POST'])]
    public function delete(Request $request, Burger $burger): Response
    {
        if ($this->isCsrfTokenValid('delete' . $burger->getId(), $request->request->get('_token'))) {
            if ($burger->getImage()) {
                $publicId = $this->cloudinaryService->getPublicIdFromUrl($burger->getImage());
                if ($publicId) {
                    $this->cloudinaryService->delete($publicId);
                }
            }

            $this->entityManager->remove($burger);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le burger a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_burger_index');
    }

    #[Route('/{id}/toggle', name: 'app_burger_toggle', methods: ['POST'])]
    public function toggle(Request $request, Burger $burger): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $burger->getId(), $request->request->get('_token'))) {
            $burger->setIsActive(!$burger->isActive());
            $burger->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $status = $burger->isActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Le burger a été {$status}.");
        }

        return $this->redirectToRoute('app_burger_index');
    }
}
