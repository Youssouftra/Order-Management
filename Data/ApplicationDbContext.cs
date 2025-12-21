using Microsoft.EntityFrameworkCore;
using BrasilBurger.Web.Models.Entities;

namespace BrasilBurger.Web.Data;

public class ApplicationDbContext : DbContext
{
    public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options) : base(options)
    {
    }

    public DbSet<Client> Clients { get; set; }
    public DbSet<Burger> Burgers { get; set; }
    public DbSet<Complement> Complements { get; set; }
    public DbSet<Menu> Menus { get; set; }
    public DbSet<MenuItem> MenuItems { get; set; }
    public DbSet<Zone> Zones { get; set; }
    public DbSet<Commande> Commandes { get; set; }
    public DbSet<CommandeItem> CommandeItems { get; set; }
    public DbSet<Paiement> Paiements { get; set; }
}
