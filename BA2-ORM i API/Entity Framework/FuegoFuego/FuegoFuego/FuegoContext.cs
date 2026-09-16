using System;
using System.Collections.Generic;
using System.Data.Entity;
using System.Linq;
using System.Reflection;
using System.Text;
using System.Threading.Tasks;

namespace FuegoFuego
{
    // DbContext és la classe principal d'Entity Framework
    // Representa una sessió amb la base de dades
    public class FuegoContext : DbContext
    {
        // Constructor que crida al constructor base amb el nom de la connexió
        // "Test" és el nom de la connexió al app.config/web.config
        public FuegoContext() : base("Fuego")
        {
        }

        // DbSet representa una col·lecció d'entitats que poden ser consultades
        // Cada DbSet es convertirà en una taula a la base de dades
        public DbSet<Tono> Tono { get; set; }
        public DbSet<Protocolo> Protocolo { get; set; }
        public DbSet<Alarma> Alarma { get; set; }

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
            Database.SetInitializer(new DropCreateDatabaseAlways<FuegoContext>());

            modelBuilder.Entity<Alarma>().HasOptional(a => a.Tono).WithRequired(t => t.Alarma);

            base.OnModelCreating(modelBuilder);
        }
    }
}
