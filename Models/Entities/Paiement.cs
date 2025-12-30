using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("paiements")]
public class Paiement
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("commande_id")]
    public int CommandeId { get; set; }

    [Column("date_paiement")]
    public DateTime DatePaiement { get; set; }

    [Column("montant")]
    public decimal Montant { get; set; }

    [Column("mode_paiement")]
    public string ModePaiement { get; set; } = string.Empty;

    [Column("reference_transaction")]
    public string? ReferenceTransaction { get; set; }

    [Column("statut")]
    public string Statut { get; set; } = "VALIDE";

    [Column("created_at")]
    public DateTime CreatedAt { get; set; }
}
