using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("paiements")]
public class Paiement
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("id_commande")]
    public int IdCommande { get; set; }

    [Column("date_paiement")]
    public DateTime DatePaiement { get; set; }

    [Column("montant")]
    public decimal Montant { get; set; }

    [Column("mode")]
    public string Mode { get; set; } = string.Empty;
}
