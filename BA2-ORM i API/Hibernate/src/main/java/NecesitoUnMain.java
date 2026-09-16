import org.hibernate.*;
import org.hibernate.boot.MetadataSources;
import org.hibernate.boot.registry.StandardServiceRegistryBuilder;
import org.hibernate.service.ServiceRegistry;

public class NecesitoUnMain {
	
	
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
	            //creo la sesion, esto es conectarse a la BD sin hacer nada
	            session = getSessionFactory().openSession();

	            // abrimos PRIMERA transaccion. Eso se hace siempre.
	            // Cada operació d'escriptura necessita una transacció
	            session.beginTransaction();
	            
	            Shipeos sh = new Shipeos();
	            sh.setP1("Grau");
	            sh.setP2("Xavi");
	            sh.setNombreShip("Gravi");
	            sh.setCanon(false);
	            sh.setLikes(0);
	            
	            //persist es para meter algo en BD, pensad en insert
	            session.persist(sh);
	            
	            session.getTransaction().commit();
	            
	            
	        }catch (Exception sqlException) {
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
