namespace SuperMarianoBros;

public class CrimenesDeGuerra
{
    public int CrimenesDeGuerraID { get; set; }
    public string descripcion { get; set; }
    public double numeroVictimas{ get; set; }
    public DateTime fecha{ get; set; }


    public virtual ICollection<Rey> Reyes { get; set; }

    public CrimenesDeGuerra()
    {
        Reyes = new HashSet<Rey>();
    }
}