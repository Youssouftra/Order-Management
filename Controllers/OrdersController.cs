using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Web.Services;
using BrasilBurger.Web.Models.ViewModels;

namespace BrasilBurger.Web.Controllers;

public class OrdersController : Controller
{
    private readonly CommandeService _commandeService;
    private readonly CartService _cartService;
    private readonly CatalogueService _catalogueService;

    public OrdersController(CommandeService commandeService, CartService cartService, CatalogueService catalogueService)
    {
        _commandeService = commandeService;
        _cartService = cartService;
        _catalogueService = catalogueService;
    }

    public async Task<IActionResult> Checkout()
    {
        var clientId = HttpContext.Session.GetInt32("ClientId");
        if (clientId == null)
        {
            HttpContext.Session.SetString("ReturnUrl", "/Orders/Checkout");
            return RedirectToAction("Login", "Auth");
        }

        var cart = _cartService.GetCart();
        if (!cart.Any())
        {
            return RedirectToAction("Index", "Cart");
        }

        var zones = await _catalogueService.GetZonesAsync();
        var model = new CheckoutViewModel
        {
            CartItems = cart,
            Zones = zones,
            SousTotal = cart.Sum(c => c.Prix * c.Quantite),
            Total = cart.Sum(c => c.Prix * c.Quantite)
        };

        return View(model);
    }

    [HttpPost]
    public async Task<IActionResult> ProcessOrder(string typeCommande, int? zoneId, string? adresseLivraison, string modePaiement)
    {
        var clientId = HttpContext.Session.GetInt32("ClientId");
        if (clientId == null)
        {
            return RedirectToAction("Login", "Auth");
        }

        var cart = _cartService.GetCart();
        if (!cart.Any())
        {
            return RedirectToAction("Index", "Cart");
        }

        var commande = await _commandeService.CreateCommandeAsync(clientId.Value, cart, typeCommande, zoneId, adresseLivraison);
        await _commandeService.CreatePaiementAsync(commande.Id, commande.Total, modePaiement);
        _cartService.ClearCart();

        return RedirectToAction("Confirmation", new { id = commande.Id });
    }

    public async Task<IActionResult> Confirmation(int id)
    {
        var clientId = HttpContext.Session.GetInt32("ClientId");
        if (clientId == null)
        {
            return RedirectToAction("Login", "Auth");
        }

        var commande = await _commandeService.GetCommandeByIdAsync(id);
        if (commande == null || commande.IdClient != clientId)
        {
            return NotFound();
        }

        return View(commande);
    }

    public async Task<IActionResult> List()
    {
        var clientId = HttpContext.Session.GetInt32("ClientId");
        if (clientId == null)
        {
            HttpContext.Session.SetString("ReturnUrl", "/Orders/List");
            return RedirectToAction("Login", "Auth");
        }

        var commandes = await _commandeService.GetCommandesByClientAsync(clientId.Value);
        return View(commandes);
    }

    public async Task<IActionResult> Detail(int id)
    {
        var clientId = HttpContext.Session.GetInt32("ClientId");
        if (clientId == null)
        {
            return RedirectToAction("Login", "Auth");
        }

        var commande = await _commandeService.GetCommandeByIdAsync(id);
        if (commande == null || commande.IdClient != clientId)
        {
            return NotFound();
        }

        return View(commande);
    }

    [HttpGet]
    public async Task<IActionResult> GetZonePrix(int id)
    {
        var zone = await _catalogueService.GetZoneByIdAsync(id);
        if (zone == null) return Json(new { prix = 0 });
        return Json(new { prix = zone.PrixLivraison });
    }
}
