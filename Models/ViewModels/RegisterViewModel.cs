using System.ComponentModel.DataAnnotations;

namespace BrasilBurger.Web.Models.ViewModels;

public class RegisterViewModel
{
    [Required(ErrorMessage = "Nom requis")]
    public string Nom { get; set; } = string.Empty;

    [Required(ErrorMessage = "Prenom requis")]
    public string Prenom { get; set; } = string.Empty;

    [Required(ErrorMessage = "Telephone requis")]
    public string Telephone { get; set; } = string.Empty;

    [Required(ErrorMessage = "Email requis")]
    [EmailAddress(ErrorMessage = "Email invalide")]
    public string Email { get; set; } = string.Empty;

    [Required(ErrorMessage = "Mot de passe requis")]
    [MinLength(6, ErrorMessage = "Minimum 6 caracteres")]
    public string Password { get; set; } = string.Empty;

    [Compare("Password", ErrorMessage = "Les mots de passe ne correspondent pas")]
    public string ConfirmPassword { get; set; } = string.Empty;
}
