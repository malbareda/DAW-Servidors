using Microsoft.EntityFrameworkCore;
using MiniEjemploAPI;
using System.Text.Json.Serialization;

var builder = WebApplication.CreateBuilder(args);

// Configurar DbContext amb SQL Server i Lazy Loading
builder.Services.AddDbContext<PokemonContext>(options =>
    options.UseSqlServer(builder.Configuration.GetConnectionString("DefaultConnection"))
           .UseLazyLoadingProxies()); // Habilitar Lazy Loading

// Configurar controllers amb JSON
builder.Services.AddControllers()
    .AddJsonOptions(options =>
    {
        options.JsonSerializerOptions.ReferenceHandler = ReferenceHandler.IgnoreCycles;
        options.JsonSerializerOptions.PropertyNamingPolicy = null;
    });

builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

var app = builder.Build();

// Aplicar migrations i popular BD automàticament (Development)
if (app.Environment.IsDevelopment())
{
    //ESTA ES LA PARTE UQE NO HA GENERADO AUTO
    using (var scope = app.Services.CreateScope())
    {
        var context = scope.ServiceProvider.GetRequiredService<PokemonContext>();
        //context.Database.EnsureDeleted(); CREATE
        context.Database.EnsureCreated();
    }

    app.UseSwagger();
    app.UseSwaggerUI();
}

app.UseHttpsRedirection();
app.UseAuthorization();
app.MapControllers();
app.Run();

