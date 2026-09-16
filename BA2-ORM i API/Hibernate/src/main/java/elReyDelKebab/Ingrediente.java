package elReyDelKebab;

import java.util.Date;
import java.util.HashSet;
import java.util.Set;

import org.hibernate.annotations.ColumnDefault;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.EnumType;
import jakarta.persistence.Enumerated;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.ManyToMany;
import jakarta.persistence.Table;
import jakarta.persistence.Temporal;
import jakarta.persistence.TemporalType;

/**
 * Classe Ingrediente - representa un ingredient utilitzat als productes del kebab
 * 
 * @Entity: Marca aquesta classe com a entitat JPA persistent
 * Hibernate crearà la taula "Ingrediente" a la base de dades
 */
@Entity
@Table
public class Ingrediente {
	
	/**
	 * Camp id - identificador únic de l'ingredient
	 * @Id: Clau primària de la taula
	 */
	@Id //Llave primaria
	
	/**
	 * @GeneratedValue amb IDENTITY:
	 * La base de dades assignarà automàticament un valor incremental
	 * Cada nou ingredient obtindrà el següent número disponible
	 */
	@GeneratedValue(strategy = GenerationType.IDENTITY) //Autonumerico
	@Column //Columna
	private int id;
	
	/**
	 * Camp nombre - nom de l'ingredient
	 * 
	 * @Column(unique = true):
	 * - unique=true: Crea un CONSTRAINT UNIQUE a la base de dades
	 * - No es podran inserir dos ingredients amb el mateix nom
	 * - La BD retornarà un error si s'intenta duplicar
	 * - És útil per garantir la integritat de dades
	 * 
	 * Exemple a la BD:
	 * ALTER TABLE Ingrediente ADD CONSTRAINT UK_nombre UNIQUE (nombre);
	 * 
	 * Si intentem:
	 * INSERT INTO Ingrediente (nombre) VALUES ('Tomaquet'); -- OK
	 * INSERT INTO Ingrediente (nombre) VALUES ('Tomaquet'); -- ERROR: Duplicate entry
	 */
	//unique no pot repetirse
	@Column(unique = true)
	private String nombre;
	
	/**
	 * Camp caducidad - data de caducitat de l'ingredient
	 * 
	 * @Temporal(TemporalType.DATE):
	 * - Especifica com es mapeja el java.util.Date a la base de dades
	 * - Hi ha 3 tipus de TemporalType:
	 * 
	 * 1. TemporalType.DATE:
	 *    - Només emmagatzema la data (any-mes-dia)
	 *    - Tipus SQL: DATE
	 *    - Format: 2024-01-15
	 *    - NO guarda hora, minuts, segons
	 * 
	 * 2. TemporalType.TIME:
	 *    - Només emmagatzema l'hora (hores:minuts:segons)
	 *    - Tipus SQL: TIME
	 *    - Format: 14:30:00
	 *    - NO guarda la data
	 * 
	 * 3. TemporalType.TIMESTAMP:
	 *    - Emmagatzema data i hora completes
	 *    - Tipus SQL: TIMESTAMP o DATETIME
	 *    - Format: 2024-01-15 14:30:00
	 *    - Inclou data, hora, minuts, segons (i mil·lisegons)
	 * 
	 * Per què és necessari @Temporal?
	 * - java.util.Date conté data i hora completes
	 * - @Temporal indica a Hibernate com tractar-lo a la BD
	 * - Sense @Temporal, Hibernate podria usar TIMESTAMP per defecte
	 * 
	 * Nota: A partir de Java 8, és millor usar java.time (LocalDate, LocalDateTime)
	 * que no necessiten @Temporal, però aquest codi usa java.util.Date
	 */
	//les dates poes Temporal
	@Temporal(TemporalType.DATE)
	@Column
	private Date caducidad;
	
	/**
	 * Camp stock - quantitat disponible de l'ingredient
	 * 
	 * @ColumnDefault(value = "0"):
	 * - Anotació específica d'Hibernate (no JPA estàndard)
	 * - Defineix un valor per defecte a nivell de base de dades
	 * - Si s'insereix un registre sense especificar stock, la BD posarà 0
	 * 
	 * Exemple SQL generat:
	 * CREATE TABLE Ingrediente (
	 *   ...
	 *   stock INT DEFAULT 0,
	 *   ...
	 * );
	 * 
	 * IMPORTANT:
	 * - Aquest valor per defecte s'aplica a nivell de BD, no Java
	 * - Si crees un objecte Ingrediente en Java sense setStock(), serà 0 (valor primitiu)
	 * - Però és una bona pràctica inicialitzar també al constructor
	 * 
	 * Diferència amb inicialització en Java:
	 * private int stock = 0;  // Inicialització Java (abans de persistir)
	 * @ColumnDefault("0")     // Valor per defecte BD (quan s'insereix sense valor)
	 */
	@Column
	//columndefault, el valor per defecte
	@ColumnDefault(value = "0")
	private int stock;
	
	/**
	 * Camp tipo - tipus d'ingredient (VEGETABLE, CARNE, BEBIDA, etc.)
	 * 
	 * @Enumerated(EnumType.STRING):
	 * - Especifica com mapear un enum de Java a la base de dades
	 * - Hi ha 2 opcions:
	 * 
	 * 1. EnumType.STRING (recomanat):
	 *    - Guarda el NOM de la constant enum com a VARCHAR
	 *    - Exemple: 'VEGETABLE', 'CARNE', 'BEBIDA'
	 *    - AVANTATGES:
	 *      * Llegible a la BD: pots veure directament "CARNE"
	 *      * Robust: si afegeixes valors nous al mig de l'enum, no trenques res
	 *      * Millor per debugging i consultes SQL directes
	 *    - DESAVANTATGES:
	 *      * Ocupa més espai (VARCHAR vs INT)
	 *      * Si reanomenes una constant enum, has d'actualitzar la BD
	 * 
	 * 2. EnumType.ORDINAL (NO recomanat):
	 *    - Guarda la POSICIÓ numèrica de l'enum (0, 1, 2, ...)
	 *    - Exemple: VEGETABLE=0, CARNE=1, BEBIDA=2
	 *    - AVANTATGES:
	 *      * Ocupa menys espai (INT)
	 *      * Més ràpid (comparació de números)
	 *    - DESAVANTATGES:
	 *      * NO llegible a la BD: veus 0, 1, 2 en comptes de noms
	 *      * PERILLÓS: si afegeixes un valor nou al mig de l'enum, tots els ordinals canvien!
	 *      * Exemple perillós:
	 *        Abans: enum TipoIngrediente { VEGETABLE, CARNE, BEBIDA }
	 *        Després: enum TipoIngrediente { VEGETABLE, PAN, CARNE, BEBIDA }
	 *        Ara CARNE és 2 en comptes de 1, i les dades de la BD són inconsistents!
	 * 
	 * SEMPRE usa EnumType.STRING llevat que tinguis una raó MUY específica.
	 * 
	 * Exemple de taula resultant:
	 * +----+------------------+------------+-------+------------+
	 * | id | nombre           | caducidad  | stock | tipo       |
	 * +----+------------------+------------+-------+------------+
	 * | 1  | Tomaquet         | 2024-02-01 | 10    | VEGETABLE  |
	 * | 2  | Shagrauma        | 2024-01-20 | 5     | CARNE      |
	 * | 3  | Coca Cola        | NULL       | 20    | BEBIDA     |
	 * +----+------------------+------------+-------+------------+
	 */
	//Enumerated:
	//STRING - es guarda a la BD com una string
	//ORDINAL - es guarda a la BD com un numero
	@Enumerated(EnumType.STRING)
	@Column
	private TipoIngrediente tipo;
	
	/**
	 * Relació Many-to-Many (N:M) entre Ingrediente i Producto
	 * Un ingredient pot estar en molts productes, i un producte pot tenir molts ingredients.
	 * 
	 * @ManyToMany:
	 * - Defineix una relació de N a M
	 * - Aquesta és la part "propietària" de la relació (no té mappedBy)
	 * - Hibernate crearà automàticament una TAULA INTERMÈDIA per gestionar la relació
	 * 
	 * Com funciona una relació Many-to-Many?
	 * - No es pot representar directament amb foreign keys simples
	 * - Es necessita una tercera taula (taula intermèdia o de join)
	 * - Aquesta taula conté parelles d'IDs relacionats
	 * 
	 * Exemple de taules resultants:
	 * 
	 * Taula Ingrediente:
	 * +----+------------------+
	 * | id | nombre           |
	 * +----+------------------+
	 * | 1  | Tomaquet         |
	 * | 2  | Carne            |
	 * | 3  | Pan              |
	 * +----+------------------+
	 * 
	 * Taula Producto:
	 * +----+------------------+
	 * | id | nombre           |
	 * +----+------------------+
	 * | 1  | Kebab            |
	 * | 2  | Pita             |
	 * +----+------------------+
	 * 
	 * Taula intermèdia (generada automàticament):
	 * Nom per defecte: Ingrediente_Producto
	 * +----------------+-------------+
	 * | ingrediente_id | producto_id | <- Foreign Keys
	 * +----------------+-------------+
	 * | 1              | 1           |  (Tomaquet al Kebab)
	 * | 2              | 1           |  (Carne al Kebab)
	 * | 3              | 1           |  (Pan al Kebab)
	 * | 1              | 2           |  (Tomaquet a la Pita)
	 * | 2              | 2           |  (Carne a la Pita)
	 * +----------------+-------------+
	 * 
	 * Com funciona en codi:
	 * p1.getIngredients().add(tomaquet);  // Afegim tomaquet al Kebab
	 * tomaquet.getProductos().add(p1);    // Afegim Kebab a la llista de productes del tomaquet
	 * session.persist(p1);                 // Hibernate crearà les files a la taula intermèdia
	 * 
	 * IMPORTANT - Relacions bidireccionals:
	 * Per mantenir la consistència, has d'actualitzar AMBDÓS costats:
	 * ingredient.getProductos().add(producto);
	 * producto.getIngredients().add(ingredient);
	 * 
	 * Quin costat és el "propietari"?
	 * - El costat sense mappedBy (aquest, Ingrediente)
	 * - És el que controla la relació i fa les actualitzacions a la taula intermèdia
	 * - L'altre costat (Producto) té mappedBy="productos"
	 * 
	 * Opcions addicionals per @ManyToMany:
	 * - cascade: Propaga operacions a l'altre costat
	 * - fetch: LAZY (carrega quan es necessita) o EAGER (carrega immediatament)
	 * - @JoinTable: Personalitza el nom i columnes de la taula intermèdia
	 * 
	 * Exemple amb @JoinTable:
	 * @ManyToMany
	 * @JoinTable(
	 *   name = "Ingrediente_Producto",           // Nom de la taula intermèdia
	 *   joinColumns = @JoinColumn(name = "ingrediente_id"),    // Columna que apunta a Ingrediente
	 *   inverseJoinColumns = @JoinColumn(name = "producto_id") // Columna que apunta a Producto
	 * )
	 * private Set<Producto> productos;
	 */
	@ManyToMany
	private Set<Producto> productos = new HashSet<>();

	/**
	 * Getters i Setters
	 * Necessaris per JPA/Hibernate per accedir als camps via reflexió
	 */

	public String getNombre() {
		return nombre;
	}

	public void setNombre(String nombre) {
		this.nombre = nombre;
	}

	public Date getCaducidad() {
		return caducidad;
	}

	public void setCaducidad(Date caducidad) {
		this.caducidad = caducidad;
	}

	public int getStock() {
		return stock;
	}

	public void setStock(int stock) {
		this.stock = stock;
	}

	/**
	 * getId(): No té setter perquè l'ID s'autogenera
	 */
	public int getId() {
		return id;
	}

	
	
	public TipoIngrediente getTipo() {
		return tipo;
	}

	public void setTipo(TipoIngrediente tipo) {
		this.tipo = tipo;
	}

	public Set<Producto> getProductos() {
		return productos;
	}

	public void setProductos(Set<Producto> productos) {
		this.productos = productos;
	}

	/**
	 * Constructor buit - OBLIGATORI per JPA/Hibernate
	 * 
	 * Hibernate necessita poder crear instàncies buides i després omplir els camps
	 * Si aquest constructor no existeix, obtindràs errors en temps d'execució
	 * 
	 * Inicialitzem productos amb HashSet per evitar NullPointerException
	 */
	//SIEMPRE DEBE HABER UN CONSTRUCTOR VACIO. SI NO LO HAY DAN ERRORES FUNNY
	public Ingrediente() {
		super();
		productos = new HashSet<Producto>();
	}

	/**
	 * toString(): Representació textual de l'objecte
	 * 
	 * NO incloem productos per evitar recursió infinita:
	 * Ingrediente -> Producto -> Ingrediente -> ...
	 * 
	 * Només mostrem els atributs bàsics de l'ingredient
	 */
	@Override
	public String toString() {
		return "Ingrediente [id=" + id + ", nombre=" + nombre + ", caducidad=" + caducidad + ", stock=" + stock + "]";
	}
	
	

}






