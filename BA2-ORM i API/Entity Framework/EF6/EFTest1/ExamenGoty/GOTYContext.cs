using System;
using System.Data.Entity;
using System.Linq;

namespace ExamenGOTY
{
    public class GOTYContext : DbContext
    {
        public DbSet<Candidatura> Candidatures { get; set; }
        public DbSet<Representant> Representants { get; set; }

        public GOTYContext() : base("name=GOTYConnection")
        {
        }

        protected override void OnModelCreating(DbModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            // Relació 1-1: Candidatura - Representant
            // Cada candidatura té un representant (requerit)
            // Cada representant està assignat a una única candidatura
            modelBuilder.Entity<Candidatura>()
                .HasRequired(c => c.Representant)
                .WithRequiredPrincipal(r => r.Candidatura);

            // Relació N-N recursiva: Candidatura - Candidatura
            // Important: En EF6 necessitem especificar ambdós costats amb noms diferents
            modelBuilder.Entity<Candidatura>()
                .HasMany(c => c.CandidaturesRelacionades)
                .WithMany(c => c.CandidaturesQueEmRelacionen)
                .Map(m =>
                {
                    m.ToTable("CandidaturaRelacionada");
                    m.MapLeftKey("CandidaturaId");
                    m.MapRightKey("CandidaturaRelacionadaId");
                });


        }
    }
}

