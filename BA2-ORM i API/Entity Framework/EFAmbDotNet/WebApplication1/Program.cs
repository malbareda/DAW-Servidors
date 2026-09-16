using Microsoft.EntityFrameworkCore;
using System.Text.Json.Serialization;
using WebApplication1;

var builder = WebApplication.CreateBuilder(args);

// Configurar DbContext amb SQL Server
builder.Services.AddDbContext<ProvaContext>(options =>
    options.UseSqlServer(builder.Configuration.GetConnectionString("DefaultConnection"))
           .UseLazyLoadingProxies()); // Habilitar Lazy Loading com a EF6

// Configurar controllers amb JSON
builder.Services.AddControllers()
    .AddJsonOptions(options =>
    {
        // Evitar referències circulars
        options.JsonSerializerOptions.ReferenceHandler = ReferenceHandler.IgnoreCycles;
        // Preservar noms de propietats (opcional)
        options.JsonSerializerOptions.PropertyNamingPolicy = null;
    });

// Configurar Swagger/OpenAPI
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

var app = builder.Build();

// Configure the HTTP request pipeline.
if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI();
}

app.UseHttpsRedirection();

app.UseAuthorization();

app.MapControllers();


using (var scope = app.Services.CreateScope())
{
    var context = scope.ServiceProvider.GetRequiredService<ProvaContext>();

    // Opció 1: Crear la BD si no existeix (UPDATE)
    context.Database.EnsureCreated();

    // Opció 2: Esborrar i recrear sempre (com DropCreateDatabaseAlways)
    //context.Database.EnsureDeleted();
    //context.Database.EnsureCreated();

    // Això aplica automàticament totes les migrations pendents
    //context.Database.Migrate();

}


app.Run();