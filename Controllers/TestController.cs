using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using BrasilBurger.Web.Data;

namespace BrasilBurger.Web.Controllers;

public class TestController : Controller
{
    private readonly ApplicationDbContext _context;

    public TestController(ApplicationDbContext context)
    {
        _context = context;
    }

    public async Task<IActionResult> VerifyDatabase()
    {
        var result = new
        {
            TotalCommandes = await _context.Commandes.CountAsync(),
            TotalCommandeItems = await _context.CommandeItems.CountAsync(),
            TotalPaiements = await _context.Paiements.CountAsync(),
            DernieresCommandes = await _context.Commandes
                .OrderByDescending(c => c.DateCommande)
                .Take(5)
                .Select(c => new {
                    c.Id,
                    c.ClientId,
                    c.TypeLivraison,
                    c.Statut,
                    c.DateCommande,
                    c.MontantTotal
                })
                .ToListAsync()
        };

        return Json(result);
    }

    public async Task<IActionResult> CheckTables()
    {
        var sql = @"
            SELECT table_name 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            ORDER BY table_name";

        var tables = await _context.Database.SqlQueryRaw<string>(sql).ToListAsync();
        
        return Json(new { Tables = tables });
    }
}