package Inheritance;

import jakarta.persistence.Column;
import jakarta.persistence.DiscriminatorColumn;
import jakarta.persistence.DiscriminatorType;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.Inheritance;
import jakarta.persistence.InheritanceType;

/**
 * Classe base de la jerarquia d'herència de Peluqueries.
 * Aquesta classe demostra l'estratègia TABLE_PER_CLASS:
 * - Cada classe concreta (PeluqueriaHipster, PeluqueriaPerroflauta) tindrà la seva pròpia taula
 * - Cada taula conté TOTS els camps: idPelu, nom + els atributs específics de cada classe
 * - NO hi ha una taula "Peluqueria", només taules per les classes filles
 * - NO es fa servir realment la columna discriminadora (tot i estar definida)
 */
@Entity
@Inheritance(strategy = InheritanceType.TABLE_PER_CLASS)  //POR DEFECTO ES SINGLE TABLE
//La forma més fàcil de diferenciar entre una classe i una altra es crear una columna nova que t'indiqui el tipus de classe. Així ho pots comprovar ràpidament
//en SingleTable la discriminatorcolumn es obligatoria
// NOTA: En TABLE_PER_CLASS la DiscriminatorColumn no s'utilitza realment
@DiscriminatorColumn(name="tipo", discriminatorType = DiscriminatorType.STRING)  //la discirminació pot ser integer o String. Recomano String

public class Peluqueria {
	
	// IMPORTANT: amb TABLE_PER_CLASS NO es pot fer servir IDENTITY
	// Cal utilitzar TABLE o SEQUENCE perquè múltiples taules han de coordinar els IDs
	@Id 
	@GeneratedValue(strategy = GenerationType.TABLE) // TABLE permet que múltiples taules comparteixin la generació d'IDs
	private int idPelu;
	
	// Aquest atribut apareixerà duplicat a totes les taules de les classes filles
	@Column
	private String nom;

	public String getNom() {
		return nom;
	}

	public void setNom(String nom) {
		this.nom = nom;
	}

	public int getIdPelu() {
		return idPelu;
	}

	public Peluqueria() {
		super();
	}

	
}