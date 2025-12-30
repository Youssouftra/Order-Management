<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Burger;
use App\Entity\Complement;
use App\Entity\Menu;
use App\Entity\Zone;
use App\Entity\Livreur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Créer un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@brasilburger.com');
        $admin->setNom('Admin');
        $admin->setPrenom('Brasil');
        $admin->setTelephone('+221 77 123 45 67');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setIsActive(true);
        $manager->persist($admin);

        // Créer les zones
        $zones = [];
        $zonesData = [
            ['nom' => 'Plateau', 'description' => 'Centre-ville de Dakar', 'frais' => 500],
            ['nom' => 'Médina', 'description' => 'Quartier populaire', 'frais' => 750],
            ['nom' => 'Almadies', 'description' => 'Zone résidentielle', 'frais' => 1000],
            ['nom' => 'Parcelles Assainies', 'description' => 'Grand quartier', 'frais' => 1200],
            ['nom' => 'Pikine', 'description' => 'Banlieue de Dakar', 'frais' => 1500],
        ];

        foreach ($zonesData as $data) {
            $zone = new Zone();
            $zone->setNom($data['nom']);
            $zone->setDescription($data['description']);
            $zone->setFraisLivraison($data['frais']);
            $zone->setIsActive(true);
            $manager->persist($zone);
            $zones[] = $zone;
        }

        // Créer les burgers
        $burgers = [];
        $burgersData = [
            ['nom' => 'Brasil Classic', 'description' => 'Le classique avec steak haché, salade, tomate, oignons et sauce maison', 'prix' => 2500],
            ['nom' => 'Double Brasil', 'description' => 'Double steak, double fromage, bacon croustillant', 'prix' => 3500],
            ['nom' => 'Chicken Brasil', 'description' => 'Filet de poulet grillé, avocat, salade et mayonnaise épicée', 'prix' => 2800],
            ['nom' => 'Veggie Brasil', 'description' => 'Galette de légumes, fromage, tomates fraîches et sauce yaourt', 'prix' => 2200],
            ['nom' => 'Spicy Brasil', 'description' => 'Steak épicé, jalapeños, piment, fromage et sauce piquante', 'prix' => 3000],
            ['nom' => 'Fish Brasil', 'description' => 'Filet de poisson pané, salade coleslaw et sauce tartare', 'prix' => 2700],
            ['nom' => 'BBQ Brasil', 'description' => 'Steak mariné, oignons caramélisés, bacon et sauce BBQ maison', 'prix' => 3200],
            ['nom' => 'Cheese Lovers', 'description' => 'Triple fromage (cheddar, emmental, mozzarella) et steak juteux', 'prix' => 3300],
        ];

        foreach ($burgersData as $data) {
            $burger = new Burger();
            $burger->setNom($data['nom']);
            $burger->setDescription($data['description']);
            $burger->setPrix($data['prix']);
            $burger->setIsActive(true);
            $manager->persist($burger);
            $burgers[] = $burger;
        }

        // Créer les compléments
        $complements = [];
        $complementsData = [
            // Boissons
            ['nom' => 'Coca-Cola', 'description' => 'Coca-Cola 33cl', 'prix' => 500, 'type' => 'boisson'],
            ['nom' => 'Fanta Orange', 'description' => 'Fanta Orange 33cl', 'prix' => 500, 'type' => 'boisson'],
            ['nom' => 'Sprite', 'description' => 'Sprite 33cl', 'prix' => 500, 'type' => 'boisson'],
            ['nom' => 'Eau minérale', 'description' => 'Eau minérale 50cl', 'prix' => 300, 'type' => 'boisson'],
            ['nom' => 'Jus de Bissap', 'description' => 'Jus de bissap maison 33cl', 'prix' => 600, 'type' => 'boisson'],
            ['nom' => 'Jus de Gingembre', 'description' => 'Jus de gingembre frais 33cl', 'prix' => 600, 'type' => 'boisson'],
            // Frites
            ['nom' => 'Frites classiques', 'description' => 'Portion de frites dorées', 'prix' => 800, 'type' => 'frite'],
            ['nom' => 'Frites au fromage', 'description' => 'Frites nappées de fromage fondu', 'prix' => 1200, 'type' => 'frite'],
            ['nom' => 'Potatoes', 'description' => 'Potatoes épicées croustillantes', 'prix' => 1000, 'type' => 'frite'],
            ['nom' => 'Onion Rings', 'description' => 'Rondelles d\'oignons panées', 'prix' => 900, 'type' => 'frite'],
            // Desserts
            ['nom' => 'Tiramisu', 'description' => 'Tiramisu maison au café', 'prix' => 1500, 'type' => 'dessert'],
            ['nom' => 'Brownie', 'description' => 'Brownie au chocolat fondant', 'prix' => 800, 'type' => 'dessert'],
            ['nom' => 'Glace vanille', 'description' => '2 boules de glace vanille', 'prix' => 700, 'type' => 'dessert'],
            ['nom' => 'Thiakry', 'description' => 'Thiakry traditionnel sénégalais', 'prix' => 1000, 'type' => 'dessert'],
            // Sauces
            ['nom' => 'Sauce Brasil', 'description' => 'Notre sauce maison signature', 'prix' => 200, 'type' => 'sauce'],
            ['nom' => 'Sauce Piquante', 'description' => 'Sauce piment fort', 'prix' => 200, 'type' => 'sauce'],
            ['nom' => 'Mayonnaise', 'description' => 'Mayonnaise maison', 'prix' => 150, 'type' => 'sauce'],
            ['nom' => 'Ketchup', 'description' => 'Ketchup premium', 'prix' => 150, 'type' => 'sauce'],
        ];

        foreach ($complementsData as $data) {
            $complement = new Complement();
            $complement->setNom($data['nom']);
            $complement->setDescription($data['description']);
            $complement->setPrix($data['prix']);
            $complement->setType($data['type']);
            $complement->setIsActive(true);
            $manager->persist($complement);
            $complements[$data['type']][] = $complement;
        }

        // Créer les menus
        $menusData = [
            [
                'nom' => 'Menu Classic',
                'description' => 'Brasil Classic + Frites + Boisson',
                'prix' => 3500,
                'burgerIndex' => 0,
                'boisson' => true,
                'frite' => true
            ],
            [
                'nom' => 'Menu Double',
                'description' => 'Double Brasil + Frites au fromage + Boisson',
                'prix' => 5000,
                'burgerIndex' => 1,
                'boisson' => true,
                'frite' => true
            ],
            [
                'nom' => 'Menu Chicken',
                'description' => 'Chicken Brasil + Potatoes + Boisson + Dessert',
                'prix' => 4800,
                'burgerIndex' => 2,
                'boisson' => true,
                'frite' => true,
                'dessert' => true
            ],
            [
                'nom' => 'Menu Duo',
                'description' => '2 Brasil Classic + 2 Frites + 2 Boissons',
                'prix' => 6500,
                'burgerIndex' => 0,
                'burgerQty' => 2,
                'boisson' => true,
                'frite' => true
            ],
            [
                'nom' => 'Menu Famille',
                'description' => '4 burgers au choix + 4 Frites + 4 Boissons',
                'prix' => 12000,
                'burgerIndex' => 0,
                'burgerQty' => 4,
                'boisson' => true,
                'frite' => true
            ],
        ];

        foreach ($menusData as $data) {
            $menu = new Menu();
            $menu->setNom($data['nom']);
            $menu->setDescription($data['description']);
            $menu->setPrix($data['prix']);
            $menu->setIsActive(true);
            $menu->addBurger($burgers[$data['burgerIndex']]);
            
            if (!empty($data['boisson']) && !empty($complements['boisson'])) {
                $menu->addComplement($complements['boisson'][0]);
            }
            if (!empty($data['frite']) && !empty($complements['frite'])) {
                $menu->addComplement($complements['frite'][0]);
            }
            if (!empty($data['dessert']) && !empty($complements['dessert'])) {
                $menu->addComplement($complements['dessert'][0]);
            }
            
            $manager->persist($menu);
        }

        // Créer les livreurs
        $livreursData = [
            ['prenom' => 'Moussa', 'nom' => 'Diallo', 'telephone' => '+221 77 234 56 78', 'statut' => 'disponible', 'zoneIndex' => 0],
            ['prenom' => 'Amadou', 'nom' => 'Sow', 'telephone' => '+221 77 345 67 89', 'statut' => 'disponible', 'zoneIndex' => 1],
            ['prenom' => 'Ibrahima', 'nom' => 'Ndiaye', 'telephone' => '+221 77 456 78 90', 'statut' => 'en_livraison', 'zoneIndex' => 2],
            ['prenom' => 'Ousmane', 'nom' => 'Fall', 'telephone' => '+221 77 567 89 01', 'statut' => 'disponible', 'zoneIndex' => 3],
            ['prenom' => 'Cheikh', 'nom' => 'Gueye', 'telephone' => '+221 77 678 90 12', 'statut' => 'indisponible', 'zoneIndex' => 4],
            ['prenom' => 'Pape', 'nom' => 'Mbaye', 'telephone' => '+221 77 789 01 23', 'statut' => 'disponible', 'zoneIndex' => 0],
        ];

        foreach ($livreursData as $data) {
            $livreur = new Livreur();
            $livreur->setPrenom($data['prenom']);
            $livreur->setNom($data['nom']);
            $livreur->setTelephone($data['telephone']);
            $livreur->setStatut($data['statut']);
            $livreur->setZone($zones[$data['zoneIndex']]);
            $livreur->setIsActive(true);
            $manager->persist($livreur);
        }

        $manager->flush();
    }
}
