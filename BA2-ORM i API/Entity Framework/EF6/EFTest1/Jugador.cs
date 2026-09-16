using System;

namespace EFTest1
{
    public class Jugador
    {
        public int Id { get; set; }
        public string Nom { get; set; }
        public DateTime DataRegistre { get; set; }
        public bool EstaActiu { get; set; }
        public int Victories { get; set; }

        public virtual Equip Equip { get; set; }
    }
}
