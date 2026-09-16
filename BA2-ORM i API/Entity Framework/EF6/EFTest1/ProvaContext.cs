using System.Data.Entity;

namespace EFTest1
{
    public class ProvaContext : DbContext
    {
        public ProvaContext() : base("Test")
        {
        }

        public DbSet<Professor> Professor { get; set; }
        public DbSet<Modul> Modul { get; set; }
        public DbSet<Curs> Curs { get; set; }

        protected override void OnModelCreating(DbModelBuilder modelBuilder)
        {
            Database.SetInitializer(new DropCreateDatabaseAlways<ProvaContext>());

            // Configuració de la relació 1-1 entre Professor i Curs
            modelBuilder.Entity<Professor>()
                .HasOptional(p => p.Curs)
                .WithRequired(c => c.Professor);

            // ============================================================
            // CONFIGURACIÓ DE LA RELACIÓ N-N RECURSIVA
            // ============================================================

            // Aquesta configuració defineix com es relaciona Modul amb si mateix
            // És una relació MOLTS A MOLTS (N-N) RECURSIVA

            modelBuilder.Entity<Modul>()
                // HasMany: Un mòdul TÉ MOLTS prerequisits
                // Definim la primera "punta" de la relació N-N
                .HasMany(m => m.Prerequisits)

                // WithMany: Aquests prerequisits SÓN PREREQUISITS DE MOLTS altres mòduls
                // Definim la segona "punta" (la inversa) de la relació N-N
                // Aquí especifiquem la propietat de navegació inversa
                .WithMany(m => m.EsPrerequisitDe)

                // Map: Configura la taula intermèdia (join table)
                .Map(m =>
                {
                    // ToTable: Nom de la taula intermèdia que crearà EF
                    // Si no especifiquem, EF crearia un nom automàtic com "ModulModuls"
                    m.ToTable("ModulPrerrequisits");

                    // MapLeftKey: Clau forana del "costat esquerre" (Modul que necessita prerequisits)
                    // Aquesta FK apunta a Modul.Codi i representa l'assignatura "principal"
                    // Exemple: Si M6 necessita M3, aquí hi hauria 6
                    m.MapLeftKey("ModulID");

                    // MapRightKey: Clau forana del "costat dret" (Modul que és prerequisit)
                    // Aquesta FK també apunta a Modul.Codi però representa el prerequisit
                    // Exemple: Si M6 necessita M3, aquí hi hauria 3
                    m.MapRightKey("PrerequisitID");

                    // RESULTAT: Taula amb aquesta estructura:
                    // ModulPrerrequisits(
                    //     ModulID INT FK → Modul.Codi,
                    //     PrerequisitID INT FK → Modul.Codi,
                    //     PRIMARY KEY (ModulID, PrerequisitID)
                    // )
                    //
                    // Exemple de com es veuen les dades:
                    // | ModulID | PrerequisitID |
                    // |---------|---------------|
                    // |    6    |      3        |  ← M6 necessita M3
                    // |    6    |      2        |  ← M6 necessita M2
                    // |    7    |      3        |  ← M7 necessita M3
                });

            // NOTES IMPORTANTS:
            // 1. Ambdues FK (ModulID i PrerequisitID) apunten a la mateixa taula (Modul)
            // 2. EF manté automàticament la sincronització bidireccional:
            //    - Si afegim M3 a M6.Prerequisits → EF afegeix M6 a M3.EsPrerequisitDe
            // 3. La taula intermèdia evita duplicats amb la clau primària composta
            // 4. Aquesta configuració permet cicles (A depèn de B, B depèn de C, C depèn de A)
            //    però cal anar amb compte en la lògica de negoci per evitar-los

            base.OnModelCreating(modelBuilder);
        }
    }
}