<?php

namespace App\Controller;

use App\Entity\Complement;
use App\Form\ComplementType;
use App\Repository\ComplementRepository;
use App\Service\Impl\CloudinaryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/complements')]
class ComplementController extends AbstractController
{
    public function __construct(
        private ComplementRepository $complementRepository,
        private EntityManagerInterface $entityManager,
        private CloudinaryService $cloudinaryService
    ) {
    }

    #[Route('', name: 'app_complement_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $search = $request->query->get('search');
        $type = $request->query->get('type');
        $status = $request->query->get('status');
        $limit = 100;
        
        $isActiveFilter = null;
        if ($status === '1') $isActiveFilter = true;
        if ($status === '0') $isActiveFilter = false;

        $paginator = $this->complementRepository->findPaginated($page, $limit, $search, $type, $isActiveFilter);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $limit);

        return $this->render('complement/index.html.twig', [
            'complements' => $paginator,
            'pagination' => [
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'totalItems' => $totalItems,
            ],
            'types' => Complement::TYPES,
        ]);
    }

    #[Route('/new', name: 'app_complement_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        // Handle modal form submission
        if ($request->isMethod('POST') && $request->request->has('complement')) {
            $data = $request->request->all('complement');
            
            if (!$this->isCsrfTokenValid('complement', $data['_token'] ?? '')) {
                $this->addFlash('error', 'Token CSRF invalide.');
                return $this->redirectToRoute('app_complement_index');
            }

            $complement = new Complement();
            $complement->setNom($data['nom'] ?? '');
            $complement->setType($data['type'] ?? '');
            $complement->setPrix((int) ($data['prix'] ?? 0));
            $complement->setImage($data['image'] ?? null);
            $complement->setIsActive(true);
            
            $this->entityManager->persist($complement);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le complément a été créé avec succès.');
            return $this->redirectToRoute('app_complement_index');
        }

        // Handle Symfony form
        $complement = new Complement();
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $imageUrl = $this->cloudinaryService->upload($imageFile, 'brasil-burger/complements');
                if ($imageUrl) {
                    $complement->setImage($imageUrl);
                }
            }

            $this->entityManager->persist($complement);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le complément a été créé avec succès.');
            return $this->redirectToRoute('app_complement_index');
        }

        return $this->render('complement/form.html.twig', [
            'form' => $form,
            'complement' => $complement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_complement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Complement $complement): Response
    {
        $form = $this->createForm(ComplementType::class, $complement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                if ($complement->getImage()) {
                    $publicId = $this->cloudinaryService->getPublicIdFromUrl($complement->getImage());
                    if ($publicId) {
                        $this->cloudinaryService->delete($publicId);
                    }
                }
                
                $imageUrl = $this->cloudinaryService->upload($imageFile, 'brasil-burger/complements');
                if ($imageUrl) {
                    $complement->setImage($imageUrl);
                }
            }

            $complement->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $this->addFlash('success', 'Le complément a été modifié avec succès.');
            return $this->redirectToRoute('app_complement_index');
        }

        return $this->render('complement/form.html.twig', [
            'form' => $form,
            'complement' => $complement,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_complement_delete', methods: ['POST'])]
    public function delete(Request $request, Complement $complement): Response
    {
        if ($this->isCsrfTokenValid('delete' . $complement->getId(), $request->request->get('_token'))) {
            if ($complement->getImage()) {
                $publicId = $this->cloudinaryService->getPublicIdFromUrl($complement->getImage());
                if ($publicId) {
                    $this->cloudinaryService->delete($publicId);
                }
            }

            $this->entityManager->remove($complement);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le complément a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_complement_index');
    }

    #[Route('/{id}/toggle', name: 'app_complement_toggle', methods: ['POST'])]
    public function toggle(Request $request, Complement $complement): Response
    {
        if ($this->isCsrfTokenValid('toggle' . $complement->getId(), $request->request->get('_token'))) {
            $complement->setIsActive(!$complement->isActive());
            $complement->setUpdatedAt(new \DateTimeImmutable());
            $this->entityManager->flush();

            $status = $complement->isActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "Le complément a été {$status}.");
        }

        return $this->redirectToRoute('app_complement_index');
    }
}
