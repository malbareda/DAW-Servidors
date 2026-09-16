package elReyDelKebab;

import jakarta.persistence.CascadeType;
import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.OneToOne;
import jakarta.persistence.Table;

/**
 * Classe Transaccion - representa una transacció de pagament d'un pedido
 * 
 * @Entity: Marca aquesta classe com a entitat JPA persistent
 */
@Entity
@Table
public class Transaccion {
	
	/**
	 * Camp id - identificador únic de la transacció
	 * @Id: Clau primària
	 * @GeneratedValue(IDENTITY): Auto-increment
	 */
	@Id //Llave primaria
	@GeneratedValue(strategy = GenerationType.IDENTITY) //Autonumerico
	@Column //Columna
	private int id;
	
	/**
	 * Camp IBAN - número de compte bancari internacional
	 * 
	 * @Column(nullable = false, updatable = false):
	 * 
	 * 1. nullable = false:
	 *    - La columna NO pot ser NULL a la base de dades
	 *    - Equivalent a: ALTER TABLE Transaccion ADD COLUMN IBAN VARCHAR(255) NOT NULL
	 *    - Si intentes inserir una transacció sense IBAN, la BD retornarà error
	 *    - És una restricció a nivell de BD (no només Java)
	 * 
	 * 2. updatable = false:
	 *    - El valor NO es pot modificar després de crear-se
	 *    - Hibernate ignorarà qualsevol intent d'actualitzar aquest camp
	 *    - És útil per camps que no haurien de canviar mai (com un IBAN)
	 *    - Si fas: transaccion.setIBAN("nou_iban"); session.merge(transaccion);
	 *      Hibernate NO actualitzarà la columna IBAN a la BD
	 * 
	 * Per què aquestes restriccions?
	 * - nullable=false: Una transacció sempre ha de tenir un IBAN vàlid
	 * - updatable=false: Per seguretat, l'IBAN no hauria de canviar després de crear la transacció
	 *   (seria sospitós que canviés el compte després del pagament)
	 * 
	 * Altres opcions de @Column:
	 * - insertable=false: El camp no es pot especificar en la inserció
	 * - unique=true: El valor ha de ser únic
	 * - length=50: Longitud màxima per VARCHAR
	 * - columnDefinition="TEXT": SQL personalitzat per la columna
	 */
	//nullable - pot el valor ser nul?
	//updatable - puc canviar el valor?
	@Column(nullable = false, updatable = false)
	private String IBAN;
	
	/**
	 * Camp CCN - número de targeta de crèdit (Card Credit Number)
	 * 
	 * Sense restriccions especials, és nullable i updatable
	 */
	@Column
	private String CCN;
	
	/**
	 * Relació One-to-One entre Transaccion i Pedido
	 * Una transacció pertany a un sol pedido, i un pedido té una sola transacció.
	 * 
	 * @OneToOne(cascade = CascadeType.ALL, mappedBy = "transaccion"):
	 * 
	 * 1. cascade = CascadeType.ALL:
	 *    - Propaga TOTES les operacions de Transaccion cap a Pedido
	 *    - Tipus de cascades:
	 * 
	 *    a) CascadeType.PERSIST:
	 *       - Si persisteixes una Transaccion, també persistirà el Pedido associat
	 *       - Exemple: session.persist(transaccion) → també fa persist del pedido
	 * 
	 *    b) CascadeType.MERGE:
	 *       - Si fas merge d'una Transaccion, també fa merge del Pedido
	 *       - Útil per actualitzar objectes desconnectats
	 * 
	 *    c) CascadeType.REMOVE:
	 *       - Si elimines una Transaccion, també elimina el Pedido
	 *       - PERILLÓS: pensa bé si ho necessites
	 *       - En aquest cas: eliminar transacció elimina pedido (potser massa agressiu)
	 * 
	 *    d) CascadeType.REFRESH:
	 *       - Si refresques una Transaccion, també refresca el Pedido
	 *       - Recarrega l'estat des de la BD
	 * 
	 *    e) CascadeType.DETACH:
	 *       - Si desconnectes una Transaccion de la sessió, també desconnecta el Pedido
	 * 
	 *    f) CascadeType.ALL:
	 *       - Inclou TOTS els anteriors: PERSIST, MERGE, REMOVE, REFRESH, DETACH
	 *       - Ús comú però pot ser perillós amb REMOVE
	 * 
	 *    Exemples:
	 *    - Sense cascade:
	 *      Transaccion t = new Transaccion();
	 *      Pedido p = new Pedido();
	 *      t.setPedido(p);
	 *      session.persist(t);  // ERROR: Pedido no està persistit!
	 *      
	 *    - Amb CascadeType.PERSIST:
	 *      session.persist(t);  // OK: També persisteix p automàticament
	 *      
	 *    - Amb CascadeType.ALL:
	 *      session.remove(t);   // També elimina p (pot ser perill!)
	 * 
	 * 2. mappedBy = "transaccion":
	 *    - Aquest és el costat INVERS de la relació One-to-One
	 *    - La relació es controla des de l'atribut "transaccion" de la classe Pedido
	 *    - Pedido és el propietari (té la foreign key)
	 *    - Transaccion NO té columna de foreign key
	 * 
	 * Per què Transaccion té mappedBy?
	 * - Normalment el costat més "important" o creat primer és el propietari
	 * - Pedido es crea abans que la transacció
	 * - Pedido "té" una transacció
	 * - Per tant, Pedido controla la relació
	 * 
	 * Taules resultants:
	 * 
	 * Taula Transaccion:
	 * +----+-------+-------------+
	 * | id | IBAN  | CCN         |
	 * +----+-------+-------------+
	 * | 1  | ES... | 833252...   |
	 * +----+-------+-------------+
	 * 
	 * Taula Pedido:
	 * +----+--------------+----------------+ 
	 * | id | fechaPedido  | transaccion_id | <- Foreign Key (Pedido és propietari)
	 * +----+--------------+----------------+
	 * | 1  | 2024-01-15   | 1              |
	 * +----+--------------+----------------+
	 * 
	 * Com funciona:
	 * - Pedido té una columna transaccion_id que apunta a Transaccion.id
	 * - Transaccion NO té cap foreign key
	 * - Per accedir al pedido des d'una transacció, Hibernate fa una query inversa
	 * 
	 * Exemple d'ús:
	 * Pedido pedido = new Pedido();
	 * Transaccion trans = new Transaccion();
	 * 
	 * // Establir relació bidireccional:
	 * pedido.setTransaccion(trans);  // Costat propietari (controla la FK)
	 * trans.setPedido(pedido);       // Costat invers (per coherència)
	 * 
	 * session.persist(pedido);  // Amb cascade, també persisteix trans
	 * 
	 * Accés invers:
	 * Transaccion t = session.get(Transaccion.class, 1);
	 * Pedido p = t.getPedido();  // Hibernate fa: SELECT * FROM Pedido WHERE transaccion_id = 1
	 * 
	 * IMPORTANT sobre cascade:
	 * - Amb CascadeType.ALL, si elimines la Transaccion, també s'elimina el Pedido
	 * - Això pot no ser el comportament desitjat (potser vols mantenir l'històric de pedidos)
	 * - Considera usar només: cascade = {CascadeType.PERSIST, CascadeType.MERGE}
	 */
	//mappedBy
	//Uno en realcion (solo en un lado)
	//mappedBy LA VARIABLE DE JAVA DEL OTRO LADO
	@OneToOne(cascade = CascadeType.ALL, mappedBy = "transaccion" )
	private Pedido pedido;
	
	//obviamente habrian mas

	/**
	 * Getters i Setters
	 * Necessaris per JPA/Hibernate
	 */

	public String getIBAN() {
		return IBAN;
	}

	public void setIBAN(String iBAN) {
		IBAN = iBAN;
	}

	public String getCCN() {
		return CCN;
	}

	public void setCCN(String cCN) {
		CCN = cCN;
	}

	/**
	 * getId(): No té setter perquè l'ID s'autogenera
	 */
	public int getId() {
		return id;
	}

	
	
	public Pedido getPedido() {
		return pedido;
	}

	public void setPedido(Pedido pedido) {
		this.pedido = pedido;
	}

	/**
	 * toString(): Representació textual de la transacció
	 * 
	 * NO incloem pedido per evitar recursió infinita
	 */
	@Override
	public String toString() {
		return "Transaccion [id=" + id + ", IBAN=" + IBAN + ", CCN=" + CCN + "]";
	}

	/**
	 * Constructor buit - OBLIGATORI per JPA/Hibernate
	 * 
	 * Hibernate necessita aquest constructor per crear instàncies via reflexió
	 */
	public Transaccion() {
		super();
	}
	
	
	

}






