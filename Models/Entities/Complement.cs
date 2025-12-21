using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("complements")]
public class Complement
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Column("prix")]
    public decimal Prix { get; set; }

    [Column("type_complement")]
    public string TypeComplement { get; set; } = string.Empty;

    [Column("image")]
    public string? Image { get; set; }

    [Column("actif")]
    public bool Actif { get; set; } = true;
}
