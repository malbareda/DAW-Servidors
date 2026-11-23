namespace SuperMarianoBros;

public class Castillo
{
    public int CastilloID { get; set; }
    public string Nombre { get; set; }
    public double Superficie { get; set; }
    
    //virtual significa que NO ES UNA COLUMNA, sino que es una variable interna, una relacion, etc.
    public virtual ICollection<Goomba> Goombas { get; set; }
    //1-1
    //NECSITO DECIR EN QUE TABLA VA LA FK
    public int ReyID { get; set; }
    public virtual Rey Rey { get; set; }
    
    //HAY QUE HACER SIEMPRE UN CONSTRUCTOR VACIO
    public Castillo()
    {
        Goombas = new HashSet<Goomba>();
    }
}