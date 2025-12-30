<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use App\Service\Impl\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/menus')]
class MenuController extends AbstractController
{
    public function __construct(
        private MenuRepository $menuRepository,
        private EntityManagerInterface $entityManager,
        private CloudinaryService $cloudinaryService
    ) {
    }

    #[Route('', name: 'app_menu_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search');
        $status = $request->query->get('status');
        $limit = 100;
        
        $isActiveFilter = null;
        if ($status === '1') $isActiveFilter = true;
        if ($status === '0') $isActiveFilter = false;

        $paginator = $this->menuRepository->findPaginated($page, $limit, $search, $isActiveFilter);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $limit);

        return $this->render('menu/index.html.twig', [
            'menus' => $paginator,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
            ],
        ]);
    }

    #[Route('/new', name: 'app_menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageUrl = $this->cloudinaryService->upload($imageFile, 'brasil-burger/menus');
                if ($imageUrl) {
                    $menu->setImage($imageUrl);
                }
            }

            $this->entityManager->persist($menu);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le menu a été créé avec succès.');
            return $this->redirectToRoute('app_menu_index');
        }

        return $this->render('menu/form.html.twig', [
            'form' => $form,
            'menu' => $menu,
        ]);
    }

    #[Route('/{id}', name: 'app_menu_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        return $this->render('menu/show.html.twig', [
            'menu' => $menu,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                if ($menu->getImage()) {
                    $publicId = $this->cloudinaryService->getPublicIdFromUrl($menu->getImage());
                    if ($publicId) {
                        $this->cloudinaryService->delete($publicId);
                    }
                }
                
                $imageUrl = $this->cloudinaryService->upload($imageFile, 'brasil-burger/menus');
                if ($imageUrl) {
                    $menu->setImage($imageUrl);
                }
            }

            $menu->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->addFlash('success', 'Le menu a été modifié avec succès.');
            return $this->redirectToRoute('app_menu_index');
        }

        return $this->render('menu/form.html.twig', [
            'form' => $form,
            'menu' => $menu,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_menu_delete', methods: ['POST'])]
    public function delete(Request $request, Menu $menu): Response
    {
        if ($this->isCsrfTokenValid('delete' . $menu->getId(), $request->request->get('_token'))) {
            if ($menu->getImage()) {
                $publicId = $this->cloudinaryService->getPublicIdFromUrl($menu->getImage());
                if ($publicId) {
                    $this->cloudinaryService->delete($publicId);
                }
            }

            $this->entityManager->remove($menu);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le menu a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_menu_index');
    }

    #[Route('/{id}/toggle', name: 'app_menu_toggle', methods: ['POST'])]
    public function toggle(Request $request, Menu $menu): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $menu->getId(), $request->request->get('_token'))) {
            $menu->setIsActive(!$menu->isActive());
            $menu->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $status = $menu->isActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Le menu a été {$status}.");
        }

        return $this->redirectToRoute('app_menu_index');
    }
}
