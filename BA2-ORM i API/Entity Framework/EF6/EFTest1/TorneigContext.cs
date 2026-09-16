using System.Data.Entity;

namespace EFTest1
{
    public class TorneigConext : DbContext
    {
        public TorneigConext() : base("Test")
        {
        }

        public DbSet<Torneig> Torneig { get; set; }
        public DbSet<Equip> Equip { get; set; }
        public DbSet<Jugador> Jugador { get; set; }

        protected override void OnModelCreating(DbModelBuilder modelBuilder)
        {
            Database.SetInitializer(new DropCreateDatabaseAlways<ProvaContext>());


            modelBuilder.Entity<Equip>()
                .HasMany(e => e.Torneigs)
                .WithMany(t => t.Equips);


            base.OnModelCreating(modelBuilder);
        }
    }
}