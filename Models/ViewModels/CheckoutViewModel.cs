using BrasilBurger.Web.Models.Entities;

namespace BrasilBurger.Web.Models.ViewModels;

public class CheckoutViewModel
{
    public List<CartItem> CartItems { get; set; } = new();
    public List<Zone> Zones { get; set; } = new();
    public decimal SousTotal { get; set; }
    public decimal FraisLivraison { get; set; }
    public decimal Total { get; set; }
    public string TypeCommande { get; set; } = "SUR_PLACE";
    public int? ZoneId { get; set; }
    public string? AdresseLivraison { get; set; }
    public string ModePaiement { get; set; } = "WAVE";
}
