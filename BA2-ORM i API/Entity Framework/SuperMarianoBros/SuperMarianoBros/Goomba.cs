namespace SuperMarianoBros;

public class Goomba
{
    public int GoombaID { get; set; }
    public string Nombre { get; set; }
    public double Altura { get; set; }
    public ColorGoomba Color { get; set; }
    
    public virtual Castillo Castillo { get; set; }
    public virtual Rey Rey { get; set; }

    public Goomba()
    {
        
    }

    public override string ToString()
    {
        return "El Alegre Goomba " + Nombre + " que mide " + Altura;
    }
}