using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("burgers")]
public class Burger
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Column("prix")]
    public decimal Prix { get; set; }

    [Column("image")]
    public string? Image { get; set; }

    [Column("actif")]
    public bool Actif { get; set; } = true;
}
