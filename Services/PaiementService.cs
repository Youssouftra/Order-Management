using BrasilBurger.Web.Models.DTOs;
using BrasilBurger.Web.Services.Interfaces;

namespace BrasilBurger.Web.Services;

public class PaiementService : IPaiementService
{
    public PaiementSimulationDTO SimulerPaiement(decimal montant, string mode, string telephone)
    {
        var reference = GenererReference(mode);
        
        var resultat = new PaiementSimulationDTO
        {
            Telephone = telephone,
            Montant = montant,
            Mode = mode,
            Reference = reference,
            Success = true,
            Message = mode == "WAVE" 
                ? $"Paiement Wave de {montant:N0} FCFA effectue avec succes. Reference: {reference}"
                : $"Paiement Orange Money de {montant:N0} FCFA effectue avec succes. Reference: {reference}"
        };

        return resultat;
    }

    public bool ValiderTelephone(string telephone, string mode)
    {
        if (string.IsNullOrWhiteSpace(telephone)) return false;
        
        var cleaned = telephone.Replace(" ", "").Replace("-", "");
        
        if (cleaned.Length < 9) return false;

        if (mode == "WAVE")
        {
            return cleaned.StartsWith("77") || cleaned.StartsWith("78") || 
                   cleaned.StartsWith("76") || cleaned.StartsWith("70");
        }
        else if (mode == "OM")
        {
            return cleaned.StartsWith("77") || cleaned.StartsWith("78") || 
                   cleaned.StartsWith("76") || cleaned.StartsWith("70");
        }

        return true;
    }

    private string GenererReference(string mode)
    {
        var prefix = mode == "WAVE" ? "WV" : "OM";
        var timestamp = DateTime.Now.ToString("yyyyMMddHHmmss");
        var random = new Random().Next(1000, 9999);
        return $"{prefix}{timestamp}{random}";
    }
}
