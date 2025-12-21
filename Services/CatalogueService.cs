using Microsoft.EntityFrameworkCore;
using BrasilBurger.Web.Data;
using BrasilBurger.Web.Models.Entities;

namespace BrasilBurger.Web.Services;

public class CatalogueService
{
    private readonly ApplicationDbContext _context;

    public CatalogueService(ApplicationDbContext context)
    {
        _context = context;
    }

    public async Task<List<Burger>> GetBurgersAsync()
    {
        return await _context.Burgers.Where(b => b.Actif).ToListAsync();
    }

    public async Task<Burger?> GetBurgerByIdAsync(int id)
    {
        return await _context.Burgers.FindAsync(id);
    }

    public async Task<List<Complement>> GetComplementsAsync()
    {
        return await _context.Complements.Where(c => c.Actif).ToListAsync();
    }

    public async Task<Complement?> GetComplementByIdAsync(int id)
    {
        return await _context.Complements.FindAsync(id);
    }

    public async Task<List<Menu>> GetMenusAsync()
    {
        var menus = await _context.Menus.Where(m => m.Actif).ToListAsync();
        foreach (var menu in menus)
        {
            var items = await _context.MenuItems.Where(mi => mi.IdMenu == menu.Id).ToListAsync();
            menu.Items = items;
            decimal prix = 0;
            foreach (var item in items)
            {
                if (item.TypeItem == "BURGER")
                {
                    var burger = await _context.Burgers.FindAsync(item.IdItem);
                    if (burger != null) prix += burger.Prix * item.Quantite;
                }
                else if (item.TypeItem == "COMPLEMENT")
                {
                    var complement = await _context.Complements.FindAsync(item.IdItem);
                    if (complement != null) prix += complement.Prix * item.Quantite;
                }
            }
            menu.Prix = prix;
        }
        return menus;
    }

    public async Task<Menu?> GetMenuByIdAsync(int id)
    {
        var menu = await _context.Menus.FindAsync(id);
        if (menu != null)
        {
            var items = await _context.MenuItems.Where(mi => mi.IdMenu == menu.Id).ToListAsync();
            menu.Items = items;
            decimal prix = 0;
            foreach (var item in items)
            {
                if (item.TypeItem == "BURGER")
                {
                    var burger = await _context.Burgers.FindAsync(item.IdItem);
                    if (burger != null) prix += burger.Prix * item.Quantite;
                }
                else if (item.TypeItem == "COMPLEMENT")
                {
                    var complement = await _context.Complements.FindAsync(item.IdItem);
                    if (complement != null) prix += complement.Prix * item.Quantite;
                }
            }
            menu.Prix = prix;
        }
        return menu;
    }

    public async Task<List<Zone>> GetZonesAsync()
    {
        return await _context.Zones.ToListAsync();
    }

    public async Task<Zone?> GetZoneByIdAsync(int id)
    {
        return await _context.Zones.FindAsync(id);
    }
}
