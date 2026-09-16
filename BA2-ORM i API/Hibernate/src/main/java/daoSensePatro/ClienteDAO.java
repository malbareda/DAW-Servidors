package daoSensePatro;


import java.util.List;

import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.hibernate.Transaction;

import elReyDelKebab.Cliente;
import jakarta.persistence.Query;

/**
 * ClienteDAO - Data Access Object per la classe Cliente
 * 
 * QUÈ ÉS UN DAO?
 * ===============
 * Un DAO (Data Access Object) és un patró de disseny que separa la lògica de
 * negoci de la lògica d'accés a dades.
 * 
 * OBJECTIU:
 * - Encapsular totes les operacions de base de dades en una classe
 * - Fer el codi més net, organitzat i reutilitzable
 * - Separar responsabilitats: el Main no ha de saber com funciona Hibernate
 * 
 * AVANTATGES:
 * ✓ Codi més net i organitzat
 * ✓ Reutilització: pots fer servir els mateixos mètodes arreu
 * ✓ Mantenibilitat: si canvies la BD, només canvies el DAO
 * ✓ Testing: més fàcil provar cada operació per separat
 * 
 * ESTRUCTURA BÀSICA D'UN DAO:
 * - CRUD: Create, Read, Update, Delete
 * - Consultes personalitzades segons necessitats
 * - Gestió de sessions i transaccions
 */
public class ClienteDAO {
	
	/**
	 * SessionFactory - fàbrica de sessions d'Hibernate
	 * Es passa al constructor per reutilitzar la mateixa configuració
	 */
	private SessionFactory sessionFactory;
	
	/**
	 * Constructor
	 * @param sessionFactory - La fàbrica de sessions configurada
	 */
	public ClienteDAO(SessionFactory sessionFactory) {
		this.sessionFactory = sessionFactory;
	}
	
	// ========== OPERACIONS CRUD ==========
	
	/**
	 * CREATE - Crear (inserir) un nou client a la base de dades
	 * 
	 * @param cliente - El client a inserir
	 * @return El client amb l'ID generat per la BD
	 * 
	 * PASSOS:
	 * 1. Obrir sessió
	 * 2. Iniciar transacció
	 * 3. Persistir l'objecte
	 * 4. Confirmar (commit)
	 * 5. Tancar sessió
	 * 
	 * SQL generat:
	 * INSERT INTO Cliente (nombre, telf, direccion) VALUES (?, ?, ?);
	 */
	public Cliente create(Cliente cliente) {
		Session session = null;
		Transaction transaction = null;
		
		try {
			// 1. Obrir sessió
			session = sessionFactory.openSession();
			
			// 2. Iniciar transacció
			transaction = session.beginTransaction();
			
			// 3. Persistir (INSERT)
			session.persist(cliente);
			
			// 4. Commit - confirmar canvis
			transaction.commit();
			
			System.out.println("✓ Client creat amb ID: " + cliente.getId());
			
			return cliente;
			
		} catch (Exception e) {
			// Si hi ha error, fer rollback
			if (transaction != null) {
				transaction.rollback();
			}
			System.err.println("✗ Error creant client: " + e.getMessage());
			throw e;
			
		} finally {
			// 5. Tancar sessió sempre
			if (session != null) {
				session.close();
			}
		}
	}
	
	/**
	 * READ - Buscar un client per ID
	 * 
	 * @param id - L'ID del client a buscar
	 * @return El client trobat, o null si no existeix
	 * 
	 * SQL generat:
	 * SELECT * FROM Cliente WHERE id = ?;
	 */
	public Cliente findById(int id) {
		Session session = null;
		
		try {
			// Obrir sessió
			session = sessionFactory.openSession();
			
			// Buscar per ID (SELECT)
			Cliente cliente = session.get(Cliente.class, id);
			
			if (cliente != null) {
				System.out.println("✓ Client trobat: " + cliente.getNombre());
			} else {
				System.out.println("✗ Client amb ID " + id + " no existeix");
			}
			
			return cliente;
			
		} catch (Exception e) {
			System.err.println("✗ Error buscant client: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}
	
	/**
	 * READ ALL - Obtenir tots els clients
	 * 
	 * @return Llista amb tots els clients de la BD
	 * 
	 * SQL generat:
	 * SELECT * FROM Cliente;
	 */
	public List<Cliente> findAll() {
		Session session = null;
		
		try {
			session = sessionFactory.openSession();
			
			// Consulta HQL per obtenir tots els clients
			Query query = session.createQuery("FROM Cliente", Cliente.class);
			List<Cliente> clientes = query.getResultList();
			
			System.out.println("✓ S'han trobat " + clientes.size() + " clients");
			
			return clientes;
			
		} catch (Exception e) {
			System.err.println("✗ Error obtenint clients: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}
	
	/**
	 * UPDATE - Actualitzar un client existent
	 * 
	 * @param cliente - El client amb les dades actualitzades
	 * @return El client actualitzat
	 * 
	 * IMPORTANT: El client HA de tenir un ID vàlid (que existeixi a la BD)
	 * 
	 * SQL generat:
	 * UPDATE Cliente SET nombre=?, telf=?, direccion=? WHERE id=?;
	 */
	public Cliente update(Cliente cliente) {
		Session session = null;
		Transaction transaction = null;
		
		try {
			session = sessionFactory.openSession();
			transaction = session.beginTransaction();
			
			// Merge actualitza l'objecte a la BD
			session.merge(cliente);
			
			transaction.commit();
			
			System.out.println("✓ Client actualitzat: " + cliente.getNombre());
			
			return cliente;
			
		} catch (Exception e) {
			if (transaction != null) {
				transaction.rollback();
			}
			System.err.println("✗ Error actualitzant client: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}
	
	/**
	 * DELETE - Eliminar un client per ID
	 * 
	 * @param id - L'ID del client a eliminar
	 * @return true si s'ha eliminat, false si no existia
	 * 
	 * SQL generat:
	 * DELETE FROM Cliente WHERE id=?;
	 */
	public boolean delete(int id) {
		Session session = null;
		Transaction transaction = null;
		
		try {
			session = sessionFactory.openSession();
			transaction = session.beginTransaction();
			
			// Primer buscar el client
			Cliente cliente = session.get(Cliente.class, id);
			
			if (cliente != null) {
				// Si existeix, eliminar-lo
				session.remove(cliente);
				transaction.commit();
				System.out.println("Client eliminat: " + cliente.getNombre());
				return true;
			} else {
				System.out.println("Client amb ID " + id + " no existeix");
				return false;
			}
			
		} catch (Exception e) {
			if (transaction != null) {
				transaction.rollback();
			}
			System.err.println("Error eliminant client: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}
	
	// ========== CONSULTES PERSONALITZADES ==========
	
	/**
	 * Buscar clients per nom (cerca parcial)
	 * 
	 * @param nombre - Part del nom a buscar
	 * @return Llista de clients que coincideixen
	 * 
	 * Exemple: buscarPerNom("Adrian") trobarà "Adrian Vela", "Adrian Sanchez", etc.
	 * 
	 * SQL generat:
	 * SELECT * FROM Cliente WHERE nombre LIKE '%Adrian%';
	 */
	public List<Cliente> buscarPerNom(String nombre) {
		Session session = null;
		
		try {
			session = sessionFactory.openSession();
			
			// HQL amb paràmetre LIKE
			String hql = "FROM Cliente c WHERE c.nombre LIKE :nombre";
			Query query = session.createQuery(hql, Cliente.class);
			query.setParameter("nombre", "%" + nombre + "%");
			
			List<Cliente> clientes = query.getResultList();
			
			System.out.println("✓ Trobats " + clientes.size() + " clients amb nom '" + nombre + "'");
			
			return clientes;
			
		} catch (Exception e) {
			System.err.println("✗ Error buscant per nom: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}
	
	/**
	 * Buscar client per telèfon (cerca exacta)
	 * 
	 * @param telf - El telèfon a buscar
	 * @return El client amb aquest telèfon, o null si no existeix
	 * 
	 * SQL generat:
	 * SELECT * FROM Cliente WHERE telf = ?;
	 */
	public Cliente buscarPerTelefon(String telefon) {
		Session session = null;
		
		try {
			session = sessionFactory.openSession();
			
			//:telf es una variable que s'ha d'omplir mitjançant un setparameter
			String hql = "FROM Cliente c WHERE c.telf = :telf";
			Query query = session.createQuery(hql, Cliente.class);
			query.setParameter("telf", telefon);
			
			// getSingleResult() llança excepció si no hi ha resultats
			List<Cliente> resultats = query.getResultList();
			
			if (!resultats.isEmpty()) {
				Cliente cliente = resultats.get(0);
				System.out.println("Client trobat per telèfon: " + cliente.getNombre());
				return cliente;
			} else {
				System.out.println("No s'ha trobat cap client amb telèfon " + telefon);
				return null;
			}
			
		} catch (Exception e) {
			System.err.println("Error buscant per telèfon: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}
	
	/**
	 * Comptar quants clients hi ha a la base de dades
	 * 
	 * @return Nombre total de clients
	 * 
	 */
	public long contarClients() {
		Session session = null;
		
		try {
			//RECICLAR CODI MOLA
			return this.findAll().size();
			
		} catch (Exception e) {
			System.err.println("✗ Error comptant clients: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}

}