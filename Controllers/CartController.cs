using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Web.Services;
using BrasilBurger.Web.Models.ViewModels;

namespace BrasilBurger.Web.Controllers;

public class CartController : Controller
{
    private readonly CartService _cartService;
    private readonly CatalogueService _catalogueService;

    public CartController(CartService cartService, CatalogueService catalogueService)
    {
        _cartService = cartService;
        _catalogueService = catalogueService;
    }

    public IActionResult Index()
    {
        var cart = _cartService.GetCart();
        return View(cart);
    }

    [HttpPost]
    public async Task<IActionResult> AddBurger(int id, int quantite = 1)
    {
        var burger = await _catalogueService.GetBurgerByIdAsync(id);
        if (burger == null) return NotFound();

        var item = new CartItem
        {
            Id = burger.Id,
            Type = "BURGER",
            Nom = burger.Nom,
            Prix = burger.Prix,
            Quantite = quantite,
            Image = burger.Image
        };
        _cartService.AddToCart(item);

        return RedirectToAction("Index", "Home");
    }

    [HttpPost]
    public async Task<IActionResult> AddBurgerWithComplements(int burgerId, int quantite = 1, string? complementIds = null)
    {
        var burger = await _catalogueService.GetBurgerByIdAsync(burgerId);
        if (burger == null) return NotFound();

        var burgerItem = new CartItem
        {
            Id = burger.Id,
            Type = "BURGER",
            Nom = burger.Nom,
            Prix = burger.Prix,
            Quantite = quantite,
            Image = burger.Image
        };
        _cartService.AddToCart(burgerItem);

        if (!string.IsNullOrEmpty(complementIds))
        {
            var ids = complementIds.Split(',').Where(s => !string.IsNullOrEmpty(s)).Select(int.Parse);
            foreach (var compId in ids)
            {
                var complement = await _catalogueService.GetComplementByIdAsync(compId);
                if (complement != null)
                {
                    var compItem = new CartItem
                    {
                        Id = complement.Id,
                        Type = "COMPLEMENT",
                        Nom = complement.Nom,
                        Prix = complement.Prix,
                        Quantite = quantite,
                        Image = complement.Image
                    };
                    _cartService.AddToCart(compItem);
                }
            }
        }

        return RedirectToAction("Index", "Home");
    }

    [HttpPost]
    public async Task<IActionResult> AddMenu(int id, int quantite = 1)
    {
        var menu = await _catalogueService.GetMenuByIdAsync(id);
        if (menu == null) return NotFound();

        var item = new CartItem
        {
            Id = menu.Id,
            Type = "MENU",
            Nom = menu.Nom,
            Prix = menu.Prix,
            Quantite = quantite,
            Image = menu.Image
        };
        _cartService.AddToCart(item);

        return RedirectToAction("Index", "Home");
    }

    [HttpPost]
    public async Task<IActionResult> AddComplement(int id, int quantite = 1)
    {
        var complement = await _catalogueService.GetComplementByIdAsync(id);
        if (complement == null) return NotFound();

        var item = new CartItem
        {
            Id = complement.Id,
            Type = "COMPLEMENT",
            Nom = complement.Nom,
            Prix = complement.Prix,
            Quantite = quantite,
            Image = complement.Image
        };
        _cartService.AddToCart(item);

        return RedirectToAction("Index", "Home");
    }

    [HttpPost]
    public IActionResult UpdateQuantity(int id, string type, int quantite)
    {
        _cartService.UpdateQuantity(id, type, quantite);
        return RedirectToAction("Index");
    }

    [HttpPost]
    public IActionResult Remove(int id, string type)
    {
        _cartService.RemoveFromCart(id, type);
        return RedirectToAction("Index");
    }

    public IActionResult GetCartCount()
    {
        return Json(_cartService.GetItemCount());
    }
}
