using Microsoft.EntityFrameworkCore;
using BrasilBurger.Web.Data;
using BrasilBurger.Web.Models.Entities;
using BrasilBurger.Web.Models.ViewModels;

namespace BrasilBurger.Web.Services;

public class CommandeService
{
    private readonly ApplicationDbContext _context;
    private readonly CatalogueService _catalogueService;

    public CommandeService(ApplicationDbContext context, CatalogueService catalogueService)
    {
        _context = context;
        _catalogueService = catalogueService;
    }

    public async Task<Commande> CreateCommandeAsync(int clientId, List<CartItem> items, string typeCommande, int? zoneId, string? adresseLivraison)
    {
        decimal total = items.Sum(i => i.Prix * i.Quantite);
        decimal fraisLivraison = 0;

        if (typeCommande == "LIVRAISON" && zoneId.HasValue)
        {
            var zone = await _context.Zones.FindAsync(zoneId.Value);
            if (zone != null)
            {
                fraisLivraison = zone.PrixLivraison;
                total += fraisLivraison;
            }
        }

        var commande = new Commande
        {
            ClientId = clientId,
            TypeLivraison = typeCommande,
            Statut = "EN_ATTENTE",
            DateCommande = DateTime.UtcNow,
            CreatedAt = DateTime.UtcNow,
            MontantTotal = total,
            FraisLivraison = fraisLivraison,
            ZoneId = typeCommande == "LIVRAISON" ? zoneId : null
        };

        _context.Commandes.Add(commande);
        await _context.SaveChangesAsync();

        foreach (var item in items)
        {
            var commandeItem = new CommandeItem
            {
                CommandeId = commande.Id,
                ProduitId = item.Id,
                Quantite = item.Quantite,
                PrixUnitaire = item.Prix,
                SousTotal = item.Prix * item.Quantite,
                CreatedAt = DateTime.UtcNow
            };
            _context.CommandeItems.Add(commandeItem);
        }
        await _context.SaveChangesAsync();

        return commande;
    }

    public async Task<Paiement> CreatePaiementAsync(int commandeId, decimal montant, string mode)
    {
        var paiement = new Paiement
        {
            CommandeId = commandeId,
            DatePaiement = DateTime.UtcNow,
            CreatedAt = DateTime.UtcNow,
            Montant = montant,
            ModePaiement = mode,
            Statut = "VALIDE"
        };

        _context.Paiements.Add(paiement);

        var commande = await _context.Commandes.FindAsync(commandeId);
        if (commande != null)
        {
            commande.Statut = "VALIDEE";
        }

        await _context.SaveChangesAsync();
        return paiement;
    }

    public async Task<List<Commande>> GetCommandesByClientAsync(int clientId)
    {
        var commandes = await _context.Commandes
            .Where(c => c.ClientId == clientId)
            .OrderByDescending(c => c.DateCommande)
            .ToListAsync();

        foreach (var commande in commandes)
        {
            commande.Items = await GetCommandeItemsAsync(commande.Id);
            if (commande.ZoneId.HasValue)
            {
                commande.Zone = await _context.Zones.FindAsync(commande.ZoneId.Value);
            }
            commande.Paiement = await _context.Paiements.FirstOrDefaultAsync(p => p.CommandeId == commande.Id);
        }

        return commandes;
    }

    public async Task<Commande?> GetCommandeByIdAsync(int id)
    {
        var commande = await _context.Commandes.FindAsync(id);
        if (commande != null)
        {
            commande.Items = await GetCommandeItemsAsync(commande.Id);
            if (commande.ZoneId.HasValue)
            {
                commande.Zone = await _context.Zones.FindAsync(commande.ZoneId.Value);
            }
            commande.Paiement = await _context.Paiements.FirstOrDefaultAsync(p => p.CommandeId == commande.Id);
        }
        return commande;
    }

    private async Task<List<CommandeItem>> GetCommandeItemsAsync(int commandeId)
    {
        var items = await _context.CommandeItems
            .Where(ci => ci.CommandeId == commandeId)
            .ToListAsync();

        foreach (var item in items)
        {
            // Récupérer le nom du produit depuis la table produits
            var burger = await _context.Burgers.FindAsync(item.ProduitId);
            if (burger != null)
            {
                item.NomItem = burger.Nom;
                item.ImageItem = burger.Image;
            }
            else
            {
                var menu = await _context.Menus.FindAsync(item.ProduitId);
                if (menu != null)
                {
                    item.NomItem = menu.Nom;
                    item.ImageItem = menu.Image;
                }
                else
                {
                    var complement = await _context.Complements.FindAsync(item.ProduitId);
                    if (complement != null)
                    {
                        item.NomItem = complement.Nom;
                        item.ImageItem = complement.Image;
                    }
                }
            }
        }

        return items;
    }
}
