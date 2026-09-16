package elReyDelKebab;

/**
 * Enum TipoIngrediente - tipus d'ingredients disponibles al kebab
 * 
 * Enums en Java:
 * - Tipus especial que defineix un conjunt fix de constants
 * - Més segur que usar Strings o ints per representar opcions
 * - El compilador verifica que només s'usen valors vàlids
 * 
 * Com s'usa amb JPA/Hibernate:
 * - A la classe Ingrediente hi ha: @Enumerated(EnumType.STRING)
 * - Això indica a Hibernate com mapear l'enum a la base de dades
 * - EnumType.STRING: Guarda el nom de la constant ("VEGETABLE", "CARNE", etc.)
 * - EnumType.ORDINAL: Guardaria la posició numèrica (0, 1, 2, ...) - NO recomanat
 * 
 * Exemple d'ús:
 * Ingrediente tomaquet = new Ingrediente();
 * tomaquet.setTipo(TipoIngrediente.VEGETABLE);  // Tipus segur
 * 
 * A la BD es guardarà com: "VEGETABLE" (String)
 * 
 * Avantatges dels enums:
 * - Type-safe: No pots posar un valor invàlid per error
 * - Autocompletació: L'IDE et mostra les opcions disponibles
 * - Refactorització fàcil: Pots reanomenar constants amb seguretat
 * - Documentació: Queda clar quins valors són vàlids
 */
public enum TipoIngrediente {
	/**
	 * VEGETABLE: Ingredients vegetals (tomaquet, enciam, ceba, etc.)
	 */
	VEGETABLE,
	
	/**
	 * CARNE: Ingredients càrnics (shawarma, pollastre, etc.)
	 */
	CARNE,
	
	/**
	 * BEBIDA: Begudes (Coca Cola, Fanta, aigua, etc.)
	 */
	BEBIDA,
	
	/**
	 * SALSA: Salses (salsa blanca, picant, iogurt, etc.)
	 */
	SALSA,
	
	/**
	 * PAN: Tipus de pa (durum, pita, etc.)
	 */
	PAN
}