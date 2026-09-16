using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace MiniEjemploAPI
{
    [Table("Entrenador")]
    public class Entrenador
    {
        public int EntrenadorID { get; set; }

        [Required]
        [MaxLength(50)]
        public string Nom { get; set; } = string.Empty; //""

        [Required]
        [MaxLength(100)]
        public string Cognoms { get; set; } = string.Empty;

        public int Medalles { get; set; } = 0;

        public int DinersPokeDollars { get; set; } = 0;

        // Relació 1-N: Un entrenador té molts Pokemon
        public virtual ICollection<Pokemon> Pokemons { get; set; } = new List<Pokemon>();
    }
}
