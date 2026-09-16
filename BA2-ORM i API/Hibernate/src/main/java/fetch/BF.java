package fetch;

import java.util.HashSet;
import java.util.Set;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.FetchType;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.OneToMany;

@Entity
public class BF {

	@Id
	@GeneratedValue(strategy = GenerationType.IDENTITY)
	int id;
	
	@Column
	String nomB;
	
	
	public BF() {
	}
	
	@ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name="a_id", nullable=false)
	private AF a;


	public String getNomB() {
		return nomB;
	}

	public void setNomB(String nomB) {
		this.nomB = nomB;
	}

	public AF getA() {
		return a;
	}

	public void setA(AF a) {
		this.a = a;
	}
	
	
	
}
