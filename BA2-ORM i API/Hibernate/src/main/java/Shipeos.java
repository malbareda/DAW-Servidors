import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Table;

@Entity
@Table
public class Shipeos {
	
	//ID signfica clave rpimaria
	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	@Column
	private int id;
	
	@Column(length = 20)
	private String p1;
	
	@Column(nullable = true)
	private String p2;
	
	@Column
	private String nombreShip;
	
	@Column
	private boolean canon;
	
	@Column
	private int likes;

	public Shipeos() {
		super();
	}

	public String getP1() {
		return p1;
	}

	public void setP1(String p1) {
		this.p1 = p1;
	}

	public String getP2() {
		return p2;
	}

	public void setP2(String p2) {
		this.p2 = p2;
	}

	public String getNombreShip() {
		return nombreShip;
	}

	public void setNombreShip(String nombreShip) {
		this.nombreShip = nombreShip;
	}

	public boolean isCanon() {
		return canon;
	}

	public void setCanon(boolean canon) {
		this.canon = canon;
	}

	public int getLikes() {
		return likes;
	}

	public void setLikes(int likes) {
		this.likes = likes;
	}

	public int getId() {
		return id;
	}
	
	
	
	
	

}
