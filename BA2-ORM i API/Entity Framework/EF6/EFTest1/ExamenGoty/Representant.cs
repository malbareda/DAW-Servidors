using System.ComponentModel.DataAnnotations;

namespace ExamenGOTY
{
    public class Representant
    {
        public int Id { get; set; }

        [Required]
        [MaxLength(80)]
        public string Nom { get; set; }

        [Required]
        [MaxLength(60)]
        public string Carrec { get; set; }

        [Required]
        [MaxLength(80)]
        public string Estudi { get; set; }

        // Relació 1-1 amb Candidatura
        public virtual Candidatura Candidatura { get; set; }
    }
}