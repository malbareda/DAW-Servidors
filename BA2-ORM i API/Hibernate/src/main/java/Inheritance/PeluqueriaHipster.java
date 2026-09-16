package Inheritance;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.Table;

/**
 * Classe filla que hereta de Peluqueria.
 * En TABLE_PER_CLASS:
 * - Aquesta classe crea la seva PRÒPIA taula independent: PeluqueriaHipster
 * - La taula conté: idPelu (heretat), nom (heretat), olorPeculiar (propi)
 * - NO hi ha relació amb cap taula "Peluqueria" (no existeix)
 * - Cada taula és completament independent i autosuficient
 */
@Entity
@Table // Aquesta anotació crea una taula amb el nom de la classe
public class PeluqueriaHipster extends Peluqueria{
	
	// Atribut específic d'aquesta classe
	// A diferència de SINGLE_TABLE, aquest camp NOMÉS existeix a la taula PeluqueriaHipster
	// NO apareix a la taula PeluqueriaPerroflauta
	@Column
	private boolean olorPeculiar;

	public boolean isOlorPeculiar() {
		return olorPeculiar;
	}

	public void setOlorPeculiar(boolean olorPeculiar) {
		this.olorPeculiar = olorPeculiar;
	}

	public PeluqueriaHipster() {
		super();
	}
	
	

}