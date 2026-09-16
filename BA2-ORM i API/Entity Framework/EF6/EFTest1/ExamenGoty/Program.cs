using System;
using System.Collections.Generic;
using System.Linq;

namespace ExamenGOTY
{
    class Program
    {
        static void Main(string[] args)
        {
            // EX2 - Seeding manual al Main
            using (var context = new GOTYContext())
            {
                // Eliminar i recrear BD
                if (context.Database.Exists())
                {
                    context.Database.Delete();
                }
                context.Database.Create();

                Console.WriteLine("=== Creant Base de Dades i fent Seeding ===\n");

                // Crear Representants
                var repr1 = new Representant
                {
                    Nom = "Christopher Larkin",
                    Carrec = "Compositor",
                    Estudi = "Team Cherry"
                };

                var repr2 = new Representant
                {
                    Nom = "Ari Gibson",
                    Carrec = "Director Artístic",
                    Estudi = "Team Cherry"
                };

                var repr3 = new Representant
                {
                    Nom = "William Pellen",
                    Carrec = "Productor Executiu",
                    Estudi = "Team Cherry"
                };

                var repr4 = new Representant
                {
                    Nom = "Guillaume Broche",
                    Carrec = "Director",
                    Estudi = "SandFall Interative"
                };

                var repr5 = new Representant
                {
                    Nom = "Masayuki Yamada",
                    Carrec = "Director",
                    Estudi = "Sony Interactive"
                };

                // Afegir representants al context
                context.Representants.Add(repr1);
                context.Representants.Add(repr2);
                context.Representants.Add(repr3);
                context.Representants.Add(repr4);
                context.Representants.Add(repr5);

                // Guardar per generar IDs
                context.SaveChanges();

                // Crear Candidatures
                var cand1 = new Candidatura
                {
                    NomJoc = "Hollow Knight: Silksong",
                    Categoria = CategoriaGOTY.MillorBandaSonora,
                    Any = 2025,
                    HaGuanyat = true,
                    VotsPublic = 15420,
                    RepresentantId = repr1.Id
                };

                var cand2 = new Candidatura
                {
                    NomJoc = "Hollow Knight: Silksong",
                    Categoria = CategoriaGOTY.MillorDireccioArt,
                    Any = 2025,
                    HaGuanyat = false,
                    VotsPublic = 18930,
                    RepresentantId = repr2.Id
                };

                var cand3 = new Candidatura
                {
                    NomJoc = "Hollow Knight: Silksong",
                    Categoria = CategoriaGOTY.MillorJoc,
                    Any = 2025,
                    HaGuanyat = false,
                    VotsPublic = 25600,
                    RepresentantId = repr3.Id
                };

                var cand4 = new Candidatura
                {
                    NomJoc = "Expedition 33",
                    Categoria = CategoriaGOTY.MillorJoc,
                    Any = 2025,
                    HaGuanyat = true,
                    VotsPublic = 32100,
                    RepresentantId = repr4.Id
                };

                var cand5 = new Candidatura
                {
                    NomJoc = "Astrobot",
                    Categoria = CategoriaGOTY.MillorJoc,
                    Any = 2024,
                    HaGuanyat = true,
                    VotsPublic = 8200,
                    RepresentantId = repr5.Id
                };

                // Afegir candidatures
                context.Candidatures.Add(cand1);
                context.Candidatures.Add(cand2);
                context.Candidatures.Add(cand3);
                context.Candidatures.Add(cand4);
                context.Candidatures.Add(cand5);

                // Guardar candidatures
                context.SaveChanges();

                // Crear relacions N-N entre candidatures
                // Candidatura 1 (Silksong BSO) relacionada amb 2 i 3
                cand1.CandidaturesRelacionades.Add(cand2);
                cand1.CandidaturesRelacionades.Add(cand3);

                // Candidatura 2 (Silksong Art) relacionada amb 1 i 3
                cand2.CandidaturesRelacionades.Add(cand1);
                cand2.CandidaturesRelacionades.Add(cand3);

                // Candidatura 3 (Silksong GOTY) relacionada amb 1 i 2
                cand3.CandidaturesRelacionades.Add(cand1);
                cand3.CandidaturesRelacionades.Add(cand2);

                // Guardar relacions
                context.SaveChanges();

                Console.WriteLine("✅ Seeding completat!\n");
            }

            // Ara executem els exercicis amb un nou context
            using (var context = new GOTYContext())
            {
                Console.WriteLine("=== EX3: Candidatures guanyadores des de 2024 ===");
                var guanyadores = GetGuanyadores(context, 2024);
                foreach (var c in guanyadores)
                {
                    Console.WriteLine($"- {c.NomJoc} ({c.Categoria}) - {c.Any}");
                }
                Console.WriteLine();

                Console.WriteLine("=== EX4: Candidatures relacionades amb Silksong BSO ===");
                MostraCandidaturesRelacionades(context, 1);
                Console.WriteLine();

                Console.WriteLine("=== EX5: Relacionar Expedition 33 amb Silksong GOTY ===");
                bool relacionat = RelacionarCandidatures(context, 4, 3);
                Console.WriteLine($"Relacionades: {relacionat}");
                Console.WriteLine();

                Console.WriteLine("=== EX6: Candidatures exitoses ===");
                var exitoses = CandidaturesExitoses(context);
                foreach (var c in exitoses)
                {
                    Console.WriteLine($"- {c.NomJoc} ({c.Categoria}) - {(c.HaGuanyat ? "GUANYADORA" : "Relacionada amb guanyadora")}");
                }
                Console.WriteLine();

                Console.WriteLine("=== EX7: Estudis amb més de 30000 vots totals ===");
                TopEstudis(context, 30000);
                Console.WriteLine();
            }

            Console.WriteLine("\nPrem qualsevol tecla per sortir...");
            Console.ReadKey();
        }

        // EX3 (1p)
        static List<Candidatura> GetGuanyadores(GOTYContext context, int anyMinim)
        {
            return context.Candidatures
                .Where(c => c.HaGuanyat && c.Any >= anyMinim)
                .ToList();
        }

        // EX4 (1.5p)
        static void MostraCandidaturesRelacionades(GOTYContext context, int idCandidatura)
        {
            var candidatura = context.Candidatures.Find(idCandidatura);

            if (candidatura == null)
            {
                Console.WriteLine("Candidatura no trobada");
                return;
            }

            // Amb lazy loading, no cal Include - es carreguen automàticament
            Console.WriteLine($"Candidatura: {candidatura.NomJoc} - {candidatura.Categoria} - {candidatura.Representant.Nom}");
            Console.WriteLine("Candidatures relacionades:");

            foreach (var relacionada in candidatura.CandidaturesRelacionades)
            {
                Console.WriteLine($"  - {relacionada.NomJoc} - {relacionada.Categoria} - {relacionada.Representant.Nom}");
            }
        }

        // EX5 (1.5p)
        static bool RelacionarCandidatures(GOTYContext context, int idCandidatura1, int idCandidatura2)
        {
            var cand1 = context.Candidatures.Find(idCandidatura1);
            var cand2 = context.Candidatures.Find(idCandidatura2);

            // Validacions
            if (cand1 == null || cand2 == null)
                return false;

            if (idCandidatura1 == idCandidatura2)
                return false;

            // Comprovar si són el mateix joc, categoria i any
            if (cand1.NomJoc == cand2.NomJoc && cand1.Categoria == cand2.Categoria && cand1.Any == cand2.Any)
                return false;

            // Comprovar si ja estan relacionades
            if (cand1.CandidaturesRelacionades.Any(c => c.Id == idCandidatura2))
                return false;

            // Afegir relació bidireccional
            cand1.CandidaturesRelacionades.Add(cand2);
            cand2.CandidaturesRelacionades.Add(cand1);

            context.SaveChanges();
            return true;
        }

        // EX6 (1.5p)
        static List<Candidatura> CandidaturesExitoses(GOTYContext context)
        {
            // LINQ: Candidatures que han guanyat O tenen una relacionada que ha guanyat
            // Amb lazy loading, podem accedir a CandidaturesRelacionades directament
            return context.Candidatures
                .AsEnumerable() // Carreguem tot a memòria per poder fer lazy loading
                .Where(c => c.HaGuanyat || c.CandidaturesRelacionades.Any(cr => cr.HaGuanyat))
                .ToList();
        }

        // EX7 (1.5p)
        static void TopEstudis(GOTYContext context, int votsMinims)
        {
            // Amb lazy loading i LINQ: GroupBy per estudi
            var estudis = context.Candidatures
                .AsEnumerable() // Carreguem a memòria per accedir a Representant.Estudi
                .GroupBy(c => c.Representant.Estudi)
                .Where(g => g.Sum(c => c.VotsPublic) > votsMinims)
                .Select(g => new
                {
                    Estudi = g.Key,
                    NumeroCandidatures = g.Count(),
                    VotsTotals = g.Sum(c => c.VotsPublic)
                })
                .OrderByDescending(e => e.VotsTotals)
                .ToList();

            foreach (var estudi in estudis)
            {
                Console.WriteLine($"{estudi.Estudi} - {estudi.NumeroCandidatures} candidatures - {estudi.VotsTotals} vots totals");
            }
        }
    }
}
