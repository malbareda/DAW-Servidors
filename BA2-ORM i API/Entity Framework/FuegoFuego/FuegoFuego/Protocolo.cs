using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace FuegoFuego
{
    public class Protocolo
    {
        public int ProtocoloID { get; set; }
        public string ProtocoloName { get; set; }

        // RELACIÓ 1-N: Protocolo (1) ←→ (N) Alarmas
        // 
        // AQUESTA ÉS LA PART "1" de la relació 1-N
        // Per tant és una ICollection (col·lecció d'Alarmes)
        // 
        // ⚠️ VIRTUAL és CRÍTIC aquí:
        // 
        // AMB VIRTUAL (Lazy Loading):
        // - EF crea un proxy que carrega Alarmas automàticament quan hi accedim
        // - Només funciona si el context està obert
        // - Permet: var p = ctx.Protocolo.Find(1); p.Alarmas.First(); ✓
        // 
        // SENSE VIRTUAL (Eager Loading obligatori):
        // - EF NO pot carregar automàticament
        // - Alarmas serà NULL si no fem Include()
        // - Obliga a fer: ctx.Protocolo.Include(p => p.Alarmas).Find(1)
        // 
        // DECISIÓ DE DISSENY:
        // - Fer-ho virtual: més flexible però pot causar N+1 queries
        // - No fer-ho virtual: més explícit i controlat però menys còmode
        public virtual ICollection<Alarma> Alarmas { get; set; }

        // CONSTRUCTOR: Inicialitzem la col·lecció per evitar NULL
        // Això és una bona pràctica independentment de si és virtual o no
        public Protocolo()
        {
            Alarmas = new HashSet<Alarma>();
        }
    }
}