using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;

namespace WebApplication1
{
    public class Curs
    {
        //Infereix automaticament que és un autonumèric
        public int CursID { get; set; }

        public bool Actiu { get; set; }

        [Required]
        public int Any { get; set; }

        public CicleFormatiu Cicle { get; set; }

        public virtual ICollection<Modul> Moduls { get; set; }

        public virtual Professor? Professor { get; set; }

        public Curs()
        {
            Moduls = new HashSet<Modul>();
        }
    }

    public enum CicleFormatiu
    {
        DAM,
        ASIX,
        DAMVI
    }
}