using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Web.Services;
using BrasilBurger.Web.Models.ViewModels;

namespace BrasilBurger.Web.Controllers;

public class HomeController : Controller
{
    private readonly CatalogueService _catalogueService;

    public HomeController(CatalogueService catalogueService)
    {
        _catalogueService = catalogueService;
    }

    public async Task<IActionResult> Index(string filter = "tous")
    {
        var model = new HomeViewModel
        {
            Filter = filter,
            Burgers = await _catalogueService.GetBurgersAsync(),
            Menus = await _catalogueService.GetMenusAsync(),
            Complements = await _catalogueService.GetComplementsAsync()
        };
        return View(model);
    }

    public async Task<IActionResult> DetailBurger(int id)
    {
        var burger = await _catalogueService.GetBurgerByIdAsync(id);
        if (burger == null) return NotFound();
        return View(burger);
    }

    public async Task<IActionResult> DetailMenu(int id)
    {
        var menu = await _catalogueService.GetMenuByIdAsync(id);
        if (menu == null) return NotFound();
        return View(menu);
    }

    public async Task<IActionResult> DetailComplement(int id)
    {
        var complement = await _catalogueService.GetComplementByIdAsync(id);
        if (complement == null) return NotFound();
        return View(complement);
    }

    public IActionResult Error()
    {
        return View(new ErrorViewModel { RequestId = HttpContext.TraceIdentifier });
    }
}
