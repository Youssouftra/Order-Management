using Microsoft.EntityFrameworkCore;
using BrasilBurger.Web.Data;
using BrasilBurger.Web.Models.Entities;
using BrasilBurger.Web.Models.DTOs;
using BrasilBurger.Web.Services.Interfaces;
using BCrypt.Net;

namespace BrasilBurger.Web.Services;

public class AuthService : IAuthService
{
    private readonly ApplicationDbContext _context;

    public AuthService(ApplicationDbContext context)
    {
        _context = context;
    }

    public async Task<Client?> LoginAsync(string email, string password)
    {
        var client = await _context.Clients.FirstOrDefaultAsync(c => c.Email == email);
        if (client != null && BCrypt.Net.BCrypt.Verify(password, client.Password))
        {
            return client;
        }
        return null;
    }

    public async Task<Client?> RegisterAsync(string nom, string prenom, string telephone, string email, string password)
    {
        var existingEmail = await _context.Clients.FirstOrDefaultAsync(c => c.Email == email);
        if (existingEmail != null) return null;

        var existingPhone = await _context.Clients.FirstOrDefaultAsync(c => c.Telephone == telephone);
        if (existingPhone != null) return null;

        var client = new Client
        {
            Nom = nom,
            Prenom = prenom,
            Telephone = telephone,
            Email = email,
            Password = BCrypt.Net.BCrypt.HashPassword(password)
        };

        _context.Clients.Add(client);
        await _context.SaveChangesAsync();
        return client;
    }

    public async Task<Client?> GetClientByIdAsync(int id)
    {
        return await _context.Clients.FindAsync(id);
    }

    public ClientDTO? ToDTO(Client? client)
    {
        if (client == null) return null;
        
        return new ClientDTO
        {
            Id = client.Id,
            Nom = client.Nom,
            Prenom = client.Prenom,
            Telephone = client.Telephone,
            Email = client.Email
        };
    }
}
