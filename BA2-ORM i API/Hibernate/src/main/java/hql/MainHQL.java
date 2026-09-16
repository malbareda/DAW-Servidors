package hql;

import java.util.Date;
import java.util.List;

import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.hibernate.boot.MetadataSources;
import org.hibernate.boot.registry.StandardServiceRegistryBuilder;
import org.hibernate.query.Query;
import org.hibernate.service.ServiceRegistry;

import elReyDelKebab.Cliente;
import elReyDelKebab.Ingrediente;
import elReyDelKebab.Menu;
import elReyDelKebab.Pedido;
import elReyDelKebab.Producto;
import elReyDelKebab.TipoIngrediente;
import elReyDelKebab.Transaccion;

/**
 * MainHQL - Exemples complets d'HQL (Hibernate Query Language)
 * 
 * Aquest main demostra TOTES les capacitats d'HQL:
 * - Consultes SELECT bàsiques i avançades
 * - Filtres amb WHERE
 * - Joins (INNER, LEFT, RIGHT, FETCH)
 * - Agregacions (COUNT, SUM, AVG, MAX, MIN)
 * - GROUP BY i HAVING
 * - ORDER BY
 * - Subconsultes
 * - Paràmetres (named i positional)
 * - UPDATE i DELETE en massa
 * - Projeccions i DTOs
 */
public class MainHQL {

	static Session session;
	static SessionFactory sessionFactory;
	static ServiceRegistry serviceRegistry;

	public static synchronized SessionFactory getSessionFactory() {
		if (sessionFactory == null) {
			serviceRegistry = new StandardServiceRegistryBuilder().configure("hibernate.cfg.xml").build();
			sessionFactory = new MetadataSources(serviceRegistry).buildMetadata().buildSessionFactory();
		}
		return sessionFactory;
	}

	public static void main(String[] args) {
		try {
			session = getSessionFactory().openSession();
			
			// Crear dades de prova si cal
			inicialitzarDades();
			
			System.out.println("\n========== EXEMPLES D'HQL ==========\n");
			
			// 1. Consultes SELECT bàsiques
			exempleSelectBasic();
			
			// 2. Consultes amb WHERE
			exempleWhere();
			
			// 3. Paràmetres (named i positional)
			exempleParametres();
			
			// 4. Operadors lògics i comparació
			exempleOperadors();
			
			// 5. LIKE i expressions de text
			exempleLike();
			
			// 6. IN i BETWEEN
			exempleInBetween();
			
			// 7. IS NULL i IS NOT NULL
			exempleNull();
			
			// 8. ORDER BY
			exempleOrderBy();
			
			// 9. Funcions d'agregació
			exempleAgregacio();
			
			// 10. GROUP BY i HAVING
			exempleGroupBy();
			
			// 11. JOINS
			exempleJoins();
			
			// 12. FETCH JOIN (evitar N+1)
			exempleFetchJoin();
			
			// 13. Subconsultes
			exempleSubconsultes();
			
			// 14. DISTINCT
			exempleDistinct();
			
			// 15. Projeccions i constructor expressions
			exempleProjections();
			
			// 16. UPDATE en massa
			exempleUpdate();
			
			// 17. DELETE en massa
			exempleDelete();
			
			// 18. Funcions d'HQL
			exempleFuncions();
			
			// 19. Consultes natives (SQL)
			exempleNativeQuery();
			
			// 20. Paginació
			exemplePaginacio();
			
			System.out.println("\n========== FI EXEMPLES D'HQL ==========\n");
			
		} catch (Exception e) {
			e.printStackTrace();
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}
	
	/**
	 * Inicialitza dades de prova si la BD està buida
	 */
	private static void inicialitzarDades() {
		session.beginTransaction();
		
		// Comprovar si ja hi ha dades
		Long count = session.createQuery("SELECT COUNT(i) FROM Ingrediente i", Long.class).getSingleResult();
		
		if (count == 0) {
			System.out.println("Creant dades de prova...\n");
			
			// Crear ingredients
			Ingrediente tomaquet = new Ingrediente();
			tomaquet.setNombre("Tomaquet");
			tomaquet.setTipo(TipoIngrediente.VEGETABLE);
			tomaquet.setStock(50);
			tomaquet.setCaducidad(new Date());
			
			Ingrediente cebolla = new Ingrediente();
			cebolla.setNombre("Ceba");
			cebolla.setTipo(TipoIngrediente.VEGETABLE);
			cebolla.setStock(30);
			cebolla.setCaducidad(new Date());
			
			Ingrediente carne = new Ingrediente();
			carne.setNombre("Shawarma");
			carne.setTipo(TipoIngrediente.CARNE);
			carne.setStock(20);
			carne.setCaducidad(new Date());
			
			Ingrediente pan = new Ingrediente();
			pan.setNombre("Pan Pita");
			pan.setTipo(TipoIngrediente.PAN);
			pan.setStock(100);
			pan.setCaducidad(new Date());
			
			Ingrediente salsa = new Ingrediente();
			salsa.setNombre("Salsa Yogur");
			salsa.setTipo(TipoIngrediente.SALSA);
			salsa.setStock(40);
			salsa.setCaducidad(null); // Sense caducitat
			
			Ingrediente cocacola = new Ingrediente();
			cocacola.setNombre("Coca Cola");
			cocacola.setTipo(TipoIngrediente.BEBIDA);
			cocacola.setStock(60);
			cocacola.setCaducidad(null);
			
			session.persist(tomaquet);
			session.persist(cebolla);
			session.persist(carne);
			session.persist(pan);
			session.persist(salsa);
			session.persist(cocacola);
			
			// Crear productes
			Producto kebab = new Producto();
			kebab.setNombre("Kebab");
			kebab.setPrecio(5.50);
			kebab.getIngredients().add(carne);
			kebab.getIngredients().add(tomaquet);
			kebab.getIngredients().add(cebolla);
			kebab.getIngredients().add(pan);
			
			Producto pita = new Producto();
			pita.setNombre("Pita");
			pita.setPrecio(4.50);
			pita.getIngredients().add(carne);
			pita.getIngredients().add(tomaquet);
			pita.getIngredients().add(pan);
			
			Producto beguda = new Producto();
			beguda.setNombre("Coca Cola");
			beguda.setPrecio(2.00);
			beguda.getIngredients().add(cocacola);
			
			session.persist(kebab);
			session.persist(pita);
			session.persist(beguda);
			
			// Crear menús
			Menu menu1 = new Menu();
			menu1.setNombre("Menu Kebab");
			menu1.setPrecio(7.00);
			menu1.setDescuento(0.10);
			menu1.getProductos().add(kebab);
			menu1.getProductos().add(beguda);
			
			session.persist(menu1);
			
			// Crear clients
			Cliente cliente1 = new Cliente();
			cliente1.setNombre("Adrian Vela");
			cliente1.setTelf("666111222");
			cliente1.setDireccion("Censored, 1");
			
			Cliente cliente2 = new Cliente();
			cliente2.setNombre("Grau Comelles");
			cliente2.setTelf("666333444");
			cliente2.setDireccion("Casa del Xavi, 69");
			
			session.persist(cliente1);
			session.persist(cliente2);
			
			// Crear pedidos
			Pedido pedido1 = new Pedido();
			pedido1.setFechaPedido(new Date());
			pedido1.setDomicilio(true);
			pedido1.setCliente(cliente1);
			pedido1.getMenuses().add(menu1);
			
			Transaccion trans1 = new Transaccion();
			trans1.setIBAN("ES1234567890");
			trans1.setCCN("4532111122223333");
			trans1.setPedido(pedido1);
			pedido1.setTransaccion(trans1);
			
			session.persist(pedido1);
			session.persist(trans1);
		}
		
		session.getTransaction().commit();
	}
	
	/**
	 * 1. SELECT bàsic - Recuperar totes les entitats
	 */
	private static void exempleSelectBasic() {
		System.out.println("=== 1. SELECT BÀSIC ===");
		
		session.beginTransaction();
		
		/**
		 * HQL més simple: SELECT tots els ingredients
		 * 
		 * Sintaxis: FROM NomClasse
		 * - No cal posar SELECT *
		 * - Usa el nom de la classe Java, no la taula
		 * - Retorna objectes complets
		 */
		Query<Ingrediente> query = session.createQuery("FROM Ingrediente", Ingrediente.class);
		List<Ingrediente> ingredients = query.getResultList();
		
		System.out.println("Total ingredients: " + ingredients.size());
		for (Ingrediente ing : ingredients) {
			System.out.println("  - " + ing.getNombre() + " (stock: " + ing.getStock() + ")");
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 2. WHERE - Filtrar resultats
	 */
	private static void exempleWhere() {
		System.out.println("=== 2. WHERE ===");
		
		session.beginTransaction();
		
		/**
		 * Filtrar amb WHERE
		 * 
		 * Sintaxis: FROM Entitat WHERE condició
		 * - Usa noms d'atributs Java, no columnes BD
		 * - Exemple: ing.tipo (no tipus o type)
		 */
		String hql = "FROM Ingrediente ing WHERE ing.tipo = 'VEGETABLE'";
		Query<Ingrediente> query = session.createQuery(hql, Ingrediente.class);
		List<Ingrediente> vegetables = query.getResultList();
		
		System.out.println("Ingredients tipus VEGETABLE:");
		for (Ingrediente veg : vegetables) {
			System.out.println("  - " + veg.getNombre());
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 3. Paràmetres - Named i Positional
	 */
	private static void exempleParametres() {
		System.out.println("=== 3. PARÀMETRES ===");
		
		session.beginTransaction();
		
		/**
		 * PARÀMETRES NAMED (recomanat)
		 * 
		 * Sintaxis: :nomParametre
		 * - Més llegible
		 * - Ordre no importa
		 * - Millor per mantenir
		 */
		System.out.println("Named parameters:");
		String hql1 = "FROM Ingrediente i WHERE i.stock > :stockMinim AND i.tipo = :tipus";
		Query<Ingrediente> query1 = session.createQuery(hql1, Ingrediente.class);
		query1.setParameter("stockMinim", 25);
		query1.setParameter("tipus", TipoIngrediente.VEGETABLE);
		List<Ingrediente> result1 = query1.getResultList();
		
		for (Ingrediente ing : result1) {
			System.out.println("  - " + ing.getNombre() + " (stock: " + ing.getStock() + ")");
		}
		
		/**
		 * PARÀMETRES POSITIONAL (menys recomanat)
		 * 
		 * Sintaxis: ?1, ?2, ?3...
		 * - Ordre importa
		 * - Menys llegible
		 */
		System.out.println("\nPositional parameters:");
		String hql2 = "FROM Producto p WHERE p.precio < ?1";
		Query<Producto> query2 = session.createQuery(hql2, Producto.class);
		query2.setParameter(1, 5.00);
		List<Producto> result2 = query2.getResultList();
		
		for (Producto prod : result2) {
			System.out.println("  - " + prod.getNombre() + " (" + prod.getPrecio() + "€)");
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 4. Operadors lògics i de comparació
	 */
	private static void exempleOperadors() {
		System.out.println("=== 4. OPERADORS ===");
		
		session.beginTransaction();
		
		/**
		 * Operadors disponibles:
		 * - Comparació: =, !=, <, >, <=, >=, <>
		 * - Lògics: AND, OR, NOT
		 * - Aritmètics: +, -, *, /
		 */
		String hql = "FROM Producto p WHERE p.precio >= 4.00 AND p.precio <= 6.00";
		Query<Producto> query = session.createQuery(hql, Producto.class);
		List<Producto> productes = query.getResultList();
		
		System.out.println("Productes entre 4€ i 6€:");
		for (Producto prod : productes) {
			System.out.println("  - " + prod.getNombre() + ": " + prod.getPrecio() + "€");
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 5. LIKE - Cercar patrons de text
	 */
	private static void exempleLike() {
		System.out.println("=== 5. LIKE ===");
		
		session.beginTransaction();
		
		/**
		 * LIKE per cercar patrons
		 * 
		 * Wildcards:
		 * - %: Qualsevol seqüència de caràcters
		 * - _: Un sol caràcter
		 * 
		 * Exemples:
		 * - 'Coca%': Comença per "Coca"
		 * - '%Cola': Acaba en "Cola"
		 * - '%Kebab%': Conté "Kebab"
		 */
		String hql = "FROM Ingrediente i WHERE i.nombre LIKE :patro";
		Query<Ingrediente> query = session.createQuery(hql, Ingrediente.class);
		String lletraA = "a";
		query.setParameter("patro", "%"+lletraA+"%"); // Conté 'a'
		List<Ingrediente> ingredients = query.getResultList();
		
		System.out.println("Ingredients que contenen 'a':");
		for (Ingrediente ing : ingredients) {
			System.out.println("  - " + ing.getNombre());
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 6. IN i BETWEEN
	 */
	private static void exempleInBetween() {
		System.out.println("=== 6. IN i BETWEEN ===");
		
		session.beginTransaction();
		
		/**
		 * IN: Comprovar si un valor està en una llista
		 */
		System.out.println("IN:");
		String hql1 = "FROM Ingrediente i WHERE i.tipo IN :tipus";
		Query<Ingrediente> query1 = session.createQuery(hql1, Ingrediente.class);
		query1.setParameter("tipus", List.of(TipoIngrediente.CARNE, TipoIngrediente.BEBIDA));
		List<Ingrediente> result1 = query1.getResultList();
		
		for (Ingrediente ing : result1) {
			System.out.println("  - " + ing.getNombre() + " (" + ing.getTipo() + ")");
		}
		
		/**
		 * BETWEEN: Comprovar rang
		 */
		System.out.println("\nBETWEEN:");
		String hql2 = "FROM Producto p WHERE p.precio BETWEEN :min AND :max";
		Query<Producto> query2 = session.createQuery(hql2, Producto.class);
		query2.setParameter("min", 2.00);
		query2.setParameter("max", 5.00);
		List<Producto> result2 = query2.getResultList();
		
		for (Producto prod : result2) {
			System.out.println("  - " + prod.getNombre() + ": " + prod.getPrecio() + "€");
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 7. IS NULL i IS NOT NULL
	 */
	private static void exempleNull() {
		System.out.println("=== 7. NULL ===");
		
		session.beginTransaction();
		
		/**
		 * IS NULL: Trobar valors nuls
		 * IS NOT NULL: Trobar valors no nuls
		 */
		System.out.println("Ingredients sense caducitat:");
		String hql1 = "FROM Ingrediente i WHERE i.caducidad IS NULL";
		List<Ingrediente> senseCaducitat = session.createQuery(hql1, Ingrediente.class).getResultList();
		
		for (Ingrediente ing : senseCaducitat) {
			System.out.println("  - " + ing.getNombre());
		}
		
		System.out.println("\nIngredients amb caducitat:");
		String hql2 = "FROM Ingrediente i WHERE i.caducidad IS NOT NULL";
		List<Ingrediente> ambCaducitat = session.createQuery(hql2, Ingrediente.class).getResultList();
		
		for (Ingrediente ing : ambCaducitat) {
			System.out.println("  - " + ing.getNombre());
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 8. ORDER BY - Ordenar resultats
	 */
	private static void exempleOrderBy() {
		System.out.println("=== 8. ORDER BY ===");
		
		session.beginTransaction();
		
		/**
		 * ORDER BY: Ordenar resultats
		 * 
		 * Sintaxis: ORDER BY camp [ASC|DESC]
		 * - ASC: Ascendent (per defecte)
		 * - DESC: Descendent
		 * - Pots ordenar per múltiples camps
		 */
		System.out.println("Productes per preu (ascendent):");
		String hql1 = "FROM Producto p ORDER BY p.precio ASC";
		List<Producto> asc = session.createQuery(hql1, Producto.class).getResultList();
		
		for (Producto prod : asc) {
			System.out.println("  - " + prod.getNombre() + ": " + prod.getPrecio() + "€");
		}
		
		System.out.println("\nIngredients per stock (descendent) i nom:");
		String hql2 = "FROM Ingrediente i ORDER BY i.stock DESC, i.nombre ASC";
		List<Ingrediente> desc = session.createQuery(hql2, Ingrediente.class).getResultList();
		
		for (Ingrediente ing : desc) {
			System.out.println("  - " + ing.getNombre() + " (stock: " + ing.getStock() + ")");
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 9. Funcions d'agregació
	 */
	private static void exempleAgregacio() {
		System.out.println("=== 9. AGREGACIÓ ===");
		
		session.beginTransaction();
		
		/**
		 * Funcions d'agregació disponibles:
		 * - COUNT(): Comptar registres
		 * - SUM(): Sumar valors
		 * - AVG(): Mitjana
		 * - MAX(): Valor màxim
		 * - MIN(): Valor mínim
		 */
		
		// COUNT
		Long totalIngredients = session.createQuery(
			"SELECT COUNT(i) FROM Ingrediente i", Long.class
		).getSingleResult();
		System.out.println("Total ingredients: " + totalIngredients);
		
		// SUM
		Long stockTotal = session.createQuery(
			"SELECT SUM(i.stock) FROM Ingrediente i", Long.class
		).getSingleResult();
		System.out.println("Stock total: " + stockTotal);
		
		// AVG
		Double preuMitja = session.createQuery(
			"SELECT AVG(p.precio) FROM Producto p", Double.class
		).getSingleResult();
		System.out.println("Preu mitjà productes: " + String.format("%.2f", preuMitja) + "€");
		
		// MAX
		Double preuMax = session.createQuery(
			"SELECT MAX(p.precio) FROM Producto p", Double.class
		).getSingleResult();
		System.out.println("Preu màxim: " + preuMax + "€");
		
		// MIN
		Integer stockMin = session.createQuery(
			"SELECT MIN(i.stock) FROM Ingrediente i", Integer.class
		).getSingleResult();
		System.out.println("Stock mínim: " + stockMin);
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 10. GROUP BY i HAVING
	 */
	private static void exempleGroupBy() {
		System.out.println("=== 10. GROUP BY i HAVING ===");
		
		session.beginTransaction();
		
		/**
		 * GROUP BY: Agrupar resultats
		 * HAVING: Filtrar grups (com WHERE però per agregacions)
		 * 
		 * Diferència WHERE vs HAVING:
		 * - WHERE: Filtra files abans d'agrupar
		 * - HAVING: Filtra grups després d'agrupar
		 */
		
		System.out.println("Ingredients agrupats per tipus:");
		String hql1 = "SELECT i.tipo, COUNT(i), SUM(i.stock) " +
		              "FROM Ingrediente i " +
		              "GROUP BY i.tipo";
		List<Object[]> grups = session.createQuery(hql1, Object[].class).getResultList();
		
		for (Object[] grup : grups) {
			TipoIngrediente tipo = (TipoIngrediente) grup[0];
			Long count = (Long) grup[1];
			Long stockTotal = (Long) grup[2];
			System.out.println("  " + tipo + ": " + count + " ingredients, stock total: " + stockTotal);
		}
		
		System.out.println("\nTipus amb més de 1 ingredient:");
		String hql2 = "SELECT i.tipo, COUNT(i) " +
		              "FROM Ingrediente i " +
		              "GROUP BY i.tipo " +
		              "HAVING COUNT(i) > 1";
		List<Object[]> grups2 = session.createQuery(hql2, Object[].class).getResultList();
		
		for (Object[] grup : grups2) {
			TipoIngrediente tipo = (TipoIngrediente) grup[0];
			Long count = (Long) grup[1];
			System.out.println("  " + tipo + ": " + count + " ingredients");
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 11. JOINS - Relacionar entitats
	 */
	private static void exempleJoins() {
		System.out.println("=== 11. JOINS ===");
		
		session.beginTransaction();
		
		/**
		 * INNER JOIN: Només resultats amb coincidències a ambdues taules
		 * LEFT JOIN: Tots els de l'esquerra, amb o sense coincidència
		 * RIGHT JOIN: Tots els de la dreta, amb o sense coincidència
		 * 
		 * HQL usa les relacions definides a les entitats (@ManyToOne, etc.)
		 */
		
		System.out.println("INNER JOIN - Clients amb pedidos:");
		String hql1 = "SELECT c.nombre, p.id " +
		              "FROM Cliente c " +
		              "INNER JOIN c.pedidos p";
		List<Object[]> result1 = session.createQuery(hql1, Object[].class).getResultList();
		
		for (Object[] row : result1) {
			String nom = (String) row[0];
			Integer pedidoId = (Integer) row[1];
			System.out.println("  Cliente: " + nom + ", Pedido ID: " + pedidoId);
		}
		
		System.out.println("\nLEFT JOIN - Tots els clients (amb o sense pedidos):");
		String hql2 = "SELECT c.nombre, COUNT(p) " +
		              "FROM Cliente c " +
		              "LEFT JOIN c.pedidos p " +
		              "GROUP BY c.nombre";
		List<Object[]> result2 = session.createQuery(hql2, Object[].class).getResultList();
		
		for (Object[] row : result2) {
			String nom = (String) row[0];
			Long numPedidos = (Long) row[1];
			System.out.println("  Cliente: " + nom + ", Pedidos: " + numPedidos);
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 12. FETCH JOIN - Evitar problema N+1
	 */
	private static void exempleFetchJoin() {
		System.out.println("=== 12. FETCH JOIN (evitar N+1) ===");
		
		session.beginTransaction();
		
		/**
		 * FETCH JOIN: Carrega relacions en una sola query
		 * 
		 * Problema N+1:
		 * - 1 query per carregar clients
		 * - N queries per carregar pedidos de cada client
		 * - Total: 1 + N queries
		 * 
		 * Solució FETCH JOIN:
		 * - 1 sola query carrega tot
		 * 
		 * IMPORTANT: FETCH JOIN no es pot combinar amb setMaxResults() si hi ha múltiples collections
		 */
		System.out.println("Sense FETCH JOIN (múltiples queries):");
		String hql1 = "FROM Cliente c";
		List<Cliente> clients1 = session.createQuery(hql1, Cliente.class).getResultList();
		
		for (Cliente c : clients1) {
			System.out.println("  Cliente: " + c.getNombre());
			// Això genera una query extra per cada client
			System.out.println("    Pedidos: " + c.getPedidos().size());
		}
		
		System.out.println("\nAmb FETCH JOIN (una sola query):");
		String hql2 = "SELECT DISTINCT c FROM Cliente c LEFT JOIN FETCH c.pedidos";
		List<Cliente> clients2 = session.createQuery(hql2, Cliente.class).getResultList();
		
		for (Cliente c : clients2) {
			System.out.println("  Cliente: " + c.getNombre());
			// Ara els pedidos ja estan carregats, sense query extra
			System.out.println("    Pedidos: " + c.getPedidos().size());
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 13. Subconsultes
	 */
	private static void exempleSubconsultes() {
		System.out.println("=== 13. SUBCONSULTES ===");
		
		session.beginTransaction();
		
		/**
		 * Subconsultes: Queries dins de queries
		 * 
		 * Tipus:
		 * - Subconsulta escalar: Retorna un sol valor
		 * - Subconsulta de llista: Retorna múltiples valors (amb IN, EXISTS)
		 */
		
		System.out.println("Productes més cars que la mitjana:");
		String hql1 = "FROM Producto p WHERE p.precio > (SELECT AVG(p2.precio) FROM Producto p2)";
		List<Producto> productes = session.createQuery(hql1, Producto.class).getResultList();
		
		for (Producto prod : productes) {
			System.out.println("  - " + prod.getNombre() + ": " + prod.getPrecio() + "€");
		}
		
		System.out.println("\nIngredients usats en algun producte:");
		String hql2 = "FROM Ingrediente i WHERE EXISTS " +
		              "(SELECT p FROM Producto p JOIN p.ingredients ing WHERE ing.id = i.id)";
		List<Ingrediente> ingredients = session.createQuery(hql2, Ingrediente.class).getResultList();
		
		for (Ingrediente ing : ingredients) {
			System.out.println("  - " + ing.getNombre());
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 14. DISTINCT - Eliminar duplicats
	 */
	private static void exempleDistinct() {
		System.out.println("=== 14. DISTINCT ===");
		
		session.beginTransaction();
		
		/**
		 * DISTINCT: Elimina files duplicades
		 * 
		 * Útil quan fas joins que poden generar duplicats
		 */
		String hql = "SELECT DISTINCT i.tipo FROM Ingrediente i ORDER BY i.tipo";
		List<TipoIngrediente> tipus = session.createQuery(hql, TipoIngrediente.class).getResultList();
		
		System.out.println("Tipus d'ingredients únics:");
		for (TipoIngrediente tipo : tipus) {
			System.out.println("  - " + tipo);
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 15. Projeccions i constructor expressions
	 */
	private static void exempleProjections() {
		System.out.println("=== 15. PROJECCIONS ===");
		
		session.beginTransaction();
		
		/**
		 * Projeccions: Seleccionar només alguns camps
		 * 
		 * Opcions:
		 * 1. Object[]: Array d'objectes
		 * 2. Constructor expression: Crear objectes directament
		 * 3. new map(): Crear mapes
		 * 4. new list(): Crear llistes
		 */
		
		System.out.println("Object[] - Nom i preu de productes:");
		String hql1 = "SELECT p.nombre, p.precio FROM Producto p";
		List<Object[]> result1 = session.createQuery(hql1, Object[].class).getResultList();
		
		for (Object[] row : result1) {
			String nom = (String) row[0];
			Double preu = (Double) row[1];
			System.out.println("  " + nom + ": " + preu + "€");
		}
		
		/**
		 * Constructor expression necessita una classe DTO:
		 * 
		 * public class ProductoDTO {
		 *     private String nombre;
		 *     private Double precio;
		 *     
		 *     public ProductoDTO(String nombre, Double precio) {
		 *         this.nombre = nombre;
		 *         this.precio = precio;
		 *     }
		 * }
		 * 
		 * String hql = "SELECT new paquete.ProductoDTO(p.nombre, p.precio) FROM Producto p";
		 */
		
		System.out.println("\nMap - Stock per tipus:");
		String hql2 = "SELECT new map(i.tipo as tipo, SUM(i.stock) as stockTotal) " +
		              "FROM Ingrediente i GROUP BY i.tipo";
		List result2 = session.createQuery(hql2).getResultList();
		
		for (Object obj : result2) {
			System.out.println("  " + obj);
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 16. UPDATE en massa
	 */
	private static void exempleUpdate() {
		System.out.println("=== 16. UPDATE EN MASSA ===");
		
		session.beginTransaction();
		
		/**
		 * UPDATE: Actualitzar múltiples registres
		 * 
		 * Sintaxis: UPDATE Entitat SET camp = valor WHERE condició
		 * 
		 * IMPORTANT:
		 * - executeUpdate() retorna el nombre de files afectades
		 * - No passa per la cache d'Hibernate
		 * - Més ràpid que carregar objectes i fer merge()
		 * - No dispara events (@PreUpdate, etc.)
		 */
		String hql = "UPDATE Ingrediente i SET i.stock = i.stock + 10 WHERE i.tipo = :tipo";
		Query query = session.createQuery(hql);
		query.setParameter("tipo", TipoIngrediente.VEGETABLE);
		int filesActualitzades = query.executeUpdate();
		
		System.out.println("S'han actualitzat " + filesActualitzades + " ingredients");
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 17. DELETE en massa
	 */
	private static void exempleDelete() {
		System.out.println("=== 17. DELETE EN MASSA ===");
		
		session.beginTransaction();
		
		/**
		 * DELETE: Eliminar múltiples registres
		 * 
		 * Sintaxis: DELETE FROM Entitat WHERE condició
		 * 
		 * PRECAUCIÓ:
		 * - No respecta cascade
		 * - No dispara events (@PreRemove, etc.)
		 * - Pot violar constraints de foreign key
		 * - Usa amb compte!
		 */
		
		// Exemple comentat per no eliminar dades reals
		/*
		String hql = "DELETE FROM Ingrediente i WHERE i.stock = 0";
		Query query = session.createQuery(hql);
		int filesEliminades = query.executeUpdate();
		System.out.println("S'han eliminat " + filesEliminades + " ingredients");
		*/
		
		System.out.println("(Exemple comentat per seguretat)");
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 18. Funcions d'HQL
	 */
	private static void exempleFuncions() {
		System.out.println("=== 18. FUNCIONS HQL ===");
		
		session.beginTransaction();
		
		/**
		 * Funcions disponibles:
		 * 
		 * Strings:
		 * - UPPER(), LOWER(): Majúscules/minúscules
		 * - CONCAT(): Concatenar
		 * - SUBSTRING(): Substring
		 * - LENGTH(): Longitud
		 * - TRIM(), LTRIM(), RTRIM(): Eliminar espais
		 * 
		 * Numèriques:
		 * - ABS(): Valor absolut
		 * - SQRT(): Arrel quadrada
		 * - MOD(): Mòdul
		 * 
		 * Dates:
		 * - CURRENT_DATE(), CURRENT_TIME(), CURRENT_TIMESTAMP()
		 * - YEAR(), MONTH(), DAY()
		 * 
		 * Altres:
		 * - COALESCE(): Primer valor no nul
		 * - NULLIF(): Retorna null si són iguals
		 * - CASE WHEN ... THEN ... ELSE ... END
		 */
		
		System.out.println("Noms en majúscules:");
		String hql1 = "SELECT UPPER(i.nombre) FROM Ingrediente i";
		List<String> noms = session.createQuery(hql1, String.class).getResultList();
		
		for (String nom : noms) {
			System.out.println("  - " + nom);
		}
		
		System.out.println("\nCASE WHEN:");
		String hql2 = "SELECT i.nombre, " +
		              "CASE " +
		              "  WHEN i.stock < 20 THEN 'BAIX' " +
		              "  WHEN i.stock < 50 THEN 'MITJA' " +
		              "  ELSE 'ALT' " +
		              "END " +
		              "FROM Ingrediente i";
		List<Object[]> nivells = session.createQuery(hql2, Object[].class).getResultList();
		
		for (Object[] row : nivells) {
			String nom = (String) row[0];
			String nivell = (String) row[1];
			System.out.println("  " + nom + ": " + nivell);
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 19. Native Query (SQL directe)
	 */
	private static void exempleNativeQuery() {
		System.out.println("=== 19. NATIVE QUERY (SQL) ===");
		
		session.beginTransaction();
		
		/**
		 * Native Query: Executar SQL directament
		 * 
		 * Quan usar:
		 * - Queries molt específiques de la BD
		 * - Funcions SQL no suportades per HQL
		 * - Optimitzacions específiques
		 * 
		 * Desavantatges:
		 * - Perd portabilitat entre BDs
		 * - No usa cache d'Hibernate
		 */
		String sql = "SELECT * FROM Ingrediente WHERE stock > :stock";
		Query<Ingrediente> query = session.createNativeQuery(sql, Ingrediente.class);
		query.setParameter("stock", 30);
		List<Ingrediente> ingredients = query.getResultList();
		
		System.out.println("Ingredients amb stock > 30 (native query):");
		for (Ingrediente ing : ingredients) {
			System.out.println("  - " + ing.getNombre() + " (stock: " + ing.getStock() + ")");
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
	
	/**
	 * 20. Paginació
	 */
	private static void exemplePaginacio() {
		System.out.println("=== 20. PAGINACIÓ ===");
		
		session.beginTransaction();
		
		/**
		 * Paginació: Dividir resultats en pàgines
		 * 
		 * Mètodes:
		 * - setFirstResult(n): Salt els primers n resultats (OFFSET)
		 * - setMaxResults(n): Limita a n resultats (LIMIT)
		 * 
		 * Exemple pàgina 2 amb 2 resultats per pàgina:
		 * - Pàgina 1: resultats 0-1
		 * - Pàgina 2: resultats 2-3
		 * - firstResult = (pàgina - 1) * mida = (2 - 1) * 2 = 2
		 */
		int midaPagina = 2;
		int numeroPagina = 1; // Primera pàgina
		
		System.out.println("Pàgina " + numeroPagina + " (mida: " + midaPagina + "):");
		String hql = "FROM Ingrediente i ORDER BY i.nombre";
		Query<Ingrediente> query = session.createQuery(hql, Ingrediente.class);
		query.setFirstResult((numeroPagina - 1) * midaPagina);
		query.setMaxResults(midaPagina);
		List<Ingrediente> pagina = query.getResultList();
		
		for (Ingrediente ing : pagina) {
			System.out.println("  - " + ing.getNombre());
		}
		
		// Segona pàgina
		numeroPagina = 2;
		System.out.println("\nPàgina " + numeroPagina + ":");
		query.setFirstResult((numeroPagina - 1) * midaPagina);
		pagina = query.getResultList();
		
		for (Ingrediente ing : pagina) {
			System.out.println("  - " + ing.getNombre());
		}
		
		session.getTransaction().commit();
		System.out.println();
	}
}
