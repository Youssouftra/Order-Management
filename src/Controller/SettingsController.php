<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/parametres')]
class SettingsController extends AbstractController
{
    #[Route('', name: 'app_settings')]
    public function index(): Response
    {
        // Default settings - in a real app, these would come from database
        $settings = [
            'restaurant' => [
                'nom' => 'Brasil Burger',
                'telephone' => '+221 33 123 45 67',
                'adresse' => '123 Avenue Cheikh Anta Diop, Dakar',
                'email' => 'contact@brasilburger.sn',
            ],
            'notifications' => [
                'nouvelles_commandes' => true,
                'commandes_annulees' => true,
                'rapports_quotidiens' => false,
            ],
            'paiement' => [
                'wave' => true,
                'orange_money' => true,
            ],
        ];

        return $this->render('settings/index.html.twig', [
            'settings' => $settings,
        ]);
    }

    #[Route('/save', name: 'app_settings_save', methods: ['POST'])]
    public function save(Request $request): Response
    {
        // In a real application, you would save these settings to the database
        // For now, we'll just show a success message
        
        $this->addFlash('success', 'Les paramètres ont été enregistrés avec succès.');
        
        return $this->redirectToRoute('app_settings');
    }
}
