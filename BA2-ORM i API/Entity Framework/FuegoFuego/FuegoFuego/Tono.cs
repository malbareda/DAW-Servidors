using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations.Schema;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace FuegoFuego
{
    public class Tono
    {
        public int TonoID { get; set; }
        public string TonoDescripcio { get; set; }

        // RELACIÓ 1-1: Tono (1) ←→ (1) Alarma
        // 
        // CONFIGURACIÓ AMB DATA ANNOTATIONS (alternativa a Fluent API)
        // 
        // ORDRE IMPORTANT en relacions 1-1:
        // 1r. Definir la Foreign Key (quin costat té la FK)
        // 2n. Definir la propietat de navegació
        // 
        // [ForeignKey("Alarma")] indica que:
        // - AlarmaID és la clau forana que apunta a Alarma
        // - "Alarma" és el nom de la propietat de navegació
        // - TONO conté la FK → és la taula dependent
        // 
        // ⚠️ NOTA: AlarmaID hauria de ser una propietat, no un camp!
        // Millor: public int AlarmaID { get; set; }
        [ForeignKey("Alarma")]
        public int AlarmaID;

        // PROPIETAT DE NAVEGACIÓ
        // 
        // Permet navegar de Tono → Alarma
        // Exemple: var alarma = tono.Alarma;
        // 
        // NO és virtual aquí (decisió de disseny):
        // - Si fos virtual: Lazy loading (carrega Alarma automàticament)
        // - Sense virtual: Eager loading obligatori (cal Include)
        // 
        // En relacions 1-1 és menys crític que en 1-N perquè:
        // - Només es carrega 1 entitat, no una col·lecció
        // - Menys impacte en rendiment
        public Alarma Alarma { get; set; }

        public Tono() { }
    }
}