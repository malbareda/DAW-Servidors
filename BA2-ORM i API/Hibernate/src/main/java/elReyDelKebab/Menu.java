package elReyDelKebab;

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
 * Classe Menu - representa un menú del restaurant
 * Un menú agrupa diversos productes amb un preu especial i descompte
 * Exemple: "Menu Marc Llaó" = Kebab + Patates + Coca Cola amb 10% descompte
 * 
 * @Entity: Marca aquesta classe com a entitat persistent JPA
 */
@Entity
@Table
public class Menu {
	
	/**
	 * Camp id - identificador únic del menú
	 * @Id: Clau primària
	 * @GeneratedValue(IDENTITY): Auto-increment
	 */
	@Id //Llave primaria
	@GeneratedValue(strategy = GenerationType.IDENTITY) //Autonumerico
	@Column //Columna
	private int id;
	
	/**
	 * Camp nombre - nom del menú (ex: "Menu Marc Llaó")
	 */
	@Column
	private String nombre;
	
	/**
	 * Relació Many-to-Many entre Menu i Pedido
	 * Un menú pot estar en molts pedidos, i un pedido pot tenir molts menús.
	 * 
	 * @ManyToMany(mappedBy = "menuses"):
	 * - mappedBy="menuses": Aquest és el costat INVERS de la relació
	 * - La relació està controlada per l'atribut "menuses" de la classe Pedido
	 * - Pedido és el propietari (té @ManyToMany sense mappedBy)
	 * - NO es crearà una taula intermèdia des d'aquí
	 * 
	 * Per què Pedido és el propietari?
	 * - Normalment el propietari és el costat més important o el que fa les actualitzacions
	 * - En aquest cas, quan fem un pedido, afegim menús al pedido
	 * - Té més sentit que Pedido controli la relació
	 * 
	 * Taula intermèdia (creada per Pedido):
	 * Nom personalitzat a Pedido.java: "Mis_Menus"
	 * +------------+---------+
	 * | id_pedido  | id_menu | <- Foreign Keys
	 * +------------+---------+
	 * | 1          | 1       |  (Pedido 1 té Menu 1)
	 * | 2          | 1       |  (Pedido 2 també té Menu 1)
	 * +------------+---------+
	 * 
	 * Exemple d'ús:
	 * Menu menu = new Menu();
	 * Pedido pedido = new Pedido();
	 * 
	 * // Actualitzar ambdós costats:
	 * pedido.getMenuses().add(menu);    // Costat propietari (escriu a la taula intermèdia)
	 * menu.getPedidos().add(pedido);    // Costat invers (per coherència)
	 */
	@ManyToMany(mappedBy = "menuses")
	private Set<Pedido> pedidos = new HashSet<>();
	//lista N-N
	
	/**
	 * Relació Many-to-Many entre Menu i Producto
	 * Un menú conté molts productes, i un producte pot estar en molts menús.
	 * 
	 * @ManyToMany(mappedBy = "menus"):
	 * - mappedBy="menus": Aquest és el costat INVERS de la relació
	 * - La relació està controlada per l'atribut "menus" de la classe Producto
	 * - Producto és el propietari de la relació
	 * - La taula intermèdia es crea des de Producto
	 * 
	 * Taula intermèdia (creada per Producto):
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
	 * Menu menu = new Menu();
	 * Producto kebab = new Producto();
	 * 
	 * // Actualitzar ambdós costats:
	 * kebab.getMenus().add(menu);        // Costat propietari
	 * menu.getProductos().add(kebab);    // Costat invers
	 */
	@ManyToMany(mappedBy = "menus")
	private Set<Producto> productos = new HashSet<>();
	
	/**
	 * Camp precio - preu del menú
	 * Normalment més barat que comprar els productes per separat
	 */
	private double precio;
	
	/**
	 * Camp descuento - descompte aplicat al menú
	 * Valor entre 0 i 1 (ex: 0.1 = 10% de descompte)
	 * 
	 * Nota: Aquest camp NO té @Column perquè Hibernate el mapeja automàticament
	 * com a columna amb el nom del camp (descuento) de tipus DOUBLE
	 */
	private double descuento;

	/**
	 * Getters i Setters
	 * Necessaris per JPA/Hibernate per accedir als camps
	 */

	public String getNombre() {
		return nombre;
	}

	public void setNombre(String nombre) {
		this.nombre = nombre;
	}

	public double getPrecio() {
		return precio;
	}

	public void setPrecio(double precio) {
		this.precio = precio;
	}

	public double getDescuento() {
		return descuento;
	}

	public void setDescuento(double descuento) {
		this.descuento = descuento;
	}

	public int getId() {
		return id;
	}

	
	public Set<Pedido> getPedidos() {
		return pedidos;
	}

	public void setPedidos(Set<Pedido> pedidos) {
		this.pedidos = pedidos;
	}

	public Set<Producto> getProductos() {
		return productos;
	}

	public void setProductos(Set<Producto> productos) {
		this.productos = productos;
	}

	/**
	 * toString(): Representació textual del menú
	 * 
	 * NO incloem pedidos ni productos per evitar recursió infinita
	 */
	@Override
	public String toString() {
		return "Menu [id=" + id + ", nombre=" + nombre + ", precio=" + precio + ", descuento=" + descuento + "]";
	}

	/**
	 * Constructor buit - OBLIGATORI per JPA/Hibernate
	 * 
	 * Inicialitzem les col·leccions per evitar NullPointerException
	 */
	public Menu() {
		super();
		pedidos = new HashSet<Pedido>();
		productos = new HashSet<Producto>();
	}
	
	
	

}