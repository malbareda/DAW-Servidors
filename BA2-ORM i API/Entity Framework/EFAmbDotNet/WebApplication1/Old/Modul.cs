using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using WebApplication1;

namespace WebApplication1
{
    public class ModulOld
    {
        [Key]
        public int Codi { get; set; }

        [Required]
        public string Nom { get; set; } = string.Empty;

        [Column("hores_setmana")]
        public int HoresSetmana { get; set; }

        public DateTime DataInici { get; set; }

        // Relació 1-N amb Curs
        public virtual Curs? Curs { get; set; }

        // Relació N-N amb Professor
        public virtual ICollection<Professor> Professors { get; set; } = new List<Professor>();

        // Relació N-N recursiva: Prerequisites
        public virtual ICollection<Modul> Prerequisits { get; set; } = new List<Modul>();
        public virtual ICollection<Modul> EsPrerequisitDe { get; set; } = new List<Modul>();
    }
}