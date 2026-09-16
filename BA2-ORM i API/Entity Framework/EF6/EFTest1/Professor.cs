// Professor.cs
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

using System.ComponentModel.DataAnnotations.Schema;
using System.ComponentModel.DataAnnotations;

namespace EFTest1
{
    // [Table] permet especificar el nom de la taula a la base de dades
    // Si no s'especifica, s'usa el nom de la classe
    [Table("Professor")]
    public class Professor
    {
        // Clau primària autoincremental
        // Es pot decorar amb [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
        // per fer-ho més explícit
        public int ProfessorID { get; set; }


        [Required]
        // Es podria afegir [StringLength(50)] per limitar la longitud
        // [MinLength(2)] per longitud mínima
        // [RegularExpression(@"^[A-Z].*")] per validar format
        public string nom { get; set; }

        [Required]
        public string cognom { get; set; }

        [Column("hores_assignades")]
        // Es podria afegir [Range(0, 40)] per validar rang
        public int horesAssignades { get; set; }

        // Relació N-N amb Modul
        // La 's' al final (Moduls) és important per la convenció
        public virtual ICollection<Modul> Moduls { get; set; }

        // Relació 1-1 amb Curs
        // La configuració específica es fa al Context
        public virtual Curs Curs { get; set; }

        public Professor()
        {
            this.Moduls = new HashSet<Modul>();
            this.horesAssignades = 0;
        }
    }
}
