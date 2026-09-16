package elReyDelKebab;

import java.util.Date;
import java.util.HashSet;
import java.util.Set;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.JoinTable;
import jakarta.persistence.ManyToMany;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.OneToOne;
import jakarta.persistence.Table;
import jakarta.persistence.Temporal;
import jakarta.persistence.TemporalType;

/**
 * Classe Pedido - representa una comanda feta per un client
 * 
 * @Entity: Marca aquesta classe com a entitat JPA persistent
 */
@Entity
@Table
public class Pedido {
	
	/**
	 * Camp id - identificador únic del pedido
	 * @Id: Clau primària
	 * @GeneratedValue(IDENTITY): Auto-increment
	 */
	@Id //Llave primaria
	@GeneratedValue(strategy = GenerationType.IDENTITY) //Autonumerico
	@Column //Columna
	private int id;
	
	/**
	 * Camp fechaPedido - data i hora en què es va fer el pedido
	 * 
	 * @Temporal(TemporalType.TIMESTAMP):
	 * - TIMESTAMP guarda data completa + hora completa
	 * - Format: 2024-01-15 14:30:25.123
	 * - Inclou any, mes, dia, hora, minuts, segons i mil·lisegons
	 * 
	 * Recordatori dels tipus de @Temporal:
	 * - DATE: Només data (2024-01-15)
	 * - TIME: Només hora (14:30:25)
	 * - TIMESTAMP: Data + hora completes
	 * 
	 * Per a un pedido, necessitem saber exactament quan es va fer,
	 * per això usem TIMESTAMP en comptes de només DATE
	 */
	@Temporal(TemporalType.TIMESTAMP)
	@Column
	private Date fechaPedido;
	
	/**
	 * Camp domicilio - indica si el pedido es lliura a domicili o no
	 * 
	 * boolean es mapeja a:
	 * - BOOLEAN o BIT(1) a MySQL/MariaDB
	 * - TINYINT(1) en algunes configuracions
	 * - Valors: true/false (1/0 a la BD)
	 */
	@Column
	private boolean domicilio;

	/**
	 * Relació One-to-One entre Pedido i Transaccion
	 * Un pedido té una transacció de pagament, i una transacció pertany a un sol pedido.
	 * 
	 * @OneToOne:
	 * - Defineix una relació d'1 a 1
	 * - Cada pedido té exactament una transacció
	 * - Cada transacció està associada a un sol pedido
	 * 
	 * Aquest és el costat propietari de la relació:
	 * - NO té mappedBy
	 * - Conté la foreign key a la taula Pedido
	 * - Controla la relació
	 * 
	 * Taula resultant:
	 * Taula Pedido:
	 * +----+--------------+------------+----------------+
	 * | id | fechaPedido  | domicilio  | transaccion_id | <- Foreign Key
	 * +----+--------------+------------+----------------+
	 * | 1  | 2024-01-15   | true       | 1              |
	 * +----+--------------+------------+----------------+
	 * 
	 * Taula Transaccion:
	 * +----+-------+-------------+
	 * | id | IBAN  | CCN         |
	 * +----+-------+-------------+
	 * | 1  | ES... | 833252...   |
	 * +----+-------+-------------+
	 * 
	 * Com funciona:
	 * - Pedido té una columna transaccion_id que apunta a Transaccion.id
	 * - És una relació 1:1, així que cada ID només apareix una vegada
	 * 
	 * Diferència amb @ManyToOne:
	 * - @OneToOne: Cada pedido té 1 transacció, cada transacció té 1 pedido (1:1)
	 * - @ManyToOne: Molts pedidos podrien tenir la mateixa transacció (N:1)
	 * 
	 * Per què Pedido és el propietari?
	 * - És més natural: primer creem el pedido, després la transacció
	 * - El pedido "té" una transacció
	 * - Transaccion té mappedBy (costat invers)
	 */
	@OneToOne
	private Transaccion transaccion;
	
	/**
	 * Relació Many-to-One entre Pedido i Cliente
	 * Molts pedidos pertanyen a un sol client, però un client pot fer molts pedidos.
	 * 
	 * @ManyToOne:
	 * - Defineix el costat "Many" d'una relació N:1
	 * - Molts pedidos (N) pertanyen a un client (1)
	 * - Aquest és SEMPRE el costat propietari en una relació @ManyToOne/@OneToMany
	 * - Conté la foreign key
	 * 
	 * @JoinColumn(name = "cliente_que_ha_hecho_el_pedido"):
	 * - Personalitza el nom de la columna de la foreign key
	 * - Per defecte seria: cliente_id
	 * - Amb aquest JoinColumn, la columna es dirà: cliente_que_ha_hecho_el_pedido
	 * 
	 * JoinColumn és OPCIONAL però útil per:
	 * - Donar noms més descriptius a les columnes
	 * - Seguir convencions de nomenclatura específiques
	 * - Afegir restriccions (nullable, unique, etc.)
	 * 
	 * Opcions de @JoinColumn:
	 * @JoinColumn(
	 *   name = "cliente_id",              // Nom de la columna FK
	 *   nullable = false,                 // No pot ser NULL (pedido ha de tenir client)
	 *   foreignKey = @ForeignKey(name="fk_pedido_cliente")  // Nom del constraint FK
	 * )
	 * 
	 * Taula resultant:
	 * Taula Pedido:
	 * +----+--------------+------------+--------------------------------+
	 * | id | fechaPedido  | domicilio  | cliente_que_ha_hecho_el_pedido | <- Foreign Key
	 * +----+--------------+------------+--------------------------------+
	 * | 1  | 2024-01-15   | true       | 1                              |
	 * | 2  | 2024-01-16   | false      | 1                              |
	 * | 3  | 2024-01-17   | true       | 2                              |
	 * +----+--------------+------------+--------------------------------+
	 * 
	 * Taula Cliente:
	 * +----+--------------+
	 * | id | nombre       |
	 * +----+--------------+
	 * | 1  | Adrian Vela  |
	 * | 2  | Marc Llaó    |
	 * +----+--------------+
	 * 
	 * Com funciona:
	 * - Cada fila de Pedido té una columna que apunta a Cliente.id
	 * - Múltiples pedidos poden apuntar al mateix client
	 * - És una relació N:1 des de Pedido cap a Cliente
	 * 
	 * Exemple d'ús:
	 * Cliente client = new Cliente();
	 * Pedido pedido = new Pedido();
	 * 
	 * pedido.setCliente(client);         // Establim el client del pedido
	 * client.getPedidos().add(pedido);   // Afegim el pedido a la llista del client
	 * 
	 * session.persist(pedido);  // La foreign key s'escriurà automàticament
	 * 
	 * Relació bidireccional:
	 * - Pedido té @ManyToOne al client (costat Many, propietari)
	 * - Cliente té @OneToMany(mappedBy="cliente") als pedidos (costat One, invers)
	 * - mappedBy indica que la relació es controla des de Pedido
	 */
	@ManyToOne
	//JoinColumn es info sobre la columna que contindra la clau forana
	@JoinColumn(name = "cliente_que_ha_hecho_el_pedido")
	private Cliente cliente;
	
	
	/**
	 * Relació Many-to-Many entre Pedido i Menu
	 * Un pedido pot contenir molts menús, i un menú pot estar en molts pedidos.
	 * 
	 * @ManyToMany:
	 * - Aquest és el costat PROPIETARI (no té mappedBy)
	 * - Hibernate crearà una taula intermèdia per gestionar la relació
	 * 
	 * @JoinTable:
	 * - OPCIONAL: Personalitza completament la taula intermèdia
	 * - Si no poses @JoinTable, Hibernate crea una taula amb nom per defecte
	 * 
	 * Paràmetres de @JoinTable:
	 * 
	 * 1. name = "Mis_Menus":
	 *    - Nom personalitzat per la taula intermèdia
	 *    - Per defecte seria: Pedido_Menu
	 * 
	 * 2. joinColumns = {@JoinColumn(name = "id_pedido")}:
	 *    - Defineix la columna que representa AQUESTA entitat (Pedido) a la taula intermèdia
	 *    - "id_pedido": Nom de la columna que contindrà l'ID del Pedido
	 *    - Per defecte seria: pedido_id
	 * 
	 * 3. inverseJoinColumns = {@JoinColumn(name = "id_menu")}:
	 *    - Defineix la columna que representa l'ALTRA entitat (Menu) a la taula intermèdia
	 *    - "id_menu": Nom de la columna que contindrà l'ID del Menu
	 *    - Per defecte seria: menu_id
	 * 
	 * Taula intermèdia resultant:
	 * Nom: Mis_Menus (personalitzat amb @JoinTable)
	 * +------------+---------+
	 * | id_pedido  | id_menu | <- Ambdues són Foreign Keys
	 * +------------+---------+
	 * | 1          | 1       |  (Pedido 1 conté Menu 1)
	 * | 2          | 1       |  (Pedido 2 conté Menu 1)
	 * | 2          | 2       |  (Pedido 2 també conté Menu 2)
	 * +------------+---------+
	 * 
	 * Amb constraints:
	 * ALTER TABLE Mis_Menus
	 *   ADD CONSTRAINT fk_menu FOREIGN KEY (id_menu) REFERENCES Menu(id),
	 *   ADD CONSTRAINT fk_pedido FOREIGN KEY (id_pedido) REFERENCES Pedido(id),
	 *   ADD PRIMARY KEY (id_pedido, id_menu);  -- Clau primària composta
	 * 
	 * Per què usar @JoinTable?
	 * - Controlar els noms de la taula i columnes (més llegible, seguir convencions)
	 * - Afegir columnes addicionals (encara que això requereix crear una entitat separada)
	 * - Especificar índexs, constraints, etc.
	 * 
	 * Exemple sense @JoinTable (per defecte):
	 * @ManyToMany
	 * private Set<Menu> menuses;
	 * 
	 * Crearia:
	 * Taula: Pedido_Menu
	 * +-----------+----------+
	 * | pedido_id | menu_id  |
	 * +-----------+----------+
	 * 
	 * Exemple d'ús en codi:
	 * Pedido pedido = new Pedido();
	 * Menu menu = new Menu();
	 * 
	 * // Actualitzar ambdós costats:
	 * pedido.getMenuses().add(menu);    // Costat propietari (escriu a Mis_Menus)
	 * menu.getPedidos().add(pedido);    // Costat invers (per coherència)
	 * 
	 * session.persist(pedido);  // Crea files a la taula Mis_Menus
	 * 
	 * IMPORTANT:
	 * - Només els canvis al costat propietari (aquest, Pedido) s'escriuen a la BD
	 * - Si només actualitzes menu.getPedidos(), NO es guardarà a Mis_Menus
	 * - Has de mantenir sincronitzats ambdós costats
	 */
	//ManyToMany defineix una N a N
	@ManyToMany
	//TOTALMENT OPCIONAL
	@JoinTable(
			name = "Mis_Menus",//nom de la taula
			joinColumns = {@JoinColumn(name = "id_pedido")},//com es diu la columna que et representa a tu a la taula intermitja an la BD
			inverseJoinColumns = {@JoinColumn(name = "id_menu")}//com es diu la columan que representa a l'altre costat a la taula intermitja a la BD
			)
	private Set<Menu> menuses = new HashSet<>();
	
	/**
	 * Getters i Setters
	 * Necessaris per JPA/Hibernate
	 */
	
	public Date getFechaPedido() {
		return fechaPedido;
	}

	public void setFechaPedido(Date fechaPedido) {
		this.fechaPedido = fechaPedido;
	}
	
	

	public boolean isDomicilio() {
		return domicilio;
	}

	public void setDomicilio(boolean domicilio) {
		this.domicilio = domicilio;
	}

	public int getId() {
		return id;
	}
	
	/**
	 * getPrecio(): Calcula el preu total del pedido
	 * 
	 * Aquest NO és un camp persistent (no té @Column)
	 * És un mètode calculat que suma els preus dels menús amb els seus descomptes
	 * 
	 * Fórmula: preu_menu * (1 - descompte)
	 * Exemple: 
	 * - Menu a 10€ amb 10% descompte (0.1)
	 * - Preu final: 10 * (1 - 0.1) = 10 * 0.9 = 9€
	 */
	public double getPrecio() {
		double acc = 0;
		for(Menu m : this.getMenuses())
		{
			acc = acc+m.getPrecio()*(1-m.getDescuento());
		}
		return acc;
	}
	
	

	public Transaccion getTransaccion() {
		return transaccion;
	}

	public void setTransaccion(Transaccion transaccion) {
		this.transaccion = transaccion;
	}

	public Cliente getCliente() {
		return cliente;
	}

	public void setCliente(Cliente cliente) {
		this.cliente = cliente;
	}

	public Set<Menu> getMenuses() {
		return menuses;
	}

	public void setMenuses(Set<Menu> menuses) {
		this.menuses = menuses;
	}

	/**
	 * Constructor buit - OBLIGATORI per JPA/Hibernate
	 * 
	 * Inicialitzem menuses per evitar NullPointerException
	 */
	public Pedido() {
		super();
		menuses = new HashSet<Menu>();
	}

	/**
	 * toString(): Representació textual del pedido
	 * 
	 * NO incloem menuses, cliente ni transaccion per evitar recursió infinita
	 */
	@Override
	public String toString() {
		return "Pedido [id=" + id + ", fechaPedido=" + fechaPedido + "]";
	}
	
	

}






