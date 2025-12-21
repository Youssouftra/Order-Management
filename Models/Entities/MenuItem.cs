using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace BrasilBurger.Web.Models.Entities;

[Table("menu_items")]
public class MenuItem
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("id_menu")]
    public int IdMenu { get; set; }

    [Column("type_item")]
    public string TypeItem { get; set; } = string.Empty;

    [Column("id_item")]
    public int IdItem { get; set; }

    [Column("quantite")]
    public int Quantite { get; set; } = 1;
}
