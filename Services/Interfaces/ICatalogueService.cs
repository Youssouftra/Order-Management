using BrasilBurger.Web.Models.Entities;
using BrasilBurger.Web.Models.DTOs;

namespace BrasilBurger.Web.Services.Interfaces;

public interface ICatalogueService
{
    Task<List<Burger>> GetBurgersAsync();
    Task<Burger?> GetBurgerByIdAsync(int id);
    Task<List<Complement>> GetComplementsAsync();
    Task<Complement?> GetComplementByIdAsync(int id);
    Task<List<Menu>> GetMenusAsync();
    Task<Menu?> GetMenuByIdAsync(int id);
    Task<List<Zone>> GetZonesAsync();
    Task<Zone?> GetZoneByIdAsync(int id);
    
    BurgerDTO ToDTO(Burger burger);
    ComplementDTO ToDTO(Complement complement);
    MenuDTO ToDTO(Menu menu);
    ZoneDTO ToDTO(Zone zone);
}
