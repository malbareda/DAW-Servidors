using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using System.Reflection;
using WebApplication1;

namespace WebApplication1
{
    [Table("Professor")]
    public class ProfessorOld
    {
        public int ProfessorID { get; set; }

        [Required]
        public string Nom { get; set; } = string.Empty;

        [Required]
        public string Cognom { get; set; } = string.Empty;

        [Column("hores_assignades")]
        public int HoresAssignades { get; set; }

        // Relació N-N amb Modul
        public virtual ICollection<Modul> Moduls { get; set; } = new List<Modul>();

        // Relació 1-1 amb Curs
        public virtual Curs? Curs { get; set; }
    }
}