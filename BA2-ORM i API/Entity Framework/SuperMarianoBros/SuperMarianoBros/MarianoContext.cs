using Microsoft.EntityFrameworkCore;

namespace SuperMarianoBros;

public class MarianoContext : DbContext
{
    
    
    //QUE HACEMOS EN EL CONTEXT
    //1. PONER TODOS LOS MODELOS. PONES UN DBSET POR CADA MODELO (TABLA)
    public DbSet<Castillo> Castillos { get; set; }
    public DbSet<Goomba> Goombas { get; set; }
    public DbSet<Rey> Reyes { get; set; }
    public DbSet<CrimenesDeGuerra> CrimenesDeGuerras { get; set; }
    
    //esta funcion la vais a copiar siempre proyecto a proyecto
    protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
    {
        if (!optionsBuilder.IsConfigured)
        {
            optionsBuilder
                // OPCIÓ A: SQLite (no cal instal·lar res)
                .UseSqlite(@"Data Source=escola.db")
                // OPCIÓ B: SQL Server LocalDB (comenta la línia de dalt i descomenta aquesta)
                // .UseSqlServer(@"Server=(localdb)\mssqllocaldb;Database=EscolaEF;Trusted_Connection=True;")
                //Activar modo Lazy Loading
                .UseLazyLoadingProxies();
        }
    }

    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        // 1-1: Curs <-> Professor (la FK és a Curs)
        modelBuilder.Entity<Castillo>()
            .HasOne(c => c.Rey)
            .WithOne(r => r.Castillo)
            .HasForeignKey<Castillo>(c => c.ReyID);

        // N-N recursiva: Modul <-> Prerequisits
  /*      modelBuilder.Entity<Modul>()
            .HasMany(m => m.Prerequisits)
            .WithMany(m => m.EsPrerequisitDe)
            .UsingEntity(j => j.ToTable("Prerequisits"));
*/
        base.OnModelCreating(modelBuilder);
    }
}
