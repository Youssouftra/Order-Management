namespace BrasilBurger.Web.Models.DTOs;

public class BurgerDTO
{
    public int Id { get; set; }
    public string Nom { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public decimal Prix { get; set; }
    public string Image { get; set; } = string.Empty;
}

public class ComplementDTO
{
    public int Id { get; set; }
    public string Nom { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public decimal Prix { get; set; }
    public string Image { get; set; } = string.Empty;
}

public class MenuDTO
{
    public int Id { get; set; }
    public string Nom { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public decimal Prix { get; set; }
    public string Image { get; set; } = string.Empty;
    public List<MenuItemDTO> Items { get; set; } = new();
}

public class MenuItemDTO
{
    public int Id { get; set; }
    public string TypeItem { get; set; } = string.Empty;
    public int IdItem { get; set; }
    public int Quantite { get; set; }
    public string NomItem { get; set; } = string.Empty;
}

public class ZoneDTO
{
    public int Id { get; set; }
    public string Nom { get; set; } = string.Empty;
    public decimal PrixLivraison { get; set; }
}
