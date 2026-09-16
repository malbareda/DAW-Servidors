using System.Collections;
using System.Collections.Generic;

namespace EFTest1
{
    public class Equip
    {
        public int Id { get; set; }
        public string Nom { get; set; }
        public int AnyFundacio { get; set; }
        public int Pressupost { get; set; }

        public virtual ICollection<Jugador> Jugadors { get; set; }
        public virtual ICollection<Torneig> Torneigs { get; set; }
    }
}
