package daoAmbPatro;

import java.io.Serializable;
import java.lang.reflect.ParameterizedType;
import java.util.List;

import org.hibernate.HibernateException;
import org.hibernate.Session;
import org.hibernate.SessionFactory;
import org.hibernate.Transaction;
import org.hibernate.boot.Metadata;
import org.hibernate.boot.MetadataSources;
import org.hibernate.boot.registry.StandardServiceRegistry;
import org.hibernate.boot.registry.StandardServiceRegistryBuilder;

/**
 * Implementació genèrica del DAO que proporciona la funcionalitat bàsica CRUD
 * Aquesta classe es pot heretar per crear DAOs específics per cada entitat
 */
public class GenericDao<T, ID extends Serializable> implements IGenericDao<T, ID> {

    protected SessionFactory sessionFactory;

    public GenericDao() {
    	//crides a la sessionFactory desde el singleton
    	sessionFactory = SessionManager.getSessionFactory();

    }

    @Override
    //Podem crear mètodes genèrics epr aquests mètodes perquè al final totes les classes necessitaran un saveOrUpdate
    public void saveOrUpdate(T entity) {
        Session session = sessionFactory.getCurrentSession();
        try {
            // Tota operació de modificació necessita una transacció
            session.beginTransaction();
            session.saveOrUpdate(entity);
            session.getTransaction().commit();
        } catch (HibernateException e) {
            // En cas d'error, fem rollback de la transacció
            handleException(session, e);
        }
    }

    @Override
    public T get(ID id) {
        Session session = sessionFactory.getCurrentSession();
        try {
            session.beginTransaction();
            // Utilitzem el mètode getEntityClass() per obtenir la classe correcta
            T entity = (T) session.get(getEntityClass(), id);
            session.getTransaction().commit();
            return entity;
        } catch (HibernateException e) {
            handleException(session, e);
            return null;
        }
    }

    @Override
    public void delete(ID id) {
        Session session = sessionFactory.getCurrentSession();
        try {
            session.beginTransaction();
            T entity = (T) session.get(getEntityClass(), id);
            session.remove(entity);
            session.getTransaction().commit();
        } catch (HibernateException e) {
            handleException(session, e);
        }
    }

    @Override
    public List<T> list() {
        Session session = sessionFactory.getCurrentSession();
        try {
        	session.beginTransaction();
            // Creem una query HQL per obtenir totes les entitats del tipus T
            List<T> entities = session.createQuery("SELECT e FROM " + getEntityClass().getName() + " e").list();
            return entities;
        } catch (HibernateException e) {
            handleException(session, e);
            return null;
        } finally {
        	session.getTransaction().commit();
        	session.close();
        }
    }

    /**
     * Mètode auxiliar que utilitza reflection per obtenir la classe de l'entitat
     */
    private Class<T> getEntityClass() {
        return (Class<T>) ((ParameterizedType) getClass().getGenericSuperclass()).getActualTypeArguments()[0];
    }

    /**
     * Mètode auxiliar per gestionar les excepcions d'Hibernate
     */
    private void handleException(Session session, HibernateException e) {
        e.printStackTrace();
        if (session != null && session.getTransaction() != null) {
            System.out.println("\n.......Transaction Is Being Rolled Back.......");
            session.getTransaction().rollback();
        }
        e.printStackTrace();
    }

	@Override
	public void delete(T entity) {
		// TODO Auto-generated method stub
		
	}
}
