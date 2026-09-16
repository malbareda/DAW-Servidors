package fetch;

import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.hibernate.boot.MetadataSources;
import org.hibernate.boot.registry.StandardServiceRegistryBuilder;
import org.hibernate.service.ServiceRegistry;


public class MainFetch {
	
	// Session: representa una connexió amb la BD
    static Session session;
    // SessionFactory: fàbrica de sessions, configurada una vegada per aplicació
    static SessionFactory sessionFactory;
    // ServiceRegistry: registre de serveis d'Hibernate
    static ServiceRegistry serviceRegistry;

    // Mètode per obtenir la SessionFactory de forma thread-safe
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
            // Obtenim una sessió de Hibernate (connexió a la BD)
            session = getSessionFactory().openSession();

            // EXEMPLE 1: CASCADE PERSIST
            session.beginTransaction();

            // Creem les entitats i establim les relacions
            AF a1 = new AF();
            a1.setNomA("A1");
            BF b1 = new BF();
            b1.setNomB("b1");
            BF b2 = new BF();
            b1.setNomB("b2");
            
            // Establim la relació bidireccional entre A i B
            b1.setA(a1);
            b2.setA(a1);
            a1.getB().add(b1);
            a1.getB().add(b2);

            session.persist(a1);
            session.persist(b1);
            session.persist(b2);
            session.getTransaction().commit();
            session.close();

            session = getSessionFactory().openSession();

            session.beginTransaction();
            System.out.println("-**********************-");
            
            System.out.println("FEM EL FIND");
            AF a = session.find(AF.class, 1);

//			Aquest getB funcionaria amb un fetch Lazy, ja que no has tancat la commit i aniria a buscar el que toca
            System.out.println("IMPRIMIM EL NOM");
            System.out.println(a.getNomA());
            System.out.println("IMPRIMIM LA LLISTA");
            //System.out.println(a.getB());

            session.getTransaction().commit();

            
//			Aquest getB funcionaria amb un fetch Lazy, ja que, com que no has tancat la session, crearia una nova commit i tornaria a fer un select, pero seria més lent (una consulta extra)            
            System.out.println("TORNO A IMPRIMIR EL NOM");
            System.out.println(a.getNomA());
            System.out.println("TORNO A IMPRIMIR LA LLISTA");
            //System.out.println(a.getB());

            session.close();
            
            System.out.println("--------------------");
            
//			Aquest getB NO funcionaria amb un fetch Lazy, ja que has tancat la sessio i el fetch mai ha carregat les dades de la OneToMany. Donarà Error!            

            System.out.println(a.getNomA());
            System.out.println(a.getB());

            //session.clear();  // Netegem la sessió
            
            

        } catch (Exception sqlException) {
            sqlException.printStackTrace();
            
        } finally {
            if (session != null) {
                session.close();
            }
        }
    }

}
