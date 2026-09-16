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
 * Classe que representa un professor.
 * Demostra com gestionar relacions amb una jerarquia d'herència SINGLE_TABLE:
 * - El Set d'alumnes és de tipus Alumno (classe base)
 * - Això permet que contingui tant AlumnoBueno com AlumnoMalo
 * - A la BD, la taula Alumno té una FK profe_id que apunta a aquesta taula
 */
@Entity
public class Profe {

	@Id 
	@GeneratedValue(strategy = GenerationType.IDENTITY) // eso significa que es un autonumerico
	private int idProfe;
	
	@Column
	private String nomProfe;
	
	//en una herencia, el set es de la clase BASE, no de sus hijos
	// IMPORTANT: Utilitzem Alumno (classe base), no AlumnoBueno o AlumnoMalo
	// Així el professor pot tenir alumnes de qualsevol tipus
	// mappedBy indica que el camp "profe" a la classe Alumno controla la relació
	@OneToMany(mappedBy = "profe") 
	private Set<Alumno> alumnos = new HashSet<>();

	public String getNomProfe() {
		return nomProfe;
	}

	public void setNomProfe(String nomProfe) {
		this.nomProfe = nomProfe;
	}

	public Set<Alumno> getAlumnos() {
		return alumnos;
	}

	public void setAlumnos(Set<Alumno> alumnos) {
		this.alumnos = alumnos;
	}

	public Profe() {
		super();
	}
	
	
	
}