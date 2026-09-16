using System.ComponentModel.DataAnnotations;
using System.Reflection;

namespace WebApplication1
{
    public class CursOld
    {
        public int CursID { get; set; }
        public bool Actiu { get; set; }

        [Required]
        public int Any { get; set; }

        public CicleFormatiu Cicle { get; set; }

        // Relació 1-N amb Modul
        public virtual ICollection<Modul> Moduls { get; set; } = new List<Modul>();

        // Relació 1-1 amb Professor
        public virtual Professor? Professor { get; set; }
    }

    public enum CicleFormatiuOld
    {
        DAM,    // Desenvolupament d'Aplicacions Multiplataforma
        ASIX,   // Administració de Sistemes Informàtics en Xarxa
        DAMVI   // Desenvolupament d'Aplicacions Web
    }
}