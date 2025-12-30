using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("clients")]
public class Client
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("nom")]
    public string Nom { get; set; } = string.Empty;

    [Column("prenom")]
    public string Prenom { get; set; } = string.Empty;

    [Column("telephone")]
    public string Telephone { get; set; } = string.Empty;

    [Column("email")]
    public string? Email { get; set; }

    [Column("mot_de_passe")]
    public string MotDePasse { get; set; } = string.Empty;

    [Column("adresse")]
    public string? Adresse { get; set; }

    [Column("quartier_id")]
    public int? QuartierId { get; set; }

    [Column("actif")]
    public bool Actif { get; set; } = true;

    [Column("created_at")]
    public DateTime CreatedAt { get; set; }

    [Column("updated_at")]
    public DateTime UpdatedAt { get; set; }
}