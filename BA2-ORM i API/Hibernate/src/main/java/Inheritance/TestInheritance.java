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
 * Test que demostra l'estratègia d'herència SINGLE_TABLE.
 * 
 * Aquest exemple crea:
 * - 1 professor (Profe)
 * - 1 alumne bo (AlumnoBueno)
 * - 1 alumne dolent (AlumnoMalo)
 * - 1 presó (Carcel) associada a l'alumne dolent
 * 
 * A la BD es crearà:
 * - Taula Profe
 * - Taula Alumno (amb TOTS els alumnes en una sola taula)
 *   └─ Columnes: idAlumno, nombreAlumno, tipo_de_alumno (discriminador), 
 *                cosaBuena (NULL per dolents), cosaMala (NULL per bons),
 *                profe_id (FK), carcel_id (FK, NULL per bons)
 * - Taula Carcel
 */
public class TestInheritance {

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
			// Professor
			Profe p = new Profe();
			p.setNomProfe("Marc Llaó");
			
			// Alumne bo - es guardarà amb tipo_de_alumno="Buenos"
			AlumnoBueno xaviC = new AlumnoBueno();
			xaviC.setNombreAlumno("Xavi Conesa");
			xaviC.setCosaBuena(2); // Aquest camp només existeix per als bons
			
			// Alumne dolent - es guardarà amb tipo_de_alumno="Malos"
			AlumnoMalo ericH = new AlumnoMalo();
			ericH.setNombreAlumno("Eric Homofobo");
			ericH.setCosaMala(true); // Aquest camp només existeix per als dolents
			
			// Presó - només per als alumnes dolents
			Carcel c = new Carcel();
			c.setNombreCarcel("Frontend, feat. Eloi");
			
			// ESTABLIR RELACIONS BIDIRECCIONALS
			// Relació Carcel <-> AlumnoMalo (específica dels dolents)
			c.getAlumnos().add(ericH);
			ericH.setCarcel(c);
			
			// Relació Profe <-> Alumno (per tots els alumnes)
			p.getAlumnos().add(ericH);
			p.getAlumnos().add(xaviC);
			
			xaviC.setProfe(p);
			ericH.setProfe(p);
			
			// PERSISTIR - guardar a la BD
			// Cal persistir en l'ordre correcte segons les dependències
			session.persist(p);      // Primer el profe (no depèn de ningú)
			session.persist(c);      // La presó (no depèn de ningú)
			session.persist(xaviC);  // Després els alumnes (depenen de profe i potser presó)
			session.persist(ericH);
			
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