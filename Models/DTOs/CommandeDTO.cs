namespace BrasilBurger.Web.Models.DTOs;

public class CommandeDTO
{
    public int Id { get; set; }
    public int IdClient { get; set; }
    public string TypeCommande { get; set; } = string.Empty;
    public string Etat { get; set; } = string.Empty;
    public DateTime DateCommande { get; set; }
    public decimal Total { get; set; }
    public string? AdresseLivraison { get; set; }
    public ZoneDTO? Zone { get; set; }
    public PaiementDTO? Paiement { get; set; }
    public List<CommandeItemDTO> Items { get; set; } = new();
}

public class CommandeItemDTO
{
    public int Id { get; set; }
    public string TypeItem { get; set; } = string.Empty;
    public int IdItem { get; set; }
    public int Quantite { get; set; }
    public decimal Prix { get; set; }
    public string NomItem { get; set; } = string.Empty;
    public string ImageItem { get; set; } = string.Empty;
}

public class CreateCommandeDTO
{
    public string TypeCommande { get; set; } = string.Empty;
    public int? ZoneId { get; set; }
    public string? AdresseLivraison { get; set; }
    public string ModePaiement { get; set; } = string.Empty;
}

public class PaiementDTO
{
    public int Id { get; set; }
    public int IdCommande { get; set; }
    public DateTime DatePaiement { get; set; }
    public decimal Montant { get; set; }
    public string Mode { get; set; } = string.Empty;
    public string Reference { get; set; } = string.Empty;
    public string Statut { get; set; } = string.Empty;
}

public class PaiementSimulationDTO
{
    public string Telephone { get; set; } = string.Empty;
    public decimal Montant { get; set; }
    public string Mode { get; set; } = string.Empty;
    public string Reference { get; set; } = string.Empty;
    public bool Success { get; set; }
    public string Message { get; set; } = string.Empty;
}
