using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("commande_lignes")]
public class CommandeItem
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("commande_id")]
    public int CommandeId { get; set; }

    [Column("produit_id")]
    public int ProduitId { get; set; }

    [Column("quantite")]
    public int Quantite { get; set; } = 1;

    [Column("prix_unitaire")]
    public decimal PrixUnitaire { get; set; }

    [Column("sous_total")]
    public decimal SousTotal { get; set; }

    [Column("created_at")]
    public DateTime CreatedAt { get; set; }

    [NotMapped]
    public string? NomItem { get; set; }

    [NotMapped]
    public string? ImageItem { get; set; }
}