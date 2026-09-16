// ProvaContext.cs
using System.Data.Entity;

namespace EFTest1
{
    // DbContext és la classe principal d'Entity Framework
    // Representa una sessió amb la base de dades
    public class ProvaContextFluentAPI : DbContext
    {
        // Constructor que crida al constructor base amb el nom de la connexió
        // "Test" és el nom de la connexió al app.config/web.config
        public ProvaContextFluentAPI() : base("Test")
        {
        }

        // DbSet representa una col·lecció d'entitats que poden ser consultades
        // Cada DbSet es convertirà en una taula a la base de dades
        public DbSet<Professor> Professor { get; set; }
        public DbSet<Modul> Modul { get; set; }
        public DbSet<Curs> Curs { get; set; }

        // OnModelCreating permet configurar el model utilitzant Fluent API
        // Aquesta és una alternativa/complement a les Data Annotations
        protected override void OnModelCreating(DbModelBuilder modelBuilder)
        {
            // Diferents estratègies d'inicialització de la base de dades:

            // CreateDatabaseIfNotExists: Crea la BD només si no existeix
            //Database.SetInitializer(new CreateDatabaseIfNotExists<ProvaContext>());

            // DropCreateDatabaseIfModelChanges: Recrea la BD si canvia el model
            //Database.SetInitializer(new DropCreateDatabaseIfModelChanges<ProvaContext>());

            // DropCreateDatabaseAlways: Recrea la BD cada vegada
            Database.SetInitializer(new DropCreateDatabaseAlways<ProvaContext>());

            // Configuració de la relació 1-1 entre Professor i Curs
            modelBuilder.Entity<Professor>()
                .HasOptional(p => p.Curs)    // Professor pot tenir 0 o 1 Curs
                .WithRequired(c => c.Professor);  // Curs ha de tenir 1 Professor


            modelBuilder.Entity<Curs>()
                .HasMany(c => c.Moduls) //curs te molts moduls
                .WithRequired(m => m.Curs); //cada modul ha de tenir un curs

            modelBuilder.Entity<Modul>()
                .HasMany(m => m.Professors) //n a n
                .WithMany(p => p.Moduls) //efectiavment, n a n
                .Map(m =>
                {
                    m.ToTable("ProfessorModul"); //nom de la taula de join
                    m.MapLeftKey("ProfessorID"); //es el left per left joins
                    m.MapRightKey("ModulID"); //es el right per right joins
                });

            // Es poden afegir més configuracions com:
            // - Índexs: HasIndex()
            // - Claus compostes: HasKey()
            // - Relacions més complexes: HasMany(), HasRequired()
            // - Configuració de columnes: Property(), IsRequired(), HasMaxLength()

            base.OnModelCreating(modelBuilder);
        }
    }
}
