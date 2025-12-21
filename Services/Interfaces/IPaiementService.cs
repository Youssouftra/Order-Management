using BrasilBurger.Web.Models.DTOs;

namespace BrasilBurger.Web.Services.Interfaces;

public interface IPaiementService
{
    PaiementSimulationDTO SimulerPaiement(decimal montant, string mode, string telephone);
    bool ValiderTelephone(string telephone, string mode);
}
