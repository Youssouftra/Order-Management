namespace BrasilBurger.Web.Models.ViewModels;

public class CartItem
{
    public int Id { get; set; }
    public string Type { get; set; } = string.Empty;
    public string Nom { get; set; } = string.Empty;
    public decimal Prix { get; set; }
    public int Quantite { get; set; } = 1;
    public string? Image { get; set; }
}
