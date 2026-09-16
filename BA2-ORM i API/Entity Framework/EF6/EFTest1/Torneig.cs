using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace EFTest1
{
    public class Torneig
    {
        public int Id { get; set; }
        public string Nom { get; set; }
        public DateTime DataInici { get; set; }
        public DateTime DataFi { get; set; }

        public virtual ICollection<Equip> Equips { get; set; } = new List<Equip>();

        public Torneig() { 
            Equips = new List<Equip>();
        }


    }

}
