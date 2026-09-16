using MiniEjemploAPI;
using System.ComponentModel.DataAnnotations;

namespace MiniEjemploAPI
{
    public class Pokemon
    {
        public int PokemonID { get; set; }

        [Required]
        [MaxLength(50)]
        public string Nom { get; set; } = string.Empty;

        [Required]
        public TipusPokemon Tipus { get; set; }

        public int Nivell { get; set; } = 5;

        public int PuntsVida { get; set; } = 50;

        public int PuntsVidaMaxims { get; set; } = 50;

        public int Experiencia { get; set; } = 0;

        public bool EstaDebilitat { get; set; } = false;

        // Navegació: Aquest Pokemon pertany a un Entrenador
        public virtual Entrenador? Entrenador { get; set; }
    }

    public enum TipusPokemon
    {
        FOC,
        AIGUA,
        PLANTA,
        ELECTRIC,
        NORMAL
    }
}