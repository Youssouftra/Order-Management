using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("commandes")]
public class Commande
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("id_client")]
    public int IdClient { get; set; }

    [Column("type_commande")]
    public string TypeCommande { get; set; } = string.Empty;

    [Column("etat")]
    public string Etat { get; set; } = "EN_COURS";

    [Column("date_commande")]
    public DateTime DateCommande { get; set; }

    [Column("total")]
    public decimal Total { get; set; }

    [Column("id_zone")]
    public int? IdZone { get; set; }

    [Column("adresse_livraison")]
    public string? AdresseLivraison { get; set; }

    [NotMapped]
    public List<CommandeItem> Items { get; set; } = new();

    [NotMapped]
    public Zone? Zone { get; set; }

    [NotMapped]
    public Paiement? Paiement { get; set; }
}
