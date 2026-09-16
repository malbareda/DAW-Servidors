using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace WebApplication1
{
    [Table("Professor")]
    public class Professor
    {
        public int ProfessorID { get; set; }

        [Required]
        public string nom { get; set; }

        [Required]
        public string cognom { get; set; }

        [Column("hores_assignades")]
        public int horesAssignades { get; set; }

        // MANTENIM 'virtual' per lazy loading automàtic
        public virtual ICollection<Modul> Moduls { get; set; }

        // MANTENIM 'virtual' per lazy loading automàtic
        public virtual Curs Curs { get; set; }

        public Professor()
        {
            Moduls = new HashSet<Modul>();
            horesAssignades = 0;
        }
    }
}