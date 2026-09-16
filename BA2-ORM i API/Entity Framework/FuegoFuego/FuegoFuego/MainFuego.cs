using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Data.Entity;

namespace FuegoFuego
{
    public class MainFuego
    {
        public static void Main(string[] args)
        {
            // PRIMER CONTEXT: Creació i inserció de dades
            using (var ctx = new FuegoContext())
            {
                Console.WriteLine("empieza la magia ");

                // Creem les entitats relacionades
                Tono t = new Tono() { TonoDescripcio = "Tres tonos largos - largo" };
                Alarma evac = new Alarma() { Name = "Alarma evacuacio", tonos = 3, Tono = t };
                Protocolo iesSab = new Protocolo() { ProtocoloName = "Protocol IES SABADELL" };

                // Afegim l'alarma al protocol (relació 1-N)
                iesSab.Alarmas.Add(evac);

                // Guardem tot a la BD
                ctx.Alarma.Add(evac);
                ctx.Tono.Add(t);
                ctx.Protocolo.Add(iesSab);
                ctx.SaveChanges();

                // AQUÍ FUNCIONA: Estem dins del mateix context que ha carregat les dades
                // El Lazy Loading està actiu i pot carregar Alarmas quan accedim a la propietat
                Protocolo asa = ctx.Protocolo.Find(1);
                Console.WriteLine(asa.Alarmas.First().Name);
                // Funciona perquè Alarmas és VIRTUAL i el context segueix obert
            }

            // SEGON CONTEXT: Demostració d'Eager vs Lazy Loading
            using (var ctx2 = new FuegoContext())
            {
                // AIXÒ PETARIA SI ALARMAS NO FOS VIRTUAL:
                // Protocolo asa = ctx2.Protocolo.Find(1);
                // Console.WriteLine(asa.Alarmas.First().Name); // NULL REFERENCE!

                // EXPLICACIÓ DEL PROBLEMA:
                // 1. Find() carrega només l'entitat Protocolo
                // 2. Si Alarmas NO és virtual → És EAGER loading
                //    - EF no pot fer proxy per carregar-ho automàticament
                //    - Alarmas queda NULL
                // 3. Si Alarmas ÉS virtual → És LAZY loading
                //    - EF crea un proxy que carrega Alarmas quan s'accedeix
                //    - Però necessita el context obert!

                // SOLUCIÓ: Usar Include() per EAGER LOADING EXPLÍCIT
                // Include() força la càrrega de la relació en la consulta inicial
                Protocolo asa = ctx2.Protocolo.Include(p => p.Alarmas).First();

                // Ara Alarmas està carregat i podem accedir-hi sense problemes
                Console.WriteLine(asa.Alarmas.First().Name);

                // ALTERNATIVA (si el context segueix obert i Alarmas és virtual):
                // Protocolo asa = ctx2.Protocolo.Find(1);
                // ctx2.Entry(asa).Collection(p => p.Alarmas).Load(); // Càrrega explícita
                // Console.WriteLine(asa.Alarmas.First().Name);
            }
        }
    }
}