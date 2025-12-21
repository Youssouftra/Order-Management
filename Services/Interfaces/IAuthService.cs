using BrasilBurger.Web.Models.Entities;
using BrasilBurger.Web.Models.DTOs;

namespace BrasilBurger.Web.Services.Interfaces;

public interface IAuthService
{
    Task<Client?> LoginAsync(string email, string password);
    Task<Client?> RegisterAsync(string nom, string prenom, string telephone, string email, string password);
    Task<Client?> GetClientByIdAsync(int id);
    ClientDTO? ToDTO(Client? client);
}
