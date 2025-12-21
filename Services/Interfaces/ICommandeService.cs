using BrasilBurger.Web.Models.Entities;
using BrasilBurger.Web.Models.ViewModels;
using BrasilBurger.Web.Models.DTOs;

namespace BrasilBurger.Web.Services.Interfaces;

public interface ICommandeService
{
    Task<Commande> CreateCommandeAsync(int clientId, List<CartItem> items, string typeCommande, int? zoneId, string? adresseLivraison);
    Task<Paiement> CreatePaiementAsync(int commandeId, decimal montant, string mode);
    Task<List<Commande>> GetCommandesByClientAsync(int clientId);
    Task<Commande?> GetCommandeByIdAsync(int id);
    
    CommandeDTO ToDTO(Commande commande);
    PaiementDTO ToDTO(Paiement paiement);
}
