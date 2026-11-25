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
        
        //FLUENT API
        //hay propiedades que no se pueden definir por inferencia
        //SQL decimal(3,2)
        modelBuilder.Entity<CrimenesDeGuerra>() //Entity es tabla
            .Property(cdg => cdg.numeroVictimas) //property es columna
            .HasPrecision(6, 7); //000000.0000000 a 999999.9999999  //y luego defines lo que quieras.
        
        
        
        
        
        
        // 1-1: Curs <-> Professor (la FK és a Curs)
        modelBuilder.Entity<Castillo>()
            .HasOne(c => c.Rey)
            .WithOne(r => r.Castillo)
            .HasForeignKey<Castillo>(c => c.ReyID);

        //N-N recursiva: Reyes <-> Reyes - Shipeos
        //Per poder sembrar (HasData) la taula intermitja, cal donar-li nom i columnes amb UsingEntity
        modelBuilder.Entity<Rey>()
            //PRIMERO EL HAS Y LUEGO EL WITH
            .HasMany(r => r.ShipeoDominante)
            .WithMany(r => r.ShipeoSumiso)
            .UsingEntity<Dictionary<string, object>>("ReyShipeo",
                right => right.HasOne<Rey>().WithMany().HasForeignKey("ShipeoDominanteID"),
                left => left.HasOne<Rey>().WithMany().HasForeignKey("ReyID"));

        //N-N Rey <-> CrimenesDeGuerra, tambe amb la taula intermitja anomenada
        modelBuilder.Entity<Rey>()
            .HasMany(r => r.CrimenesDeGuerras)
            .WithMany(c => c.Reyes)
            .UsingEntity<Dictionary<string, object>>("ReyCrimen",
                right => right.HasOne<CrimenesDeGuerra>().WithMany().HasForeignKey("CrimenesDeGuerraID"),
                left => left.HasOne<Rey>().WithMany().HasForeignKey("ReyID"));

        //1-N: Goomba no te propietats FK, aixi que les nomenem (shadow FKs) per poder-les sembrar
        modelBuilder.Entity<Goomba>()
            .HasOne(g => g.Castillo).WithMany(c => c.Goombas).HasForeignKey("CastilloID");
        modelBuilder.Entity<Goomba>()
            .HasOne(g => g.Rey).WithMany(r => r.Goombas).HasForeignKey("ReyID");

        //SEEDER: les dades inicials (veure Seed.cs)
        Seed.Aplicar(modelBuilder);

  /*      modelBuilder.Entity<Modul>()
            .HasMany(m => m.Prerequisits)
            .WithMany(m => m.EsPrerequisitDe)
            .UsingEntity(j => j.ToTable("Prerequisits"));
*/
        base.OnModelCreating(modelBuilder);
    }
}
