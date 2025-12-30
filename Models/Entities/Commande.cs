using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("commandes")]
public class Commande
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("client_id")]
    public int ClientId { get; set; }

    [Column("date_commande")]
    public DateTime DateCommande { get; set; }

    [Column("statut")]
    public string Statut { get; set; } = "EN_ATTENTE";

    [Column("type_livraison")]
    public string TypeLivraison { get; set; } = string.Empty;

    [Column("quartier_id")]
    public int? QuartierId { get; set; }

    [Column("zone_id")]
    public int? ZoneId { get; set; }

    [Column("livreur_id")]
    public int? LivreurId { get; set; }

    [Column("montant_total")]
    public decimal MontantTotal { get; set; }

    [Column("frais_livraison")]
    public decimal FraisLivraison { get; set; }

    [Column("notes")]
    public string? Notes { get; set; }

    [Column("created_at")]
    public DateTime CreatedAt { get; set; }

    [NotMapped]
    public List<CommandeItem> Items { get; set; } = new();

    [NotMapped]
    public Zone? Zone { get; set; }

    [NotMapped]
    public Paiement? Paiement { get; set; }
}