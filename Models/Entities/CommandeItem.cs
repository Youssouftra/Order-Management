using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("commande_items")]
public class CommandeItem
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("id_commande")]
    public int IdCommande { get; set; }

    [Column("type_item")]
    public string TypeItem { get; set; } = string.Empty;

    [Column("id_item")]
    public int IdItem { get; set; }

    [Column("quantite")]
    public int Quantite { get; set; } = 1;

    [Column("prix")]
    public decimal Prix { get; set; }

    [NotMapped]
    public string? NomItem { get; set; }

    [NotMapped]
    public string? ImageItem { get; set; }
}
