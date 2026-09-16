package daoAmbPatro;

import org.hibernate.SessionFactory;
import org.hibernate.boot.Metadata;
import org.hibernate.boot.MetadataSources;
import org.hibernate.boot.registry.StandardServiceRegistry;
import org.hibernate.boot.registry.StandardServiceRegistryBuilder;


public class SessionManager {
	
	private static SessionFactory sessionFactory;
	
	//estructura singleton
	public static SessionFactory getSessionFactory() {
		if (sessionFactory == null) {
            // Configuració bàsica d'Hibernate utilitzant el fitxer hibernate.cfg.xml
            StandardServiceRegistry serviceRegistry = new StandardServiceRegistryBuilder()
                .configure("hibernate.cfg.xml")
                .build();
            
            Metadata metadata = new MetadataSources(serviceRegistry).getMetadataBuilder().build();
            sessionFactory = metadata.getSessionFactoryBuilder().build();
        }
		return sessionFactory;
	}

}
