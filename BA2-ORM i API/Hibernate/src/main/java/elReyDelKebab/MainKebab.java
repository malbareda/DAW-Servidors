package elReyDelKebab;

import java.util.Date;
import java.util.List;

import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.hibernate.boot.MetadataSources;
import org.hibernate.boot.registry.StandardServiceRegistryBuilder;
import org.hibernate.service.ServiceRegistry;

import jakarta.persistence.Query;

/**
 * Classe MainKebab - punt d'entrada principal per l'aplicació de gestió del kebab
 * 
 * Aquesta classe conté el mètode main() i gestiona:
 * - La configuració d'Hibernate
 * - La creació i gestió de sessions
 * - Les operacions CRUD (Create, Read, Update, Delete)
 * - Les transaccions
 */
public class MainKebab {

	/**
	 * Session: Representa una connexió/sessió amb la base de dades
	 * 
	 * Què és una Session?
	 * - És l'objecte principal per interactuar amb la BD a Hibernate
	 * - Proporciona mètodes per:
	 *   * persist(): Inserir nous objectes
	 *   * get()/find(): Recuperar objectes per ID
	 *   * merge(): Actualitzar objectes
	 *   * remove(): Eliminar objectes
	 *   * createQuery(): Executar consultes HQL o JPQL
	 * 
	 * - És similar a una "connexió" JDBC però més d'alt nivell
	 * - Gestiona el cache de primer nivell (objectes carregats)
	 * - NO és thread-safe: cada thread ha de tenir la seva pròpia Session
	 * 
	 * Cicle de vida típic:
	 * 1. Obrir: session = sessionFactory.openSession();
	 * 2. Usar: session.persist(), get(), merge(), etc.
	 * 3. Tancar: session.close();
	 */
	static Session session;
	
	/**
	 * SessionFactory: Fàbrica de sessions d'Hibernate
	 * 
	 * Què és un SessionFactory?
	 * - És un objecte pesat (heavy-weight) que es crea una sola vegada per aplicació
	 * - Conté la configuració completa d'Hibernate:
	 *   * Connexió a la BD (URL, usuari, contrasenya)
	 *   * Mapejos de les entitats (classes anotades amb @Entity)
	 *   * Configuració del dialecte SQL
	 *   * Configuració del pool de connexions
	 * 
	 * - És thread-safe: múltiples threads poden obtenir Sessions simultàniament
	 * - És immutable: la configuració no canvia després de crear-se
	 * - És costós de crear: per això només es crea una vegada
	 * 
	 * Cicle de vida:
	 * 1. Crear una vegada a l'inici de l'aplicació
	 * 2. Reutilitzar per obtenir Sessions quan calgui
	 * 3. Tancar quan l'aplicació acaba
	 */
	// SessionFactory: fÁ brica de sessions, configurada una vegada per aplicaciÓ
	static SessionFactory sessionFactory;
	
	/**
	 * ServiceRegistry: Registre de serveis d'Hibernate
	 * 
	 * Què és un ServiceRegistry?
	 * - Conté els serveis de baix nivell que necessita Hibernate:
	 *   * Connexió a la BD
	 *   * Pool de connexions
	 *   * Transaccions
	 *   * Cache
	 * 
	 * - Es crea a partir del fitxer hibernate.cfg.xml
	 * - És necessari per crear el SessionFactory
	 * - Rarament interactuem directament amb ell
	 */
	// ServiceRegistry: registre de serveis d'Hibernate
	static ServiceRegistry serviceRegistry;

	/**
	 * getSessionFactory(): Crea i retorna el SessionFactory (patró Singleton)
	 * 
	 * Patró Singleton:
	 * - Garanteix que només existeix una instància de SessionFactory
	 * - synchronized: Evita problemes de concurrència quan múltiples threads criden aquest mètode
	 * 
	 * Passos per crear SessionFactory:
	 * 
	 * 1. StandardServiceRegistryBuilder().configure("hibernate.cfg.xml"):
	 *    - Llegeix el fitxer de configuració hibernate.cfg.xml
	 *    - Aquest fitxer conté:
	 *      <hibernate-configuration>
	 *        <session-factory>
	 *          <!-- Connexió a la BD -->
	 *          <property name="hibernate.connection.url">jdbc:mysql://localhost:3306/kebab</property>
	 *          <property name="hibernate.connection.username">root</property>
	 *          <property name="hibernate.connection.password">password</property>
	 *          
	 *          <!-- Dialecte SQL (MySQL, PostgreSQL, etc.) -->
	 *          <property name="hibernate.dialect">org.hibernate.dialect.MySQLDialect</property>
	 *          
	 *          <!-- Comportament -->
	 *          <property name="hibernate.hbm2ddl.auto">update</property>
	 *          <property name="hibernate.show_sql">true</property>
	 *          
	 *          <!-- Entitats mapejades -->
	 *          <mapping class="elReyDelKebab.Cliente"/>
	 *          <mapping class="elReyDelKebab.Ingrediente"/>
	 *          ...
	 *        </session-factory>
	 *      </hibernate-configuration>
	 * 
	 * 2. .build():
	 *    - Construeix el ServiceRegistry amb la configuració carregada
	 * 
	 * 3. new MetadataSources(serviceRegistry):
	 *    - Crea un objecte que conté els metadades de les entitats
	 *    - Inclou informació sobre taules, columnes, relacions, etc.
	 * 
	 * 4. .buildMetadata():
	 *    - Processa les anotacions JPA (@Entity, @Id, @Column, etc.)
	 *    - Crea el mapejat complet entre classes Java i taules de BD
	 * 
	 * 5. .buildSessionFactory():
	 *    - Finalment crea el SessionFactory amb tot el mapejat configurat
	 *    - A partir d'aquí podem crear Sessions
	 */
	public static synchronized SessionFactory getSessionFactory() {
		if (sessionFactory == null) {
			// Crea el registre de serveis utilitzant el fitxer hibernate.cfg.xml
			serviceRegistry = new StandardServiceRegistryBuilder().configure("hibernate.cfg.xml").build();
			sessionFactory = new MetadataSources(serviceRegistry).buildMetadata().buildSessionFactory();
		}
		return sessionFactory;
	}

	public static void main(String[] args) {
		/**
		 * Bloc try-catch-finally:
		 * - try: Conté el codi que pot llançar excepcions
		 * - catch: Gestiona els errors
		 * - finally: S'executa sempre, fins i tot si hi ha error (per tancar la sessió)
		 */
		try {
			
			/**
			 * CREACIÓ D'OBJECTES JAVA
			 * 
			 * Primer creem tots els objectes en memòria (Java)
			 * Encara NO estan a la base de dades
			 */
			
			// Crear ingredients
			Ingrediente salsaBlanca = new Ingrediente();
			salsaBlanca.setNombre("Salsa Blanca");
			salsaBlanca.setTipo(TipoIngrediente.SALSA);
			salsaBlanca.setCaducidad(new Date());
			
			Ingrediente carneEnBarra = new Ingrediente();
			carneEnBarra.setNombre("Shagrauma");
			carneEnBarra.setTipo(TipoIngrediente.CARNE);
			carneEnBarra.setCaducidad(new Date());
			
			Ingrediente tomaquet = new Ingrediente();
			tomaquet.setNombre("Tomaquet");
			tomaquet.setTipo(TipoIngrediente.VEGETABLE);
			tomaquet.setCaducidad(new Date());
			
			Ingrediente cebolla = new Ingrediente();
			cebolla.setNombre("Ceba");
			cebolla.setTipo(TipoIngrediente.VEGETABLE);
			cebolla.setCaducidad(new Date());
			
			Ingrediente q = new Ingrediente();
			q.setNombre("Formatge de Xavra");
			q.setTipo(TipoIngrediente.VEGETABLE);
			q.setCaducidad(new Date());
			
			Ingrediente p = new Ingrediente();
			p.setNombre("Pan Durum");
			p.setTipo(TipoIngrediente.PAN);
			p.setCaducidad(new Date());

			Ingrediente panpita = new Ingrediente();
			panpita.setNombre("Pan Pita");
			panpita.setTipo(TipoIngrediente.PAN);
			panpita.setCaducidad(new Date());
			
			
			Ingrediente c = new Ingrediente();
			c.setNombre("Coca Cola");
			c.setTipo(TipoIngrediente.BEBIDA);
			c.setCaducidad(null);
			
			Ingrediente aquihaypatacas = new Ingrediente();
			aquihaypatacas.setNombre("Patacas Fritas");
			aquihaypatacas.setTipo(TipoIngrediente.VEGETABLE);
			aquihaypatacas.setCaducidad(new Date());
			
			// Crear productes
			Producto p1 = new Producto();
			p1.setNombre("Kebab");
			p1.setPrecio(4.50);
			
			Producto p2 = new Producto();
			p2.setNombre("Pita");
			p2.setPrecio(4.50);
			
			Producto p3 = new Producto();
			p3.setNombre("Patates Fregides");
			p3.setPrecio(2.50);
			
			Producto p4 = new Producto();
			p4.setNombre("Kebab solo Carne");
			p4.setPrecio(5.0);
			
			Producto p5 = new Producto();
			p5.setNombre("Coca Cola");
			p5.setPrecio(2.0);
			
			/**
			 * ESTABLIR RELACIONS MANY-TO-MANY (Producto - Ingrediente)
			 * 
			 * MOLT IMPORTANT: Per relacions bidireccionals, cal actualitzar AMBDÓS costats
			 * 
			 * p1.getIngredients().add(cebolla);  → Afegim cebolla als ingredients del kebab
			 * cebolla.getProductos().add(p1);    → Afegim kebab als productes de la cebolla
			 * 
			 * Per què actualitzar ambdós costats?
			 * 1. Coherència: Els objectes Java estan sincronitzats
			 * 2. Persistència: El costat propietari (@ManyToMany sense mappedBy) controla la BD
			 * 3. Navegació: Podem accedir a la relació des de qualsevol direcció
			 * 
			 * En aquest cas:
			 * - Ingrediente és el propietari (té @ManyToMany sense mappedBy)
			 * - Producto és l'invers (té @ManyToMany(mappedBy="productos"))
			 * - Les actualitzacions a ingredients.getProductos() s'escriuen a la taula intermèdia
			 */
			
			// Kebab (p1) conté: cebolla, tomaquet, carne, formatge, pan durum
			p1.getIngredients().add(cebolla);
			cebolla.getProductos().add(p1);
			p1.getIngredients().add(tomaquet);
			tomaquet.getProductos().add(p1);
			p1.getIngredients().add(carneEnBarra);
			carneEnBarra.getProductos().add(p1);
			p1.getIngredients().add(q);
			q.getProductos().add(p1);
			p1.getIngredients().add(p);
			p.getProductos().add(p1);
			
			// Pita (p2) conté: cebolla, tomaquet, carne, pan pita, formatge
			p2.getIngredients().add(cebolla);
			cebolla.getProductos().add(p2);
			p2.getIngredients().add(tomaquet);
			tomaquet.getProductos().add(p2);
			p2.getIngredients().add(carneEnBarra);
			carneEnBarra.getProductos().add(p2);
			p2.getIngredients().add(panpita);
			panpita.getProductos().add(p2);
			p2.getIngredients().add(q);
			q.getProductos().add(p2);
			
			// Patates (p3) conté: patates, salsa blanca
			p3.getIngredients().add(aquihaypatacas);
			aquihaypatacas.getProductos().add(p3);
			p3.getIngredients().add(salsaBlanca);
			salsaBlanca.getProductos().add(p3);
			
			// Kebab solo carne (p4) conté: carne, pan pita, salsa blanca
			p4.getIngredients().add(carneEnBarra);
			carneEnBarra.getProductos().add(p4);
			p4.getIngredients().add(panpita);
			panpita.getProductos().add(p4);
			p4.getIngredients().add(salsaBlanca);
			salsaBlanca.getProductos().add(p4);

			// Coca Cola (p5) conté: coca cola (ingredient)
			p5.getIngredients().add(c);
			c.getProductos().add(p5);
			
			/**
			 * ESTABLIR RELACIONS MANY-TO-MANY (Menu - Producto)
			 */
			Menu m1 = new Menu();
			
			m1.setNombre("Menu Marc LlaÃ³");
			m1.setPrecio(6.5);
			m1.setDescuento(0.1);
			m1.getProductos().add(p1);
			p1.getMenus().add(m1);
			m1.getProductos().add(p3);
			p3.getMenus().add(m1);
			m1.getProductos().add(p5);
			p5.getMenus().add(m1);
			
			/**
			 * ESTABLIR RELACIONS ONE-TO-MANY i MANY-TO-ONE (Cliente - Pedido)
			 */
			
			Pedido pe1 = new Pedido();
			pe1.setDomicilio(true);
			pe1.setFechaPedido(new Date());
			pe1.getMenuses().add(m1);
			m1.getPedidos().add(pe1);
			
			
			Cliente c1 = new Cliente();
			c1.setNombre("Adrian Vela");
			c1.setDireccion("Carrer Mendez  Pelayo, 1");
			c1.setTelf("634245207");
			c1.getPedidos().add(pe1);
			pe1.setCliente(c1);
			
			/**
			 * ESTABLIR RELACIONS ONE-TO-ONE (Transaccion - Pedido)
			 */
			Transaccion t1 = new Transaccion();
			t1.setIBAN("No me lo se");
			t1.setCCN("833252608540");
			t1.setPedido(pe1);
			pe1.setTransaccion(t1);
			
			
			
			
			
			
			/**
			 * OBRIR SESSIÓ
			 * 
			 * session = getSessionFactory().openSession();
			 * - Crea una nova sessió per interactuar amb la BD
			 * - La sessió és com una "finestra" oberta a la base de dades
			 * - Cada sessió té el seu propi cache de primer nivell
			 * - Les sessions NO són thread-safe
			 */
			// creo la sesion, esto es conectarse a la BD sin hacer nada
			session = getSessionFactory().openSession();

			/**
			 * INICIAR TRANSACCIÓ
			 * 
			 * session.beginTransaction();
			 * - Inicia una nova transacció
			 * 
			 * Què és una transacció?
			 * - Agrupa múltiples operacions de BD en una sola unitat atòmica
			 * - O es completen TOTES les operacions, o NO es completa CAP (atomicitat)
			 * - Propietats ACID:
			 *   A - Atomicity (atomicitat): Tot o res
			 *   C - Consistency (consistència): La BD passa d'un estat vàlid a un altre
			 *   I - Isolation (aïllament): Les transaccions no interfereixen entre elles
			 *   D - Durability (durabilitat): Els canvis confirmats són permanents
			 * 
			 * Per què usar transaccions?
			 * - Exemple: Transferència bancària
			 *   1. Restar diners del compte A
			 *   2. Sumar diners al compte B
			 *   Si falla el pas 2, el pas 1 es desfà (rollback)
			 * 
			 * Flux típic:
			 * 1. beginTransaction(): Iniciar
			 * 2. persist(), merge(), remove(): Operacions
			 * 3. commit(): Confirmar canvis a la BD
			 * o
			 * 3. rollback(): Desfer tots els canvis
			 * 
			 * IMPORTANT: Tota operació d'escriptura (INSERT, UPDATE, DELETE) necessita transacció
			 * Les operacions de lectura (SELECT) poden funcionar sense transacció (però és millor amb)
			 */
			// abrimos PRIMERA transaccion. Eso se hace siempre.
			// Cada operaciÓ d'escriptura necessita una transacciÓ
			session.beginTransaction();
			
			/**
			 * PERSIST - INSERTS
			 * 
			 * session.persist(objecte);
			 * - Marca l'objecte per ser inserit a la base de dades
			 * - Genera un INSERT SQL
			 * - L'objecte passa a estat "persistent" (gestionat per Hibernate)
			 * 
			 * Diferència amb save():
			 * - persist(): Estàndard JPA, retorna void, més estricte
			 * - save(): Específic d'Hibernate, retorna l'ID generat
			 * 
			 * IMPORTANT:
			 * - L'INSERT NO s'executa immediatament
			 * - Hibernate espera fins al commit() o flush()
			 * - Això permet optimitzar (batch inserts)
			 * 
			 * Exemple SQL generat:
			 * INSERT INTO Ingrediente (nombre, caducidad, stock, tipo) VALUES ('Salsa Blanca', '2024-01-15', 0, 'SALSA');
			 */
			//PERSIST --- INSERTS 
			session.persist(salsaBlanca);
			session.persist(carneEnBarra);
			session.persist(tomaquet);
			session.persist(cebolla);
			session.persist(q);
			session.persist(p);
			session.persist(c);
			session.persist(panpita);
			session.persist(aquihaypatacas);
			
			session.persist(p1);
			session.persist(p2);
			session.persist(p3);
			session.persist(p4);
			session.persist(p5);
			
			session.persist(m1);
			session.persist(c1);
			session.persist(t1);
			session.persist(pe1);
			
			/**
			 * MERGE - UPDATE (comentat aquí, s'usa més endavant)
			 * 
			 * session.merge(objecte);
			 * - Actualitza un objecte existent a la BD
			 * - Genera un UPDATE SQL
			 * - Útil per objectes "detached" (desconnectats de la sessió)
			 * 
			 * Quan usar merge?
			 * - Quan has carregat un objecte, tancat la sessió, modificat l'objecte, i vols guardar els canvis
			 * - Per objectes que vénen de fora (ex: deserialitzats de JSON)
			 * 
			 * Exemple SQL generat:
			 * UPDATE Ingrediente SET stock = 2 WHERE id = 1;
			 */
			//MERGE --- UPDATE
			
			
			/**
			 * COMMIT
			 * 
			 * session.getTransaction().commit();
			 * - Confirma tots els canvis de la transacció
			 * - Executa tots els SQL pendents (INSERT, UPDATE, DELETE)
			 * - Escriu els canvis permanentment a la BD
			 * - Allibera locks de la BD
			 * 
			 * Després del commit:
			 * - Els canvis són visibles per altres usuaris/transaccions
			 * - Els objectes persistents passen a estar "detached"
			 * - Cal iniciar una nova transacció per més operacions
			 * 
			 * Si NO fas commit:
			 * - Els canvis es perden quan tanques la sessió
			 * - És com si no haguessis fet res
			 */
			session.getTransaction().commit();
			
			
			/**
			 * SEGONA TRANSACCIÓ - OPERACIONS DE LECTURA I ACTUALITZACIÓ
			 */
			session.getTransaction().begin();
			
			/**
			 * GET - RECUPERAR UN OBJECTE PER ID
			 * 
			 * session.get(Class, id);
			 * - Carrega un objecte des de la BD pel seu ID
			 * - Retorna null si no existeix
			 * - Genera un SELECT SQL
			 * 
			 * Exemple SQL generat:
			 * SELECT * FROM Ingrediente WHERE id = 1;
			 * 
			 * Diferència amb load():
			 * - get(): Accedeix immediatament a la BD, retorna null si no existeix
			 * - load(): Retorna un proxy (lazy), llança excepció si no existeix
			 * 
			 * get() és més segur i recomanat
			 */
			//Get me devuelve el objeto con esa id
			Ingrediente in = session.get(Ingrediente.class, 1);
			System.out.println(in);
			in.setStock(2);
			
			/**
			 * MERGE - ACTUALITZAR L'OBJECTE
			 * 
			 * session.merge(in);
			 * - Actualitza l'objecte a la BD
			 * - Genera: UPDATE Ingrediente SET stock = 2 WHERE id = 1;
			 * 
			 * NOTA: En aquest cas, merge() no és estrictament necessari perquè:
			 * - L'objecte ja està "managed" (gestionat per la sessió)
			 * - Hibernate detecta els canvis automàticament (dirty checking)
			 * - Al fer commit(), actualitzarà l'objecte automàticament
			 * 
			 * Però és bona pràctica posar merge() per claredat
			 */
			session.merge(in); //ES MEJOR PONER MERGE
			
			/**
			 * CREATEQUERY - EXECUTAR CONSULTES HQL/JPQL
			 * 
			 * session.createQuery("FROM Ingrediente");
			 * - Executa una consulta HQL (Hibernate Query Language)
			 * - HQL és similar a SQL però usa noms de classes i atributs Java
			 * - Exemple HQL: "FROM Ingrediente" (no cal especificar columnes ni taula)
			 * - SQL equivalent: SELECT * FROM Ingrediente
			 * 
			 * HQL vs SQL:
			 * - HQL: "FROM Ingrediente WHERE nombre = :nom"
			 * - SQL: "SELECT * FROM Ingrediente WHERE nombre = ?"
			 * 
			 * Avantatges d'HQL:
			 * - Independència de la BD (Hibernate tradueix a SQL específic)
			 * - Treballa amb objectes, no taules
			 * - Type-safe (en combinar amb Criteria API)
			 * - Suporta joins automàtics per relacions
			 */
			//COGER TODO
			Query query = session.createQuery("FROM Ingrediente");
			
			/**
			 * GETRESULTLIST - OBTENIR TOTS ELS RESULTATS
			 * 
			 * query.getResultList();
			 * - Executa la query i retorna una llista amb tots els resultats
			 * - Cada element és un objecte Ingrediente
			 * 
			 * Alternativa:
			 * - getSingleResult(): Per queries que retornen un sol resultat
			 * - setMaxResults(n): Limitar el nombre de resultats
			 * - setFirstResult(n): Paginació (skip n resultats)
			 */
			List<Ingrediente> llista = query.getResultList();
			
			/**
			 * Recórrer la llista i actualitzar l'stock
			 * 
			 * Nota: En aquest exemple no es fan persist ni merge dins el bucle
			 * perquè els objectes ja estan managed (carregats en aquesta sessió)
			 * Hibernate detecta els canvis automàticament (dirty checking)
			 */
			for(Ingrediente ing : llista) {
				if(!ing.getNombre().equals("Ceba")) {
					ing.setStock(ing.getStock()+1);
					//session.merge(ing);
				}else {
					ing.setStock(ing.getStock()+1);
					//session.merge(ing);
					//session.remove(ing);
				}
			}
			
			/**
			 * Carregar un pedido i mostrar el seu preu calculat
			 * 
			 * ped1.getPrecio() crida al mètode calculat que suma els preus dels menús
			 */
			Pedido ped1 = session.get(Pedido.class, 1);
			System.out.println(ped1.getPrecio());
			
			/**
			 * COMMIT de la segona transacció
			 * Totes les actualitzacions d'stock s'escriuen a la BD
			 */
			session.getTransaction().commit();
			
			
			/**
			 * TERCERA TRANSACCIÓ - LÒGICA DE NEGOCI (Crear un kebab)
			 * 
			 * Aquest bloc implementa la lògica per crear un kebab:
			 * 1. Comprovar si hi ha prou stock de tots els ingredients
			 * 2. Si hi ha stock, decrementar l'stock de cada ingredient
			 * 3. Si no hi ha stock, mostrar error
			 */
			session.getTransaction().begin();

			/**
			 * Buscar tots els productes
			 */
			//CREAR UN KEBAB
			Query query2 = session.createQuery("FROM Producto");
			List<Producto> llistap = query2.getResultList();
			
			
			boolean posible = true;
			
			/**
			 * Buscar el producte "Kebab" i comprovar stock
			 */
			for(Producto produc : llistap) {
				if(produc.getNombre().equals("Kebab")) {
					// Comprovar si hi ha stock de tots els ingredients
					for(Ingrediente ingred : produc.getIngredients()) {
						System.out.println(ingred);
						if(ingred.getStock()<1) {
							System.out.println("falta "+ingred.getNombre());
							posible=false;
							break;
						}
						
						
					}
					// Si hi ha stock, decrementar-lo
					if(posible) {
						for(Ingrediente ingred : produc.getIngredients()) {
							System.out.println(ingred);
							ingred.setStock(ingred.getStock()-1);
							/**
							 * MERGE per actualitzar l'stock
							 * 
							 * En aquest cas SÍ és necessari merge() perquè:
							 * - Estem modificant objectes relacionats (ingredients del producte)
							 * - Volem assegurar que els canvis es propaguen correctament
							 */
							session.merge(ingred);
						}
					}
					
					if(posible)System.out.println("Aqui tiene su kebab amigo, son "+produc.getPrecio());
					else System.out.println("No me queda carne en barra amigo. Si quieres ve a hablar con el gerente");
					break;
				}
			}
			
			/**
			 * COMMIT de la tercera transacció
			 * Els canvis d'stock s'escriuen a la BD
			 */
			session.getTransaction().commit();
			
			


			
			
			
			
			System.out.println("Todo ha salido a pedir de Milhouse");

		} catch (Exception sqlException) {
			/**
			 * GESTIÓ D'ERRORS
			 * 
			 * Si qualsevol operació falla (SQLException, NullPointer, etc.):
			 * 1. Mostrar l'error
			 * 2. Fer ROLLBACK de la transacció
			 */
			sqlException.printStackTrace();
			if (null != session.getTransaction()) {
				System.out.println("\n.......Transaction Is Being Rolled Back.......");
				/**
				 * ROLLBACK
				 * 
				 * session.getTransaction().rollback();
				 * - Desfà TOTS els canvis de la transacció actual
				 * - La BD torna a l'estat anterior al beginTransaction()
				 * - És com prémer "Ctrl+Z" per tota la transacció
				 * 
				 * Exemple:
				 * - beginTransaction()
				 * - persist(ingredient1)  ← Inserit a memòria
				 * - persist(ingredient2)  ← Inserit a memòria
				 * - ERROR!
				 * - rollback()            ← ingredient1 i ingredient2 NO s'insereixen a la BD
				 * 
				 * Per què fer rollback?
				 * - Mantenir la consistència de dades
				 * - Si una operació falla, no volem dades a mitges
				 * - Exemple: Si falla transferir diners entre comptes, no volem que un compte
				 *   perdi diners però l'altre no els guanyi
				 */
				// Si hi ha error, es fa rollback de la transacciÓ
				session.getTransaction().rollback();
			}
			sqlException.printStackTrace();
		} finally {
			/**
			 * FINALLY - TANCAR LA SESSIÓ
			 * 
			 * Aquest bloc s'executa SEMPRE:
			 * - Encara que tot vagi bé
			 * - Encara que hi hagi error
			 * - Encara que hi hagi return
			 * 
			 * És CRUCIAL tancar la sessió per:
			 * - Alliberar connexions de BD (evitar fuites de memòria)
			 * - Tancar transaccions obertes
			 * - Alliberar recursos del sistema
			 * 
			 * Si no tanques les sessions:
			 * - El pool de connexions s'exhaureix
			 * - L'aplicació deixa de funcionar
			 * - Possibles deadlocks a la BD
			 */
			// Sempre cal tancar la sessiÓ
			if (session != null) {
				session.close();
			}
		}
	}

}