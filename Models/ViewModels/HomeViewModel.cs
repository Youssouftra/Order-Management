using BrasilBurger.Web.Models.Entities;

namespace BrasilBurger.Web.Models.ViewModels;

public class HomeViewModel
{
    public List<Burger> Burgers { get; set; } = new();
    public List<Menu> Menus { get; set; } = new();
    public List<Complement> Complements { get; set; } = new();
    public string Filter { get; set; } = "tous";
}
