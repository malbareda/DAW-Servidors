using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace EFTest1
{
    public class TorneigService
    {
        private TorneigConext _context;
        public TorneigService() { 
            _context = new TorneigConext();
        }

        List<Jugador> GetJugadorsRegistratsAbans(DateTime data)
        {

            List<Jugador> l = _context.Jugador.ToList();
            List<Jugador> l2 = new List<Jugador>();
            foreach (Jugador litem in l)
            {
                if(litem.DataRegistre<data)l2.Add(litem);
            }return l2;

            return _context.Jugador.Where(j=>j.DataRegistre<data).ToList();
        }

        void MostraJugadorsEnTorneig(int idTorneig)
        {
            Torneig t = _context.Torneig.Find(idTorneig);
            foreach(Equip eq in t.Equips)
            {
                foreach(Jugador j in eq.Jugadors)
                {
                    if(j.EstaActiu) Console.WriteLine(j.Nom +" "+j.DataRegistre);
                }
            }
        }

        bool TancarTorneig(int idTorneig)
        {
            Torneig t = _context.Torneig.Find(idTorneig);
            if (t == null) return false;
            if (t.DataFi != null) return false;
            t.DataFi = DateTime.Now;
            foreach (Equip eq in t.Equips)
            {
                foreach (Jugador j in eq.Jugadors)
                {
                    j.Victories++;
                }
            }
            _context.SaveChanges();
            return true;

        }

        void OldMoney(int any, int diners)
        {
            List<Equip> old = _context.Equip.Where(e => e.AnyFundacio < any && e.Pressupost > diners).ToList();
            


            List<Equip> old2 = _context.Equip.Where
                    (e => e.AnyFundacio < any && 
                    e.Jugadors.Any(j => j.Nom == "Vinicius"))
                .ToList();



            foreach (Equip eq in old)
            {
                Console.WriteLine(eq.Nom);
            }
        }

    }
}
