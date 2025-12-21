using BrasilBurger.Web.Models.ViewModels;

namespace BrasilBurger.Web.Services.Interfaces;

public interface ICartService
{
    List<CartItem> GetCart();
    void AddToCart(CartItem item);
    void UpdateQuantity(int id, string type, int quantite);
    void RemoveFromCart(int id, string type);
    void ClearCart();
    decimal GetTotal();
    int GetItemCount();
}
