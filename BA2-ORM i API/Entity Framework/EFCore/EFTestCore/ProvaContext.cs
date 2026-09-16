using Microsoft.EntityFrameworkCore;

namespace EFCoreTest1
{ 
    public class ProvaContext : DbContext
    {
        public ProvaContext()
        {
        }

        public DbSet<Professor> Professor { get; set; }
        public DbSet<Modul> Modul { get; set; }
        public DbSet<Curs> Curs { get; set; }

        protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        {
            if (!optionsBuilder.IsConfigured)
            {
                optionsBuilder
                    .UseSqlServer(@"Server=(localdb)\mssqllocaldb;Database=EFCoreTest;Trusted_Connection=True;")
                    .UseLazyLoadingProxies();  // AIXÒ ACTIVA EL LAZY LOADING AUTOMÀTIC com EF6
            }
        }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            // Configuració de la relació 1-1 entre Professor i Curs
            //LES 1-1 HAN D'ANAR PER FLUENT API. NO ES PODEN INFERIR PERQUE SI NO NO SAPS QUIN ES EL COSTAT DOMINANT
            modelBuilder.Entity<Professor>()
                .HasOne(p => p.Curs)
                .WithOne(c => c.Professor)
                .HasForeignKey<Curs>("ProfessorID") //Aqui la necessites per dir-li en quin cantó va la foreignKey
                .OnDelete(DeleteBehavior.NoAction); //AQUI ESTIC DIENT QUE NO FACI DELETE EN CASCADA
            //EFCORE ASSUMEIX DELETE EN CASCADA
            //AQUEST ES UN CAS ESPECIAL EPRQUE TOTES LES TAULSE EM FAN CICLE. PER TANT EL DELETE EN CASCADA SERIA INFINIT. EN ALGUN LLOC LI HE DE DIR UQE NO ME'L FACI


            // Configuració de la relació 1-N entre Curs i Modul
            //AQUESTA NO ES NECESSARIA
            //modelBuilder.Entity<Curs>()
            //    .HasMany(c => c.Moduls)
            //    .WithOne(m => m.Curs);
            //.HasForeignKey("CursID"); no es necessaria, nomes si vols posar nom a la columna

            // ============================================================
            // CONFIGURACIÓ DE LA RELACIÓ N-N RECURSIVA
            // ============================================================

            //LAS RECURSIVAS LAS TIENES QUE HACER SI O SI POR EF
            //PORQUE RECURSIVAS. SON UNA FIESTA. YA LAS CONOCEIS
            modelBuilder.Entity<Modul>()
                .HasMany(m => m.Prerequisits)
                .WithMany(m => m.EsPrerequisitDe)
                .UsingEntity<Dictionary<string, object>>(
                    "ModulPrerrequisits", //Nom de la Taula (ToTable)
                    // PRIMER r => ... defineix PrerequisitID (MapRightKey)
                    r => r
                        .HasOne<Modul>()
                        .WithMany()
                        .HasForeignKey("PrerequisitID"),
                    // SEGON l => ... defineix ModulID (MapLeftKey)  
                    l => l
                        .HasOne<Modul>()
                        .WithMany()
                        .HasForeignKey("ModulID")
                    );

            // Configuració de la relació N-N entre Professor i Modul
            //ESTA ME LA PUEDO PETAR
            //modelBuilder.Entity<Professor>()
            //    .HasMany(p => p.Moduls)
            //    .WithMany(m => m.Professors);
               //NO NECESSITO CREAR JO LA TAULA INTERMITJA; JA LA FA EF

            base.OnModelCreating(modelBuilder);
        }
    }
}