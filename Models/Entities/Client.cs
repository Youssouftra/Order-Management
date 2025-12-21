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
    public string Email { get; set; } = string.Empty;

    [Column("mot_de_passe")]
    public string Password { get; set; } = string.Empty;
}
