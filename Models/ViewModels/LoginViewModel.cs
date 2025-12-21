using System.ComponentModel.DataAnnotations;

namespace BrasilBurger.Web.Models.ViewModels;

public class LoginViewModel
{
    [Required(ErrorMessage = "Email requis")]
    [EmailAddress(ErrorMessage = "Email invalide")]
    public string Email { get; set; } = string.Empty;

    [Required(ErrorMessage = "Mot de passe requis")]
    public string Password { get; set; } = string.Empty;
}
