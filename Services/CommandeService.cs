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

        if (typeCommande == "LIVRAISON" && zoneId.HasValue)
        {
            var zone = await _context.Zones.FindAsync(zoneId.Value);
            if (zone != null)
            {
                total += zone.PrixLivraison;
            }
        }

        var commande = new Commande
        {
            IdClient = clientId,
            TypeCommande = typeCommande,
            Etat = "EN_COURS",
            DateCommande = DateTime.UtcNow,
            Total = total,
            IdZone = typeCommande == "LIVRAISON" ? zoneId : null,
            AdresseLivraison = typeCommande == "LIVRAISON" ? adresseLivraison : null
        };

        _context.Commandes.Add(commande);
        await _context.SaveChangesAsync();

        foreach (var item in items)
        {
            var commandeItem = new CommandeItem
            {
                IdCommande = commande.Id,
                TypeItem = item.Type.ToUpper(),
                IdItem = item.Id,
                Quantite = item.Quantite,
                Prix = item.Prix
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
            IdCommande = commandeId,
            DatePaiement = DateTime.UtcNow,
            Montant = montant,
            Mode = mode
        };

        _context.Paiements.Add(paiement);

        var commande = await _context.Commandes.FindAsync(commandeId);
        if (commande != null)
        {
            commande.Etat = "VALIDE";
        }

        await _context.SaveChangesAsync();
        return paiement;
    }

    public async Task<List<Commande>> GetCommandesByClientAsync(int clientId)
    {
        var commandes = await _context.Commandes
            .Where(c => c.IdClient == clientId)
            .OrderByDescending(c => c.DateCommande)
            .ToListAsync();

        foreach (var commande in commandes)
        {
            commande.Items = await GetCommandeItemsAsync(commande.Id);
            if (commande.IdZone.HasValue)
            {
                commande.Zone = await _context.Zones.FindAsync(commande.IdZone.Value);
            }
            commande.Paiement = await _context.Paiements.FirstOrDefaultAsync(p => p.IdCommande == commande.Id);
        }

        return commandes;
    }

    public async Task<Commande?> GetCommandeByIdAsync(int id)
    {
        var commande = await _context.Commandes.FindAsync(id);
        if (commande != null)
        {
            commande.Items = await GetCommandeItemsAsync(commande.Id);
            if (commande.IdZone.HasValue)
            {
                commande.Zone = await _context.Zones.FindAsync(commande.IdZone.Value);
            }
            commande.Paiement = await _context.Paiements.FirstOrDefaultAsync(p => p.IdCommande == commande.Id);
        }
        return commande;
    }

    private async Task<List<CommandeItem>> GetCommandeItemsAsync(int commandeId)
    {
        var items = await _context.CommandeItems
            .Where(ci => ci.IdCommande == commandeId)
            .ToListAsync();

        foreach (var item in items)
        {
            if (item.TypeItem == "BURGER")
            {
                var burger = await _context.Burgers.FindAsync(item.IdItem);
                if (burger != null)
                {
                    item.NomItem = burger.Nom;
                    item.ImageItem = burger.Image;
                }
            }
            else if (item.TypeItem == "MENU")
            {
                var menu = await _context.Menus.FindAsync(item.IdItem);
                if (menu != null)
                {
                    item.NomItem = menu.Nom;
                    item.ImageItem = menu.Image;
                }
            }
            else if (item.TypeItem == "COMPLEMENT")
            {
                var complement = await _context.Complements.FindAsync(item.IdItem);
                if (complement != null)
                {
                    item.NomItem = complement.Nom;
                    item.ImageItem = complement.Image;
                }
            }
        }

        return items;
    }
}
