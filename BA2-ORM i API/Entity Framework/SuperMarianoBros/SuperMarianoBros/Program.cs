using SuperMarianoBros;
    
using (var ctx = new MarianoContext())
{
    //Esto borra y crea la BD cada vez. Esto seria un --fresh
    ctx.Database.EnsureDeleted();
    ctx.Database.EnsureCreated();


    //VAMOS A CREAR LOS OBJETOS
    var g1 = new Goomba() { Nombre = "Barcenas", Altura = 0.45, Color = ColorGoomba.LLANTA_QUEMADA };
    var g2 = new Goomba() { Nombre = "PacoFranco", Altura = 2, Color = ColorGoomba.PSOEIZADO };
    var g3 = new Goomba() { Nombre = "PerroSanxe", Altura = 0.2, Color = ColorGoomba.CASTAÑO };
    var g4 = new Goomba() { Nombre = "Anotop At", Altura = 4, Color = ColorGoomba.DORADO };

    var c1 = new Castillo() { Nombre = "Castillo 8-4", Superficie = 67.67 };
    c1.Goombas.Add(g1);
    c1.Goombas.Add(g3);
    
    
    var c2 = new Castillo() { Nombre = "Valle de los Caídos", Superficie = 19.36 };
    c2.Goombas.Add(g2);
    c2.Goombas.Add(g4);


    var r1 = new Rey() { Nombre = "Bowser", Dictakdor = false };
    var r2 = new Rey() { Nombre = "Trakatrá", Dictakdor = true };
    
    r1.Castillo = c1;
    r2.Castillo = c2;

    r1.Goombas = c1.Goombas;
    r2.Goombas = c2.Goombas;

    var cdg3 = new CrimenesDeGuerra() { descripcion = " Apuñalar a gente en centros educativos deltante de Marc", numeroVictimas = 1, fecha = DateTime.Now} ;
    var cdg2 = new CrimenesDeGuerra() { descripcion = "Bombardear abuelas" , numeroVictimas = 50000, fecha = DateTime.Now};
    var cdg1 = new CrimenesDeGuerra() { descripcion = "Robar un castillo y llevarselo al espacio" , numeroVictimas = 1, fecha = DateTime.Now};


    r1.CrimenesDeGuerras.Add(cdg1);
    r1.CrimenesDeGuerras.Add(cdg3);
    
    r2.CrimenesDeGuerras.Add(cdg2);
    r2.CrimenesDeGuerras.Add(cdg3);
    
    
    //ESTOS OBJETOS QUE HEMOS CREADO DE MOMENTO SOLO SON OBJETOS, NO SON TABLAS
    //PARA SER TABLAS DEBEN AÑADIRSE AL DBSET
    
    ctx.Castillos.AddRange(c1,c2);
    ctx.Reyes.AddRange(r1,r2);
    ctx.Goombas.AddRange(g1,g2,g3,g4);

    ctx.SaveChanges();
    
    
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




}