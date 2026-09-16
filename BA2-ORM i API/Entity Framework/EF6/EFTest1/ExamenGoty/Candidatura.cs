using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;

namespace ExamenGOTY
{
    public enum CategoriaGOTY
    {
        MillorJoc,
        MillorNarrativa,
        MillorDireccioArt,
        MillorBandaSonora,
        MillorMultijugador,
        MillorJocIndependent
    }

    public class Candidatura
    {
        public int Id { get; set; }

        [Required]
        [MaxLength(100)]
        public string NomJoc { get; set; }

        [Required]
        public CategoriaGOTY Categoria { get; set; }

        public int Any { get; set; }

        public bool HaGuanyat { get; set; }

        public int VotsPublic { get; set; }

        // Relació 1-1 amb Representant (FK requerida)
        public int RepresentantId { get; set; }
        public virtual Representant Representant { get; set; }

        // Relació N-N recursiva amb Candidatura
        // Important: En EF6 necessitem dues propietats per la recursiva
        public virtual ICollection<Candidatura> CandidaturesRelacionades { get; set; }
        public virtual ICollection<Candidatura> CandidaturesQueEmRelacionen { get; set; }

        public Candidatura()
        {
            CandidaturesRelacionades = new List<Candidatura>();
            CandidaturesQueEmRelacionen = new List<Candidatura>();
        }
    }
}
