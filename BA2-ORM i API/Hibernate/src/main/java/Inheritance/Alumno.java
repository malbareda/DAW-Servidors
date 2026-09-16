package Inheritance;

import java.util.HashSet;
import java.util.Set;

import jakarta.persistence.DiscriminatorColumn;
import jakarta.persistence.DiscriminatorType;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Inheritance;
import jakarta.persistence.InheritanceType;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.OneToMany;

/**
 * Classe base de la jerarquia d'herència d'Alumnes.
 * Aquesta classe demostra l'estratègia SINGLE_TABLE:
 * - Tots els alumnes (bons i dolents) es guarden a la mateixa taula
 * - La columna discriminadora "tipo_de_alumno" permet diferenciar-los
 */
@Entity
@Inheritance(strategy = InheritanceType.SINGLE_TABLE)  //POR DEFECTO ES SINGLE TABLE
//La forma més fàcil de diferenciar entre una classe i una altra es crear una columna nova que t'indiqui el tipus de classe. Així ho pots comprovar ràpidament
//en SingleTable la discriminatorcolumn es obligatoria
@DiscriminatorColumn(name="tipo_de_alumno", discriminatorType = DiscriminatorType.STRING)  //la discirminació pot ser integer o String. Recomano String
public class Alumno {
	
	//ID ESTA A LA BASE EPRQEU COMAPRTEIXEN ID I VULL QUE ESTIGUIN A LA MATEIXA TAULA
	@Id 
	@GeneratedValue(strategy = GenerationType.IDENTITY) // eso significa que es un autonumerico
	private int idAlumno;
	
	//AQUEST ESTA A LA BASE PERQEU TOTS ELS FILLS LA TENEN
	// Aquest atribut el comparteixen tant AlumnoBueno com AlumnoMalo
	private String nombreAlumno;

	// Relació Many-to-One: molts alumnes poden tenir el mateix professor
	// Aquesta relació es defineix a la classe base, per tant TOTS els alumnes (bons i dolents) poden tenir profe
	@ManyToOne 
	private Profe profe = null;


	public String getNombreAlumno() {
		return nombreAlumno;
	}


	public void setNombreAlumno(String nombreAlumno) {
		this.nombreAlumno = nombreAlumno;
	}



	

	
	public Profe getProfe() {
		return profe;
	}


	public void setProfe(Profe profe) {
		this.profe = profe;
	}


	public Alumno() {
		super();
	}
	
	

}