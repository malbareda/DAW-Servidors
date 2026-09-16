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
 * Classe que representa una presó.
 * Demostra una relació amb UNA CLASSE FILLA específica en herència SINGLE_TABLE:
 * - El Set és de tipus AlumnoMalo (classe filla), no Alumno (classe base)
 * - Només els AlumnoMalo poden estar relacionats amb una Carcel
 * - Els AlumnoBueno no tenen aquesta relació
 * - A la BD, la FK carcel_id a la taula Alumno només tindrà valor per files on tipo_de_alumno="Malos"
 */
@Entity
public class Carcel {

	@Id 
	@GeneratedValue(strategy = GenerationType.IDENTITY) // eso significa que es un autonumerico
	private int idCarcel;
	
	@Column
	private String nombreCarcel;
	
	//en una herencia, aqui tengo un set de solo un hijo. No aplicara a sus hermanos
	// IMPORTANT: Aquí utilitzem AlumnoMalo (classe filla específica)
	// Només els alumnes dolents poden anar a la presó
	// Això és diferent de Profe.alumnos que usa la classe base Alumno
	@OneToMany(mappedBy = "carcel") 
	private Set<AlumnoMalo> alumnos = new HashSet<>();



	public String getNombreCarcel() {
		return nombreCarcel;
	}



	public void setNombreCarcel(String nombreCarcel) {
		this.nombreCarcel = nombreCarcel;
	}



	public Set<AlumnoMalo> getAlumnos() {
		return alumnos;
	}



	public void setAlumnos(Set<AlumnoMalo> alumnos) {
		this.alumnos = alumnos;
	}



	public int getIdCarcel() {
		return idCarcel;
	}



	public Carcel() {
		
		super();
	}
	
	
	
}