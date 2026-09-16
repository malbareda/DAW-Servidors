using Microsoft.EntityFrameworkCore;
using MiniEjemploAPI;

namespace MiniEjemploAPI
{
    public class PokemonContext : DbContext
    {
        public PokemonContext(DbContextOptions<PokemonContext> options) : base(options)
        {
        }

        public DbSet<Entrenador> Entrenador { get; set; }
        public DbSet<Pokemon> Pokemon { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
           

            base.OnModelCreating(modelBuilder);
        }
    }
}