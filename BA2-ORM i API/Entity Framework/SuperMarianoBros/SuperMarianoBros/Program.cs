using SuperMarianoBros;
    
using (var ctx = new MarianoContext())
{
    //Esto borra y crea la BD cada vez. Esto seria un --fresh
    ctx.Database.EnsureDeleted();
    ctx.Database.EnsureCreated();


    //LES DADES JA NO ES CREEN AQUI: les posa el SEEDER (Seed.cs, HasData) en fer EnsureCreated.
    //Aixi cada execucio parteix sempre de les mateixes dades.

    
    
    //CONSULTA 1: TODOS LOS CASTILLOS CON SUS GOOMBAS

    //ToList te devuelve todos los castillos en una comoda lista de C#
    List<Castillo> listacastillos = ctx.Castillos.ToList();
    foreach (Castillo c in listacastillos)
    {
        //count es el size()
        Console.WriteLine(c.Nombre + "con "+c.Goombas.Count+" goombas");
        foreach (Goomba g in c.Goombas)
        {
            Console.WriteLine(g.Nombre+ " con "+g.Altura + " metros ");
        }

        Console.WriteLine("-----------------------------");
    }

    
    ///CONSULTA 2: Pongo nombre de Goomba y me dice su castillo
    string nombreBuscado = "PacoFranco";
    Goomba buscado = ctx.Goombas.First(g => g.Nombre == nombreBuscado);
    Console.WriteLine(buscado);
    Console.WriteLine(buscado.Castillo.Nombre);
    Console.WriteLine("-----------------------------");

    
    //CONSULTA 3: Goombas mas altos que 0.5m, ordenados de mas alto a amas bajo
    List<Goomba> lista = ctx.Goombas.Where(g => g.Altura > 0.5).OrderByDescending(g => g.Altura).ToList();
    foreach (var g in lista)
    {
        Console.WriteLine(g);
    }
    Console.WriteLine("-----------------------------");

    
    //NO VOY A PONER UNA COSA ASI EN EL EXAMEN DO NOT WORRY
    var goombasporColor = ctx.Goombas
        .GroupBy(g => g.Color)
        .Select(grup => new { Color = grup.Key, Total = grup.Count() })
        .ToList();

    foreach (var gru in goombasporColor)
    {
        Console.WriteLine(gru.Color+": "+gru.Total);
    }
    
    
    
    ///Consulta graciosa
    
    //FIND VA POR ID
    Rey rey = ctx.Reyes.Find(1);
    Console.WriteLine("Goombas del Rey 1");
    foreach (Goomba g in rey.Castillo.Goombas)
    {
        Console.WriteLine(g);
    }
    Console.WriteLine("-----------------------------");

    //Cuantos reyes han apuñalado a gente
    Console.WriteLine("CUANTOS REYES HAN APUÑALADO A GENTE?");
    CrimenesDeGuerra puñal = ctx.CrimenesDeGuerras.Where(c => c.descripcion.Contains("puñal")).First();
    Console.WriteLine(puñal.Reyes.Count);

    //Cuantos reyes han apuñalado a gente
    Console.WriteLine("CUANTOS SHIPEOS TIENE EL SEÑOR CON BIGOTE?");
    Rey bigote = ctx.Reyes.Where(r => r.Nombre.Contains("6-7")).First();
    Console.WriteLine("Shipeos dominantes");
    foreach (Rey d in bigote.ShipeoDominante)
    {
        Console.WriteLine(d.Nombre);
    }

    Console.WriteLine("Shipeos Sumisos");
    foreach (Rey s in bigote.ShipeoSumiso)
    {
        Console.WriteLine(s.Nombre);
    }



}