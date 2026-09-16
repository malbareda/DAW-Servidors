package Inheritance;

import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.Date;
import java.util.Random;

import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.hibernate.boot.MetadataSources;
import org.hibernate.boot.registry.StandardServiceRegistryBuilder;
import org.hibernate.service.ServiceRegistry;

/**
 * Test que demostra l'estratègia d'herència TABLE_PER_CLASS.
 * 
 * Aquest exemple crea:
 * - 1 peluqueria hipster (PeluqueriaHipster)
 * - 1 peluqueria perroflauta (PeluqueriaPerroflauta)
 * - Barcelona, associada a la peluqueria perroflauta
 * 
 * A la BD es crearà:
 * - Taula PeluqueriaHipster
 *   └─ Columnes: idPelu, nom (heretats), olorPeculiar (propi)
 * - Taula PeluqueriaPerroflauta
 *   └─ Columnes: idPelu, nom (heretats), chinches (propi), carcel_id (FK propi)
 * - Taula Barcelona
 * 
 * IMPORTANT: NO es crea cap taula "Peluqueria". Només taules per les classes concretes.
 * Cada taula conté TOTS els camps necessaris (heretats + propis).
 */
public class TestInheritance2 {

	// Session: representa una connexió amb la BD
	static Session session;
	// SessionFactory: fàbrica de sessions, configurada una vegada per aplicació
	static SessionFactory sessionFactory;
	// ServiceRegistry: registre de serveis d'Hibernate
	static ServiceRegistry serviceRegistry;

	public static synchronized SessionFactory getSessionFactory() {
		if (sessionFactory == null) {
			// Crea el registre de serveis utilitzant el fitxer hibernate.cfg.xml
			serviceRegistry = new StandardServiceRegistryBuilder().configure("hibernate.cfg.xml").build();
			sessionFactory = new MetadataSources(serviceRegistry).buildMetadata().buildSessionFactory();
		}
		return sessionFactory;
	}

	public static void main(String[] args) {
		try {
			// creo la sesion, esto es conectarse a la BD sin hacer nada
			session = getSessionFactory().openSession();

			// abrimos PRIMERA transaccion. Eso se hace siempre.
			// Cada operació d'escriptura necessita una transacció
			session.beginTransaction();
			
			// CREAR OBJECTES
			// Peluqueria hipster - es guardarà a la taula PeluqueriaHipster
			PeluqueriaHipster p = new PeluqueriaHipster();
			p.setNom("Anabel");
			p.setOlorPeculiar(true); // Camp específic, només existeix a PeluqueriaHipster
			
			// Peluqueria perroflauta - es guardarà a la taula PeluqueriaPerroflauta
			PeluqueriaPerroflauta f = new PeluqueriaPerroflauta();
			f.setNom("Rastatistan");
			f.setChinches(Integer.MAX_VALUE); // Camp específic, només existeix a PeluqueriaPerroflauta
			
			// Barcelona - es relaciona només amb peluqueries perroflautas
			Barcelona b = new Barcelona();
			b.setAlcaldesa("Colau");
			
			// ESTABLIR RELACIONS BIDIRECCIONALS
			// Barcelona <-> PeluqueriaPerroflauta
			// Només la taula PeluqueriaPerroflauta tindrà la FK carcel_id
			// La taula PeluqueriaHipster NO té aquesta columna
			b.getPelus().add(f);
			f.setCarcel(b);
			
			// PERSISTIR - guardar a la BD
			// Les peluqueries es guarden en taules diferents i independents
			session.persist(p);  // Va a PeluqueriaHipster (idPelu, nom, olorPeculiar)
			session.persist(f);  // Va a PeluqueriaPerroflauta (idPelu, nom, chinches, carcel_id)
			session.persist(b);  // Va a Barcelona
			
			// Confirmar la transacció
			session.getTransaction().commit();

			System.out.println("todo ha salido a pedir de Milhouse 2");
		} catch (Exception sqlException) {
			sqlException.printStackTrace();
			if (null != session.getTransaction()) {
				System.out.println("\n.......Transaction Is Being Rolled Back.......");
				// Si hi ha error, es fa rollback de la transacció
				session.getTransaction().rollback();
			}
			sqlException.printStackTrace();
		} finally {
			// Sempre cal tancar la sessió
			if (session != null) {
				session.close();
			}
		}

	}
}