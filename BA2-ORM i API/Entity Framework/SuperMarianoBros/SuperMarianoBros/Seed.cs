using Microsoft.EntityFrameworkCore;

namespace SuperMarianoBros;

//SEEDER: dades inicials que viuen al MODEL (no a Program.cs).
//Es crida des de OnModelCreating amb Seed.Aplicar(modelBuilder).
//REGLES DE HasData:
//  1. Totes les PK i FK s'escriuen A MA (ReyID = 1, CastilloID = 2...)
//  2. NO es poden posar navegacions (Castillo = c1 NO funciona). Nomes ids.
//  3. Dates FIXES (mai DateTime.Now, o cada migracio canviaria el seed)
//  4. Les N-N tenen la seva propia taula intermitja i s'han d'omplir a part
public static class Seed
{
    public static void Aplicar(ModelBuilder mb)
    {
        //REYES (primer els Reyes, perque Castillo te la FK ReyID)
        mb.Entity<Rey>().HasData(
            new Rey { ReyID = 1, Nombre = "Bowser", Dictakdor = false },
            new Rey { ReyID = 2, Nombre = "Trakatrá", Dictakdor = true },
            new Rey { ReyID = 3, Nombre = "Koquitas", Dictakdor = true },
            new Rey { ReyID = 4, Nombre = "Aida", Dictakdor = true },
            new Rey { ReyID = 5, Nombre = "Carrero Blanco", Dictakdor = true },
            new Rey { ReyID = 6, Nombre = "el grande el señor con bigote si si si 6-7", Dictakdor = false }
        );

        //CASTILLOS (1-1 amb Rey: la FK ReyID va aqui, i s'escriu com un int normal)
        mb.Entity<Castillo>().HasData(
            new Castillo { CastilloID = 1, Nombre = "Castillo 8-4", Superficie = 67.67, ReyID = 1 },
            new Castillo { CastilloID = 2, Nombre = "Valle de los Caídos", Superficie = 19.36, ReyID = 2 }
        );

        //GOOMBAS (1-N amb Castillo i amb Rey).
        //Goomba no te propietats FK (nomes navegacions), aixi que son "shadow properties".
        //Per sembrar-les s'usa un objecte anonim amb els mateixos noms que a OnModelCreating.
        mb.Entity<Goomba>().HasData(
            new { GoombaID = 1, Nombre = "Barcenas", Altura = 0.45, Color = ColorGoomba.LLANTA_QUEMADA, CastilloID = 1, ReyID = 1 },
            new { GoombaID = 2, Nombre = "PacoFranco", Altura = 2.0, Color = ColorGoomba.PSOEIZADO, CastilloID = 2, ReyID = 2 },
            new { GoombaID = 3, Nombre = "PerroSanxe", Altura = 0.2, Color = ColorGoomba.CASTAÑO, CastilloID = 1, ReyID = 1 },
            new { GoombaID = 4, Nombre = "Anotop At", Altura = 4.0, Color = ColorGoomba.DORADO, CastilloID = 2, ReyID = 2 }
        );

        //CRIMENES DE GUERRA (data fixa i en UTC!)
        mb.Entity<CrimenesDeGuerra>().HasData(
            new CrimenesDeGuerra { CrimenesDeGuerraID = 1, descripcion = "Robar un castillo y llevarselo al espacio", numeroVictimas = 1, fecha = new DateTime(2026, 10, 2, 9, 12, 0, DateTimeKind.Utc) },
            new CrimenesDeGuerra { CrimenesDeGuerraID = 2, descripcion = "Bombardear abuelas", numeroVictimas = 50000, fecha = new DateTime(2026, 10, 3, 18, 30, 0, DateTimeKind.Utc) },
            new CrimenesDeGuerra { CrimenesDeGuerraID = 3, descripcion = "Apuñalar a gente en centros educativos deltante de Marc", numeroVictimas = 1, fecha = new DateTime(2026, 10, 4, 11, 0, 0, DateTimeKind.Utc) }
        );

        //N-N: la taula intermitja es defineix al Context (UsingEntity) i aqui nomes s'omple, pel seu nom.
        //Rey <-> CrimenesDeGuerra
        mb.Entity("ReyCrimen").HasData(
            new { ReyID = 1, CrimenesDeGuerraID = 1 },
            new { ReyID = 1, CrimenesDeGuerraID = 3 },
            new { ReyID = 2, CrimenesDeGuerraID = 2 },
            new { ReyID = 2, CrimenesDeGuerraID = 3 }
        );

        //N-N recursiva Rey <-> Rey (shipeos).
        //Cada fila = "ReyID te com a ShipeoDominante a ShipeoDominanteID"
        //(r1.ShipeoDominante={r3}, r3.ShipeoDominante={r6}, r5.ShipeoDominante={r6})
        mb.Entity("ReyShipeo").HasData(
            new { ReyID = 1, ShipeoDominanteID = 3 },
            new { ReyID = 3, ShipeoDominanteID = 6 },
            new { ReyID = 5, ShipeoDominanteID = 6 }
        );
    }
}
