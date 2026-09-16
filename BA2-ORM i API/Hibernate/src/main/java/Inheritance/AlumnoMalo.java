package Inheritance;

import jakarta.persistence.Column;
import jakarta.persistence.DiscriminatorValue;
import jakarta.persistence.Entity;
import jakarta.persistence.ManyToOne;

/**
 * Classe filla que hereta d'Alumno.
 * En SINGLE_TABLE:
 * - Els atributs cosaMala i la relació amb Carcel s'afegeixen a la taula Alumno
 * - Només els AlumnoMalo poden tenir una Carcel associada
 * - Les columnes cosaMala i carcel_id seran NULL per als AlumnoBueno
 */
@Entity
//DiscriminatorValue serà el valor que sortira a la DiscriminatorColumn en els d'aquiesta classe
@DiscriminatorValue("Malos") // Valor "Malos" a la columna "tipo_de_alumno"
public class AlumnoMalo extends Alumno {

	@Column
	// Atribut específic dels alumnes dolents
	// A la BD serà NULL per als AlumnoBueno
	private boolean cosaMala;

	// Relació específica només per als alumnes dolents
	// Un alumne dolent pot estar en una presó
	// Aquesta relació NO existeix per als AlumnoBueno (la FK serà NULL)
	@ManyToOne 
	private Carcel carcel = null;
	
	
	
	public Carcel getCarcel() {
		return carcel;
	}

	public void setCarcel(Carcel carcel) {
		this.carcel = carcel;
	}

	public boolean isCosaMala() {
		return cosaMala;
	}

	public void setCosaMala(boolean cosaMala) {
		this.cosaMala = cosaMala;
	}
	
	
}