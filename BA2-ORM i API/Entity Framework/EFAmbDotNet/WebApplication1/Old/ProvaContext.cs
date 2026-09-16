using Microsoft.EntityFrameworkCore;
using System.Collections.Generic;
using System.Reflection.Emit;
using WebApplication1;

namespace WebApplication1 
{ 
    public class ProvaContextOld : DbContext
    { 
        public ProvaContextOld(DbContextOptions<ProvaContext> options) : base(options)
        {
        }

        public DbSet<Professor> Professor { get; set; }
        public DbSet<Modul> Modul { get; set; }
        public DbSet<Curs> Curs { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            // Relació 1-1 entre Professor i Curs
            modelBuilder.Entity<Professor>()
                .HasOne(p => p.Curs)
                .WithOne(c => c.Professor)
                .HasForeignKey<Curs>(c => c.CursID);

            // Relació N-N recursiva: Modul ←→ Modul (Prerequisites)
            modelBuilder.Entity<Modul>()
                .HasMany(m => m.Prerequisits)
                .WithMany(m => m.EsPrerequisitDe)
                .UsingEntity<Dictionary<string, object>>(
                    "ModulPrerrequisits",
                    j => j
                        .HasOne<Modul>()
                        .WithMany()
                        .HasForeignKey("PrerequisitID"),
                    j => j
                        .HasOne<Modul>()
                        .WithMany()
                        .HasForeignKey("ModulID")
                );

            base.OnModelCreating(modelBuilder);
        }
    }
}