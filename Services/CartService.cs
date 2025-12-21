using System.Text.Json;
using BrasilBurger.Web.Models.ViewModels;

namespace BrasilBurger.Web.Services;

public class CartService
{
    private readonly IHttpContextAccessor _httpContextAccessor;
    private const string CartKey = "Cart";

    public CartService(IHttpContextAccessor httpContextAccessor)
    {
        _httpContextAccessor = httpContextAccessor;
    }

    public List<CartItem> GetCart()
    {
        var session = _httpContextAccessor.HttpContext?.Session;
        var cartJson = session?.GetString(CartKey);
        if (string.IsNullOrEmpty(cartJson))
        {
            return new List<CartItem>();
        }
        return JsonSerializer.Deserialize<List<CartItem>>(cartJson) ?? new List<CartItem>();
    }

    public void AddToCart(CartItem item)
    {
        var cart = GetCart();
        var existingItem = cart.FirstOrDefault(c => c.Id == item.Id && c.Type == item.Type);
        if (existingItem != null)
        {
            existingItem.Quantite += item.Quantite;
        }
        else
        {
            cart.Add(item);
        }
        SaveCart(cart);
    }

    public void UpdateQuantity(int id, string type, int quantite)
    {
        var cart = GetCart();
        var item = cart.FirstOrDefault(c => c.Id == id && c.Type == type);
        if (item != null)
        {
            if (quantite <= 0)
            {
                cart.Remove(item);
            }
            else
            {
                item.Quantite = quantite;
            }
        }
        SaveCart(cart);
    }

    public void RemoveFromCart(int id, string type)
    {
        var cart = GetCart();
        var item = cart.FirstOrDefault(c => c.Id == id && c.Type == type);
        if (item != null)
        {
            cart.Remove(item);
        }
        SaveCart(cart);
    }

    public void ClearCart()
    {
        SaveCart(new List<CartItem>());
    }

    public decimal GetTotal()
    {
        return GetCart().Sum(c => c.Prix * c.Quantite);
    }

    public int GetItemCount()
    {
        return GetCart().Sum(c => c.Quantite);
    }

    private void SaveCart(List<CartItem> cart)
    {
        var session = _httpContextAccessor.HttpContext?.Session;
        session?.SetString(CartKey, JsonSerializer.Serialize(cart));
    }
}
