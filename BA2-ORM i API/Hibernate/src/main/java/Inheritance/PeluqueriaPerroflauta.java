package Inheritance;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;

/**
 * Classe filla que hereta de Peluqueria.
 * En TABLE_PER_CLASS:
 * - Aquesta classe crea la seva PRÒPIA taula: PeluqueriaPerroflauta
 * - La taula conté: idPelu (heretat), nom (heretat), chinches (propi), carcel_id (FK propi)
 * - Aquesta relació amb Barcelona només existeix per les peluqueries perroflautas
 * - La taula PeluqueriaHipster NO té aquesta relació
 */
@Entity
@Table
public class PeluqueriaPerroflauta extends Peluqueria{
	
	// Atribut específic d'aquesta classe
	// NOMÉS existeix a la taula PeluqueriaPerroflauta
	@Column
	private int chinches;

	// Relació específica només per les peluqueries perroflautas
	// La clau forana carcel_id només existeix a la taula PeluqueriaPerroflauta
	// La taula PeluqueriaHipster NO té aquesta FK
	@ManyToOne 
	private Barcelona carcel = null;
	
	
	public int getChinches() {
		return chinches;
	}


	public void setChinches(int chinches) {
		this.chinches = chinches;
	}


	public Barcelona getCarcel() {
		return carcel;
	}


	public void setCarcel(Barcelona carcel) {
		this.carcel = carcel;
	}


	public PeluqueriaPerroflauta() {
		super();
	}
	
	

}