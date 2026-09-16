package Inheritance;

import jakarta.persistence.Column;
import jakarta.persistence.DiscriminatorValue;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;

/**
 * Classe filla que hereta d'Alumno.
 * En SINGLE_TABLE, aquesta classe NO crea una taula nova:
 * - Els seus atributs específics (cosaBuena) s'afegeixen com a columnes a la taula Alumno
 * - El valor "Buenos" a la columna discriminadora identifica les files d'aquest tipus
 * - L'atribut cosaBuena serà NULL per als AlumnoMalo
 */
@Entity
@DiscriminatorValue("Buenos") // Valor que apareixerà a la columna "tipo_de_alumno" per identificar aquesta classe
public class AlumnoBueno extends Alumno {

	@Column
	//COSABUENA NOMES ESTARA EN ELS ALUMNOS BUENOS. ELS ALUMNOS MALOS NO LA TINDRAN
	//PER TANT, COSABUENA ES NULLABLE
	// Aquest atribut només té sentit per als alumnes bons
	// A la BD, aquesta columna estarà a NULL per als AlumnoMalo
	private int cosaBuena;

	public int getCosaBuena() {
		return cosaBuena;
	}

	public void setCosaBuena(int cosaBuena) {
		this.cosaBuena = cosaBuena;
	}
	
	

}