using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace EFTest1
{
    class Program
    {
        static void Main(string[] args)
        {
            using (var ctx = new ProvaContext())
            {
                // CREATE - Creació d'entitats
                var marc = new Professor() { nom = "Marc", cognom = "Albareda" };
                var grego = new Professor() { nom = "Gregorio", cognom = "Santamaria" };
                var m6 = new Modul() { Codi = 6, nom = "Acces a Dades", horesSetmana = 4 };
                var m3 = new Modul() { Codi = 3, nom = "Programacio", horesSetmana = 7 };
                var m2 = new Modul() { Codi = 2, nom = "Bases de Dades", horesSetmana = 4 };
                var m7 = new Modul() { Codi = 7, nom = "Interficies", horesSetmana = 4 };
                var segonDvi = new Curs()
                {
                    Cicle = CicleFormatiu.DAMVI,
                    Actiu = true,
                    Any = 2024,
                    Professor = marc
                };

                marc.Moduls.Add(m6);
                marc.Moduls.Add(m3);
                grego.Moduls.Add(m2);
                grego.Moduls.Add(m7);

                Console.WriteLine("INSERINT LA RECURSIVITAT");

                // ============================================================
                // CREACIÓ DE LA RELACIÓ RECURSIVA: PREREQUISITS
                // ============================================================

                // M6 (Accés a Dades) necessita M3 (Programació) com a prerequisit
                // Aquesta línia fa DUES coses automàticament:
                // 1. Afegeix M3 a la col·lecció M6.Prerequisits
                // 2. EF automàticament afegeix M6 a M3.EsPrerequisitDe (navegació inversa)
                m6.Prerequisits.Add(m3);

                // M7 (Interfícies) també necessita M3 (Programació)
                // Ara M3.EsPrerequisitDe contindrà: [M6, M7]
                m7.Prerequisits.Add(m3);

                // M6 (Accés a Dades) també necessita M2 (Bases de Dades)
                // M6 té dos prerequisits: M3 i M2
                // M6.Prerequisits ara conté: [M3, M2]
                m6.Prerequisits.Add(m2);

                ctx.Modul.Add(m6);
                ctx.Modul.Add(m7);
                ctx.Modul.Add(m3);
                ctx.Modul.Add(m2);
                ctx.Professor.Add(marc);
                ctx.Professor.Add(grego);
                ctx.Curs.Add(segonDvi);

                ctx.SaveChanges();
                Console.WriteLine("\n=== RESULTAT DE LA INSERCIÓ DE DADES ===");
                Console.WriteLine("Tot ha sortit a pedir de Milhouse");

                // READ - Lectura d'entitats
                List<Professor> listg = ctx.Professor.ToList();

                Console.WriteLine("\n=== LLISTAT DE COGNOMS DE PROFESSORS ===");
                Console.WriteLine("Hauria de mostrar: Albareda, Santamaria");
                foreach (Professor p in listg)
                {
                    Console.WriteLine(p.cognom);
                    if (p.nom == "Marc")
                    {
                        Console.WriteLine("guapo");
                    }
                }

                Console.WriteLine("\n=== NOMBRE TOTAL DE PROFESSORS ===");
                Console.WriteLine("Hauria de ser 2");
                Console.WriteLine(ctx.Professor.Count());

                Console.WriteLine("\n=== CERCA DE PROFESSOR PER COGNOM (WHERE) ===");
                Console.WriteLine("Hauria de trobar: Albareda");
                Professor test1 = ctx.Professor
                                    .Where(p => p.cognom == "Albareda")
                                    .FirstOrDefault();
                Console.WriteLine(test1.cognom);

                Console.WriteLine("\n=== CERCA DE PROFESSOR PER ID (FIND) ===");
                Console.WriteLine("Hauria de trobar: Santamaria (ID=2)");
                Professor test2 = ctx.Professor.Find(2);
                Console.WriteLine(test2.cognom);

                Console.ReadLine();

                Console.WriteLine("\n=== ACTUALITZACIÓ DEL NOM D'UN PROFESSOR ===");
                Console.WriteLine("Canviem Marc a GregMarc");
                test1.nom = "GregMarc";
                ctx.SaveChanges();

                Console.WriteLine("\n=== COMPROVACIÓ SI TOTS ELS NOMS COMENCEN PER 'G' ===");
                Console.WriteLine("Hauria de ser True perquè tenim GregMarc i Gregorio");
                bool flag = ctx.Professor.All(p => p.nom.StartsWith("G"));
                Console.WriteLine(flag);

                Console.WriteLine("\n=== ELIMINACIÓ D'UN PROFESSOR ===");
                Console.WriteLine("Eliminem el professor Santamaria");
                ctx.Professor.Remove(test2);
                ctx.SaveChanges();

                Console.WriteLine("............");

                Console.WriteLine("\n=== NAVEGACIÓ 1-1: CURS -> PROFESSOR ===");
                Console.WriteLine("Mostrem el nom del professor del primer curs");
                Curs c = ctx.Curs.Find(1);
                Console.WriteLine(c.Professor.nom);

                Console.WriteLine("\n=== NAVEGACIÓ COMPLEXA: PROFESSOR -> CURS -> CICLE ===");
                Console.WriteLine("Mostrem el cicle on ensenya el GregMarc");
                Professor m = ctx.Professor.Where(p => p.nom == "GregMarc").FirstOrDefault();
                Console.WriteLine("GregMarc ensenya a " + m.Curs.Cicle);

                Console.WriteLine("RECURSIVITAAAAAAT");

                // ============================================================
                // NAVEGACIÓ DE LA RELACIÓ RECURSIVA: PREREQUISITS
                // ============================================================

                // Obtenim el mòdul M6 (Accés a Dades)
                Modul mrec = ctx.Modul.Where(p => p.nom == "Acces a Dades").First();

                Console.WriteLine("ELS PRERREQUISITS D'ACCES A DADES SON: ");

                // LAZY LOADING EN ACCIÓ (perquè Prerequisits és VIRTUAL):
                // Quan accedim a mrec.Prerequisits, EF automàticament:
                // 1. Detecta que la col·lecció no està carregada
                // 2. Fa una consulta a ModulPrerrequisits: 
                //    SELECT * FROM ModulPrerrequisits WHERE ModulID = 6
                // 3. Obté els PrerequisitID: [3, 2]
                // 4. Fa una consulta a Modul:
                //    SELECT * FROM Modul WHERE Codi IN (3, 2)
                // 5. Omple la col·lecció Prerequisits amb M3 i M2
                //
                //SI PREREQUISITS NO FOS VIRTUAL:
                // - mrec.Prerequisits seria NULL
                // - El foreach petaria amb NullReferenceException
                // - Caldria fer: ctx.Modul.Include(m => m.Prerequisits).Where(...).First()
                foreach (Modul preq in mrec.Prerequisits)
                {
                    // Sortida esperada:
                    // 3: Programacio
                    // 2: Bases de Dades
                    Console.WriteLine(preq.Codi + ": " + preq.nom);
                }

                // ============================================================
                // NAVEGACIÓ INVERSA DE LA RELACIÓ RECURSIVA
                // ============================================================

                // Obtenim el mòdul M3 (Programació)
                Modul mrec2 = ctx.Modul.Where(p => p.nom == "Programacio").First();

                Console.WriteLine("PROGRAMACIO ES PREREQUISIT DE: ");

                // NAVEGACIÓ INVERSA amb LAZY LOADING:
                // Quan accedim a mrec2.EsPrerequisitDe, EF:
                // 1. Consulta ModulPrerrequisits buscant on PrerequisitID = 3
                //    SELECT * FROM ModulPrerrequisits WHERE PrerequisitID = 3
                // 2. Obté els ModulID que depenen de M3: [6, 7]
                // 3. Carrega els mòduls:
                //    SELECT * FROM Modul WHERE Codi IN (6, 7)
                // 4. Retorna M6 (Accés a Dades) i M7 (Interfícies)
                //
                // Aquesta navegació permet respondre preguntes com:
                // - "Si elimino Programació, quins mòduls queden sense prerequisit?"
                // - "Quin impacte té modificar aquest mòdul?"
                //
                //SI ESPREQUISITDE NO FOS VIRTUAL:
                // - mrec2.EsPrerequisitDe seria NULL
                // - Caldria: ctx.Modul.Include(m => m.EsPrerequisitDe).Where(...).First()
                foreach (Modul preq in mrec2.EsPrerequisitDe)
                {
                    // Sortida esperada:
                    // 6: Acces a Dades
                    // 7: Interficies
                    Console.WriteLine(preq.Codi + ": " + preq.nom);
                }

                Console.WriteLine("\nPrem ENTER per sortir...");
                Console.ReadLine();
            }
        }
    }
}