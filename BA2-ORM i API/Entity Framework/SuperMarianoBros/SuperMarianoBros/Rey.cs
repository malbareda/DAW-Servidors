namespace SuperMarianoBros;

public class Rey
{
    public int ReyID { get; set; }
    public string Nombre { get; set; }
    public bool Dictakdor { get; set; }
    
    public virtual Castillo Castillo { get; set; }
    public virtual ICollection<Goomba> Goombas { get; set; }
    //N-N
    public virtual ICollection<CrimenesDeGuerra> CrimenesDeGuerras { get; set; }

    public Rey()
    {
        Goombas = new HashSet<Goomba>();
        CrimenesDeGuerras = new HashSet<CrimenesDeGuerra>();
    }
    
    
}