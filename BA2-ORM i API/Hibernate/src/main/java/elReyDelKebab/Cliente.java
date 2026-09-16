package elReyDelKebab;

import java.util.HashSet;
import java.util.Set;

import jakarta.persistence.Column;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.OneToMany;
import jakarta.persistence.Table;

/**
 * Classe Cliente - representa un client del restaurant de kebabs
 * 
 * @Entity: Anotació JPA que indica que aquesta classe és una entitat persistent.
 * Hibernate crearà automàticament una taula a la base de dades per aquesta classe.
 * Cada instància d'aquesta classe representarà una fila a la taula.
 */
@Entity

/**
 * @Table: Indica que aquesta entitat correspon a una taula a la base de dades.
 * Si no s'especifica el nom (name="nom_taula"), Hibernate utilitzarà el nom de la classe.
 * En aquest cas, la taula es dirà "Cliente".
 * 
 * Pots personalitzar-ho així: @Table(name="clients", schema="public")
 */
@Table
public class Cliente {
	
	/**
	 * Camp id - identificador únic del client
	 * 
	 * @Id: Marca aquest camp com la clau primària (PRIMARY KEY) de la taula.
	 * Cada entitat JPA ha de tenir almenys un camp marcat amb @Id.
	 * Aquest camp identifica de forma única cada registre.
	 */
	@Id //Llave primaria
	
	/**
	 * @GeneratedValue: Indica com es generarà automàticament el valor de la clau primària.
	 * 
	 * strategy = GenerationType.IDENTITY:
	 * - Utilitza una columna AUTO_INCREMENT de la base de dades
	 * - La BD assigna automàticament un valor únic cada vegada que s'insereix un registre
	 * - És la forma més comuna per MySQL/MariaDB
	 * 
	 * Altres estratègies:
	 * - GenerationType.AUTO: Hibernate escull la millor estratègia segons la BD
	 * - GenerationType.SEQUENCE: Usa sequences (típic de PostgreSQL, Oracle)
	 * - GenerationType.TABLE: Usa una taula separada per generar IDs
	 */
	@GeneratedValue(strategy = GenerationType.IDENTITY) //Autonumerico
	
	/**
	 * @Column: Indica que aquest atribut es mapeja a una columna de la taula.
	 * Si no s'especifiquen paràmetres, Hibernate usa el nom de l'atribut com a nom de columna.
	 * 
	 * Paràmetres útils:
	 * - name="nom_columna": Nom específic per la columna a la BD
	 * - nullable=false: La columna NO pot ser NULL (equivalent a NOT NULL)
	 * - unique=true: Valor ha de ser únic (equivalent a UNIQUE)
	 * - length=100: Longitud màxima per VARCHAR (per defecte 255)
	 * - updatable=false: El valor no es pot modificar després de crear-se
	 * - insertable=false: El valor no es pot especificar en la inserció
	 */
	@Column //Columna
	private int id;
	
	/**
	 * Camp nombre - nom del client
	 * Es crearà una columna VARCHAR a la taula Cliente
	 */
	@Column
	private String nombre;
	
	/**
	 * Camp telf - telèfon del client
	 * Hibernate mapeja String a VARCHAR per defecte
	 */
	@Column
	private String telf;
	
	/**
	 * Camp direccion - adreça del client
	 */
	@Column
	private String direccion;
	
	/**
	 * Relació One-to-Many (1:N) entre Cliente i Pedido
	 * Un client pot fer molts pedidos, però cada pedido pertany a un sol client.
	 * 
	 * @OneToMany: Defineix una relació d'1 a N des del costat "One" (Cliente).
	 * 
	 * mappedBy = "cliente":
	 * - MOLT IMPORTANT: mappedBy sempre va al costat "One" d'una relació 1:N
	 * - Indica que aquesta relació està mapejada per l'atribut "cliente" a la classe Pedido
	 * - Això significa que Pedido és el "propietari" de la relació (té la foreign key)
	 * - Cliente és el costat "invers" (no controla la relació directament)
	 * - NO es crearà una columna extra a la taula Cliente per aquesta relació
	 * - La foreign key estarà a la taula Pedido
	 * 
	 * Per què usar mappedBy?
	 * - Evita duplicació: sense mappedBy, Hibernate crearia una taula intermèdia innecessària
	 * - Defineix clarament quin costat controla la relació
	 * - Millora el rendiment: les operacions de persistència saben on actualitzar
	 * 
	 * Exemple de taules resultants:
	 * 
	 * Taula Cliente:
	 * +----+---------+------+-----------+
	 * | id | nombre  | telf | direccion |
	 * +----+---------+------+-----------+
	 * | 1  | Adrian  | 634  | Carrer... |
	 * +----+---------+------+-----------+
	 * 
	 * Taula Pedido:
	 * +----+--------------+------------+------------+
	 * | id | fechaPedido  | domicilio  | cliente_id | <- Foreign Key
	 * +----+--------------+------------+------------+
	 * | 1  | 2024-01-15   | true       | 1          |
	 * | 2  | 2024-01-16   | false      | 1          |
	 * +----+--------------+------------+------------+
	 * 
	 * Altres opcions per @OneToMany:
	 * - cascade: Propaga operacions (CascadeType.ALL, PERSIST, REMOVE, etc.)
	 * - fetch: Com carregar les dades (FetchType.LAZY o EAGER)
	 * - orphanRemoval=true: Elimina pedidos orfes quan es treuen de la col·lecció
	 */
	//@OneToMany defineix una relaciÓ un-a-molts amb Pedido
	//EL MAPPEDBY VA SEMPRE EN EL COSTAT DE L'ONE EN UNA 1-N
	//mappedBy indica que la relacio esta mapejada per el camp cliente a l'altre costat
	@OneToMany(mappedBy = "cliente")
	/**
	 * Set<Pedido>: Col·lecció de pedidos del client
	 * 
	 * Per què Set i no List?
	 * - Set evita duplicats automàticament
	 * - Millor rendiment per relacions bidireccionals
	 * - Hibernate recomana Set per relacionsMany
	 * 
	 * HashSet: Implementació de Set sense ordre garantit
	 * - Més ràpid que TreeSet o LinkedHashSet
	 * - Ordre no garantit però suficient per la majoria de casos
	 */
	private Set<Pedido> pedidos = new HashSet<>();

	/**
	 * Mètodes Getter i Setter
	 * 
	 * JPA/Hibernate requereix getters i setters per accedir als camps:
	 * - Hibernate usa reflexió per llegir/escriure valors
	 * - Permet afegir lògica de validació o transformació
	 * - Necessaris per proxies i lazy loading
	 */
	
	public String getNombre() {
		return nombre;
	}

	public void setNombre(String nombre) {
		this.nombre = nombre;
	}

	public String getTelf() {
		return telf;
	}

	public void setTelf(String telf) {
		this.telf = telf;
	}

	public String getDireccion() {
		return direccion;
	}

	public void setDireccion(String direccion) {
		this.direccion = direccion;
	}

	/**
	 * getId(): No té setter perquè l'ID el genera automàticament la BD
	 * No volem que es pugui modificar manualment
	 */
	public int getId() {
		return id;
	}

	
	
	public Set<Pedido> getPedidos() {
		return pedidos;
	}

	public void setPedidos(Set<Pedido> pedidos) {
		this.pedidos = pedidos;
	}

	/**
	 * toString(): Representació en text de l'objecte
	 * 
	 * IMPORTANT: NO incloem pedidos al toString() per evitar recursió infinita:
	 * Cliente -> toString() -> Pedido -> toString() -> Cliente -> ...
	 * 
	 * Això causaria un StackOverflowError
	 */
	@Override
	public String toString() {
		return "Cliente [id=" + id + ", nombre=" + nombre + ", telf=" + telf + ", direccion=" + direccion + "]";
	}

	/**
	 * Constructor buit (sense paràmetres)
	 * 
	 * OBLIGATORI per JPA/Hibernate:
	 * - Hibernate usa reflexió per crear instàncies
	 * - Necessita un constructor accessible sense arguments
	 * - Si defineixes un constructor amb paràmetres, has d'afegir també el buit
	 * - Si no hi ha constructor buit, obtindràs errors com:
	 *   "No default constructor for entity"
	 * 
	 * Inicialitzem pedidos amb HashSet buit:
	 * - Evita NullPointerException quan afegim pedidos
	 * - Garanteix que la col·lecció està sempre disponible
	 */
	public Cliente() {
		super();
		pedidos = new HashSet<Pedido>();
	}
	
	

}






