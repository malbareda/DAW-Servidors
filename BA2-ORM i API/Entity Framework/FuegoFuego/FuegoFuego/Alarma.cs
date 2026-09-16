using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace FuegoFuego
{
    public class Alarma
    {
        public int AlarmaID { get; set; }
        public string Name { get; set; }
        public int tonos { get; set; }

        // RELACIÓ 1-N: Protocolo (1) ←→ (N) Alarmas
        // 
        // AQUESTA ÉS LA PART "N" de la relació
        // Per tant referencia al "1" (un sol Protocolo)
        // 
        // INFERÈNCIA DE RELACIONS PER CONVENCIÓ:
        // EF detecta automàticament la relació perquè:
        // 1. La propietat es diu igual que la classe: "Protocolo"
        // 2. Protocolo té una ICollection<Alarma> amb el nom "Alarmas"
        // 3. EF assumeix que hi ha una FK ProtocoloID (encara que no la definim explícitament)
        // 
        // NO és virtual aquí:
        // - Eager loading: cal fer Include(a => a.Protocolo) per carregar-ho
        // - Més predictible en rendiment
        // - Evita càrregues automàtiques inesperades
        public Protocolo Protocolo { get; set; }

        // RELACIÓ 1-1: Alarma (1) ←→ (1) Tono
        // 
        // SÍ és VIRTUAL aquí!
        // - Lazy loading activat: Tono es carrega automàticament quan hi accedim
        // - Exemple: var t = alarma.Tono; // EF carrega Tono si cal
        // 
        // COMPARACIÓ:
        // Amb virtual:    var tono = alarma.Tono;  // Funciona si context obert
        // Sense virtual:  var alarma = ctx.Alarma.Include(a => a.Tono).Find(1);
        // 
        // DECISIÓ: Aquí és virtual perquè:
        // - És una relació 1-1 (no afecta tant el rendiment)
        // - Més còmode per accedir al Tono
        // - En producció, avaluar si val la pena el trade-off
        public virtual Tono Tono { get; set; }
    }
}