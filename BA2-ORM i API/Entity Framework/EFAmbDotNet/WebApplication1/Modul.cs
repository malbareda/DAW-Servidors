using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using System.Data.SqlTypes;
using System.Text;
using System.Threading.Tasks;

namespace WebApplication1
{
    // Aquesta classe representa un mòdul formatiu
    [Table("Modul")]
    public class Modul
    {
        [Key]
        [DatabaseGenerated(DatabaseGeneratedOption.None)]  // Deshabilita autoincrement (perque si no ho intenta fer tot per autoincrement)
        public int Codi { get; set; }

        [Required]
        public string nom { get; set; }

        [Column("hores_setmana")]
        public int horesSetmana { get; set; }

        public DateTime dataInici { get; set; }

        // Relació 1-N amb Curs
        public virtual Curs Curs { get; set; } //Aixo significa que no pot ser null

        // Relació N-N amb Professor
        public virtual ICollection<Professor> Professors { get; set; }

        // ============================================================
        // RELACIÓ N-N RECURSIVA: Modul ←→ Modul (Prerequisites)
        // ============================================================

        // PREREQUISITS: Assignatures que cal haver aprovat abans d'aquesta
        // 
        // Exemple conceptual:
        // - "Accés a Dades" té com a prerequisits: "Programació" i "Bases de Dades"
        // - Per poder cursar M6, primer has d'haver fet M3 i M2
        //
        // Aquesta és la "part activa" de la relació:
        // M6.Prerequisits → [M3, M2]  (M6 necessita M3 i M2)
        //
        // ⚠️ VIRTUAL és CRÍTIC aquí per Lazy Loading:
        // - Amb virtual: var m6 = ctx.Modul.Find(6); m6.Prerequisits.ToList(); ✓
        // - Sense virtual: Cal fer Include: ctx.Modul.Include(m => m.Prerequisits).Find(6);
        //
        // La recursivitat permet que un Modul apunti a altres Moduls de la mateixa taula
        public virtual ICollection<Modul> Prerequisits { get; set; }

        // ES PREREQUISIT DE: Assignatures que tenen aquesta com a prerequisit
        //
        // Aquesta és la "part passiva" o "inversa" de la relació:
        // M3.EsPrerequisitDe → [M6, M7]  (M3 és prerequisit de M6 i M7)
        //
        // IMPORTANT: Aquesta propietat és l'INVERSA de Prerequisits
        // - Si M6.Prerequisits conté M3 → Automàticament M3.EsPrerequisitDe conté M6
        // - EF manté aquesta sincronització automàticament
        // - És una navegació bidireccional de la mateixa relació
        //
        // Exemple pràctic:
        // m6.Prerequisits.Add(m3);  →  EF automàticament fa: m3.EsPrerequisitDe.Add(m6)
        //
        // Utilitat: Permet preguntes com "Quines assignatures depenen d'aquesta?"
        // - "Si trec Programació del pla, quines assignatures queden sense prerequisit?"
        // - "Quin impacte té eliminar aquest mòdul?"
        //
        // ⚠️ També és VIRTUAL per les mateixes raons que Prerequisits
        public virtual ICollection<Modul> EsPrerequisitDe { get; set; }

        // ============================================================
        // TAULA INTERMÈDIA: ModulPrerrequisits
        // ============================================================
        // EF crea automàticament una taula amb aquesta estructura:
        //
        // ModulPrerrequisits
        // ├─ ModulID (FK → Modul.Codi)          // L'assignatura que necessita prerequisits
        // └─ PrerequisitID (FK → Modul.Codi)    // L'assignatura que és prerequisit
        //
        // Exemple de dades:
        // | ModulID | PrerequisitID |  Significat
        // |---------|---------------|------------------------------------------
        // |    6    |      3        |  M6 (Accés Dades) necessita M3 (Programació)
        // |    6    |      2        |  M6 (Accés Dades) necessita M2 (BD)
        // |    7    |      3        |  M7 (Interfícies) necessita M3 (Programació)
        //
        // Nota: Ambdues FK apunten a la MATEIXA taula (Modul), d'aquí la recursivitat

        // Constructor que inicialitza les col·leccions
        public Modul()
        {
            this.Professors = new HashSet<Professor>();
            this.dataInici = (DateTime.Now);

            // IMPORTANT: Inicialitzar les col·leccions recursives per evitar NULL
            // Fins i tot si és virtual, és bona pràctica inicialitzar-les
            this.Prerequisits = new HashSet<Modul>();
            this.EsPrerequisitDe = new HashSet<Modul>();
        }
    }
}
