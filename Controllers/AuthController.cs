using Microsoft.AspNetCore.Mvc;
using BrasilBurger.Web.Services;
using BrasilBurger.Web.Models.ViewModels;

namespace BrasilBurger.Web.Controllers;

public class AuthController : Controller
{
    private readonly AuthService _authService;

    public AuthController(AuthService authService)
    {
        _authService = authService;
    }

    public IActionResult Login()
    {
        return View(new LoginViewModel());
    }

    [HttpPost]
    public async Task<IActionResult> Login(LoginViewModel model)
    {
        if (!ModelState.IsValid) return View(model);

        var client = await _authService.LoginAsync(model.Email, model.Password);
        if (client == null)
        {
            ModelState.AddModelError("", "Email ou mot de passe incorrect");
            return View(model);
        }

        HttpContext.Session.SetInt32("ClientId", client.Id);
        HttpContext.Session.SetString("ClientNom", client.Prenom + " " + client.Nom);

        var returnUrl = HttpContext.Session.GetString("ReturnUrl");
        if (!string.IsNullOrEmpty(returnUrl))
        {
            HttpContext.Session.Remove("ReturnUrl");
            return Redirect(returnUrl);
        }

        return RedirectToAction("Index", "Home");
    }

    public IActionResult Register()
    {
        return View(new RegisterViewModel());
    }

    [HttpPost]
    public async Task<IActionResult> Register(RegisterViewModel model)
    {
        if (!ModelState.IsValid) return View(model);

        var client = await _authService.RegisterAsync(model.Nom, model.Prenom, model.Telephone, model.Email, model.Password);
        if (client == null)
        {
            ModelState.AddModelError("", "Email ou telephone deja utilise");
            return View(model);
        }

        HttpContext.Session.SetInt32("ClientId", client.Id);
        HttpContext.Session.SetString("ClientNom", client.Prenom + " " + client.Nom);

        var returnUrl = HttpContext.Session.GetString("ReturnUrl");
        if (!string.IsNullOrEmpty(returnUrl))
        {
            HttpContext.Session.Remove("ReturnUrl");
            return Redirect(returnUrl);
        }

        return RedirectToAction("Index", "Home");
    }

    public IActionResult Logout()
    {
        HttpContext.Session.Clear();
        return RedirectToAction("Index", "Home");
    }
}
