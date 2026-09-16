using System;
using System.Collections.Generic;
using System.Linq;

namespace EFCoreTest1
{
    class Program
    {
        static void Main(string[] args)
        {
            using (var ctx = new ProvaContext())
            {
                // Equivalent a Database.SetInitializer(new DropCreateDatabaseAlways<ProvaContext>());
                ctx.Database.EnsureDeleted();  // Elimina la BD si existeix
                ctx.Database.EnsureCreated();  // Crea la BD des de zero

                // Equivalent a Update (no me'n recordo com es diu);
                //ctx.Database.EnsureCreated();

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

                // Assigna els mòduls al curs
                segonDvi.Moduls.Add(m6);
                segonDvi.Moduls.Add(m3);
                segonDvi.Moduls.Add(m2);
                segonDvi.Moduls.Add(m7);

                marc.Moduls.Add(m6);
                marc.Moduls.Add(m3);
                grego.Moduls.Add(m2);
                grego.Moduls.Add(m7);

                Console.WriteLine("INSERINT LA RECURSIVITAT");

                // Relacions recursives (EXACTAMENT IGUAL que EF6)
                m6.Prerequisits.Add(m3);
                m7.Prerequisits.Add(m3);
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

                // READ - Lectura d'entitats (EXACTAMENT IGUAL que EF6)
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
                // AMB LAZY LOADING ACTIVAT, això funciona IGUAL que EF6
                Curs c = ctx.Curs.Find(1);
                Console.WriteLine(c.Professor.nom);

                Console.WriteLine("\n=== NAVEGACIÓ COMPLEXA: PROFESSOR -> CURS -> CICLE ===");
                Console.WriteLine("Mostrem el cicle on ensenya el GregMarc");
                // AMB LAZY LOADING ACTIVAT, això funciona IGUAL que EF6
                Professor m = ctx.Professor.Where(p => p.nom == "GregMarc").FirstOrDefault();
                Console.WriteLine("GregMarc ensenya a " + m.Curs.Cicle);

                Console.WriteLine("RECURSIVITAAAAAAT");

                // ============================================================
                // NAVEGACIÓ DE LA RELACIÓ RECURSIVA: PREREQUISITS
                // ============================================================

                // AMB LAZY LOADING ACTIVAT, això funciona EXACTAMENT IGUAL que EF6
                Modul mrec = ctx.Modul.Where(p => p.nom == "Acces a Dades").First();

                Console.WriteLine("ELS PRERREQUISITS D'ACCES A DADES SON: ");

                // El lazy loading carrega automàticament Prerequisits quan accedim
                foreach (Modul preq in mrec.Prerequisits)
                {
                    Console.WriteLine(preq.Codi + ": " + preq.nom);
                }

                // ============================================================
                // NAVEGACIÓ INVERSA DE LA RELACIÓ RECURSIVA
                // ============================================================

                // AMB LAZY LOADING ACTIVAT, això funciona EXACTAMENT IGUAL que EF6
                Modul mrec2 = ctx.Modul.Where(p => p.nom == "Programacio").First();

                Console.WriteLine("PROGRAMACIO ES PREREQUISIT DE: ");

                // El lazy loading carrega automàticament EsPrerequisitDe quan accedim
                foreach (Modul preq in mrec2.EsPrerequisitDe)
                {
                    Console.WriteLine(preq.Codi + ": " + preq.nom);
                }

                Console.WriteLine("\nPrem ENTER per sortir...");
                Console.ReadLine();
            }
        }
    }
}