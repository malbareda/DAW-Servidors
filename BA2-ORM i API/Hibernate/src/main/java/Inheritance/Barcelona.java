package Inheritance;

import java.util.HashSet;
import java.util.Set;

import jakarta.persistence.CascadeType;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.OneToMany;

/**
 * Classe que representa Barcelona.
 * Demostra una relació amb UNA CLASSE FILLA específica en herència TABLE_PER_CLASS:
 * - El Set és de tipus PeluqueriaPerroflauta (classe filla específica)
 * - Només les peluqueries perroflautas estan relacionades amb Barcelona
 * - Les PeluqueriaHipster NO tenen aquesta relació
 * - A la BD: la taula PeluqueriaPerroflauta té una FK carcel_id
 *          però la taula PeluqueriaHipster NO té aquesta columna
 */
@Entity
public class Barcelona {

	@Id 
	@GeneratedValue(strategy = GenerationType.IDENTITY) // eso significa que es un autonumerico
	private int idCarcel;
	
	@Column
	private String alcaldesa;
	
	//en una herencia, aqui tengo un set de solo un hijo. No aplicara a sus hermanos
	// El Set és específic de PeluqueriaPerroflauta
	// En TABLE_PER_CLASS, la FK només existeix a la taula PeluqueriaPerroflauta
	@OneToMany(mappedBy = "carcel") 
	private Set<PeluqueriaPerroflauta> pelus = new HashSet<>();






	public String getAlcaldesa() {
		return alcaldesa;
	}



	public void setAlcaldesa(String alcaldesa) {
		this.alcaldesa = alcaldesa;
	}



	public Set<PeluqueriaPerroflauta> getPelus() {
		return pelus;
	}



	public void setPelus(Set<PeluqueriaPerroflauta> pelus) {
		this.pelus = pelus;
	}



	public int getIdCarcel() {
		return idCarcel;
	}



	public Barcelona() {
		
		super();
	}
	
	
	
}