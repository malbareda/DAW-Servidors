package elReyDelKebab;

import java.util.Date;
import java.util.HashSet;
import java.util.Set;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.ManyToMany;
import jakarta.persistence.Table;

/**
 * Classe Producto - representa un producte del menú del kebab
 * (Kebab, Pita, Patates, begudes, etc.)
 * 
 * @Entity: Marca aquesta classe com a entitat JPA
 * Hibernate gestionarà la persistència d'aquesta classe a la base de dades
 */
@Entity
@Table
public class Producto {
	
	/**
	 * Camp id - identificador únic del producte
	 * 
	 * @Id: Clau primària
	 * @GeneratedValue(IDENTITY): Auto-increment a la BD
	 */
	@Id //Llave primaria
	@GeneratedValue(strategy = GenerationType.IDENTITY) //Autonumerico
	@Column //Columna
	private int id;
	
	/**
	 * Camp nombre - nom del producte (ex: "Kebab", "Pita", "Coca Cola")
	 */
	@Column
	private String nombre;
	

	/**
	 * Relació Many-to-Many entre Producto i Menu
	 * Un producte pot estar en molts menús, i un menú pot tenir molts productes.
	 * 
	 * @ManyToMany:
	 * - Aquest és el costat "propietari" de la relació (no té mappedBy)
	 * - Hibernate crearà una taula intermèdia per gestionar aquesta relació
	 * 
	 * Taula intermèdia generada automàticament:
	 * Nom per defecte: Producto_Menu
	 * +--------------+----------+
	 * | producto_id  | menu_id  | <- Foreign Keys
	 * +--------------+----------+
	 * | 1            | 1        |  (Kebab al Menu Marc Llaó)
	 * | 3            | 1        |  (Patates al Menu Marc Llaó)
	 * | 5            | 1        |  (Coca Cola al Menu Marc Llaó)
	 * +--------------+----------+
	 * 
	 * Exemple d'ús:
	 * Producto kebab = new Producto();
	 * Menu menu1 = new Menu();
	 * 
	 * kebab.getMenus().add(menu1);     // Afegim el menu al producte
	 * menu1.getProductos().add(kebab);  // Afegim el producte al menu
	 * 
	 * IMPORTANT: Has d'actualitzar ambdós costats per mantenir la consistència!
	 */
	//lista N-N
	@ManyToMany
	private Set<Menu> menus = new HashSet<>();
	
	/**
	 * Relació Many-to-Many entre Producto i Ingrediente
	 * Un producte està fet de molts ingredients, i un ingredient pot estar en molts productes.
	 * 
	 * @ManyToMany(mappedBy = "productos"):
	 * - mappedBy="productos": Aquest és el costat "invers" de la relació
	 * - La relació està controlada per l'atribut "productos" de la classe Ingrediente
	 * - Ingrediente és el costat propietari (té @ManyToMany sense mappedBy)
	 * - NO es crearà una segona taula intermèdia (ja existeix la d'Ingrediente)
	 * 
	 * Per què usar mappedBy?
	 * - Evita duplicació: sense mappedBy, Hibernate crearia 2 taules intermèdies diferents
	 * - Una sola taula intermèdia gestiona la relació des d'ambdós costats
	 * - El costat amb mappedBy és de "només lectura" respecte a la taula intermèdia
	 * 
	 * Quin costat ha de tenir mappedBy?
	 * - Pots triar qualsevol dels dos
	 * - Normalment, el costat més "important" o més freqüentment actualitzat NO té mappedBy
	 * - En aquest cas, Ingrediente és propietari (potser perquè afegim ingredients a productes més sovint)
	 * 
	 * Taula intermèdia (la mateixa que a Ingrediente):
	 * Nom: Ingrediente_Producto
	 * +----------------+-------------+
	 * | ingrediente_id | producto_id |
	 * +----------------+-------------+
	 * | 1              | 1           |  (Tomaquet al Kebab)
	 * | 2              | 1           |  (Carne al Kebab)
	 * | 3              | 1           |  (Pan al Kebab)
	 * +----------------+-------------+
	 * 
	 * Exemple d'ús:
	 * Ingrediente tomaquet = new Ingrediente();
	 * Producto kebab = new Producto();
	 * 
	 * // Has d'actualitzar ambdós costats!
	 * tomaquet.getProductos().add(kebab);     // Costat propietari (afegeix a la taula intermèdia)
	 * kebab.getIngredients().add(tomaquet);   // Costat invers (per coherència en Java)
	 * 
	 * session.persist(tomaquet);  // Això crearà la relació a la taula intermèdia
	 * 
	 * IMPORTANT sobre actualitzacions:
	 * - Només els canvis al costat propietari (Ingrediente) s'escriuen a la BD
	 * - Si només fas kebab.getIngredients().add(tomaquet) sense actualitzar l'altre costat,
	 *   la relació NO es guardarà a la base de dades!
	 * - Sempre has de mantenir ambdós costats sincronitzats per evitar inconsistències
	 */
	@ManyToMany(mappedBy = "productos")
	private Set<Ingrediente> ingredients = new HashSet<>();
	
	/**
	 * Camp precio - preu del producte
	 * 
	 * Nota: Hibernate mapeja double a DOUBLE o DECIMAL a la BD
	 * Per diners, seria millor usar BigDecimal per evitar problemes de precisió de coma flotant
	 */
	private double precio;
	
	/**
	 * Getters i Setters
	 * Necessaris per JPA/Hibernate
	 */
	
	public String getNombre() {
		return nombre;
	}

	public void setNombre(String nombre) {
		this.nombre = nombre;
	}

	public int getId() {
		return id;
	}
	
	

	
	public Set<Menu> getMenus() {
		return menus;
	}

	public void setMenus(Set<Menu> menus) {
		this.menus = menus;
	}

	public Set<Ingrediente> getIngredients() {
		return ingredients;
	}

	public void setIngredients(Set<Ingrediente> ingredients) {
		this.ingredients = ingredients;
	}

	public double getPrecio() {
		return precio;
	}

	public void setPrecio(double precio) {
		this.precio = precio;
	}

	/**
	 * Constructor buit - OBLIGATORI per JPA/Hibernate
	 * 
	 * Hibernate necessita aquest constructor per crear instàncies via reflexió
	 * Inicialitzem ingredients amb HashSet per evitar NullPointerException
	 */
	//SIEMPRE DEBE HABER UN CONSTRUCTOR VACIO. SI NO LO HAY DAN ERRORES FUNNY
	public Producto() {
		super();
		ingredients = new HashSet<Ingrediente>();
	}

	/**
	 * toString(): Representació textual del producte
	 * 
	 * NO incloem ingredients ni menus per evitar recursió infinita
	 * Només mostrem id, nom i preu
	 */
	@Override
	public String toString() {
		return "Producto [id=" + id + ", nombre=" + nombre + ", precio=" + precio + "]";
	}


	

}






