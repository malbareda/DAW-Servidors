// Curs.cs
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

// Aquests namespaces són necessaris per les anotacions de validació i mapping
using System.ComponentModel.DataAnnotations.Schema;
using System.ComponentModel.DataAnnotations;

namespace EFTest1
{
    // Aquesta classe representa un curs acadèmic
    // Entity Framework la convertirà en una taula a la base de dades
    public class Curs
    {
        // Entity Framework utilitza convencions per detectar claus primàries:
        // - Si una propietat es diu "ID" o "[NomClasse]ID"
        // - Si és de tipus numèric (int, long, etc.)
        // Llavors es considera clau primària i autoincremental
        public int CursID { get; set; }

        // Propietat booleana per indicar si el curs està actiu
        public bool Actiu { get; set; }

        // [Required] indica que aquest camp no pot ser null a la base de dades
        // Equivalent a NOT NULL en SQL
        [Required]
        public int Any { get; set; }

        // Enum que representa el tipus de cicle formatiu
        // Es guardarà com un número a la base de dades per defecte
        // Es pot canviar el comportament amb [EnumDataType] si es vol guardar com string
        public CicleFormatiu Cicle { get; set; }

        // Relació 1-N amb Modul
        // Un curs pot tenir molts mòduls
        // La propietat virtual permet el lazy loading
        public ICollection<Modul> Moduls { get; set; }

        // Relació 1-1 amb Professor
        // Un curs té exactament un professor
        // 'virtual' permet lazy loading - el professor es carrega només quan s'accedeix
        public virtual Professor Professor { get; set; }
    }

    // Enum que defineix els possibles tipus de cicles formatius
    // Es pot decorar amb [Flags] si es volen permetre combinacions
    public enum CicleFormatiu
    {
        DAM,    // Desenvolupament d'Aplicacions Multiplataforma
        ASIX,   // Administració de Sistemes Informàtics en Xarxa
        DAMVI   // Desenvolupament d'Aplicacions Web
    }
}
