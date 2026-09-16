package daoAmbPatro;

import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import org.hibernate.Session;

import elReyDelKebab.Cliente;
import elReyDelKebab.Ingrediente;
import elReyDelKebab.TipoIngrediente;
import jakarta.persistence.Query;

public class IngredientDAO extends GenericDao<Ingrediente, Integer> implements IIngredientDAO{

	@Override
	public List<Ingrediente> listPerTipus(TipoIngrediente tipus) {
		Session session = null;
		
		try {
			session = sessionFactory.openSession();
			
			//:telf es una variable que s'ha d'omplir mitjançant un setparameter
			String hql = "FROM Ingrediente i WHERE i.tipo = :tipus";
			Query query = session.createQuery(hql, Ingrediente.class);
			query.setParameter("tipus", tipus);
			
			// getSingleResult() llança excepció si no hi ha resultats
			List<Ingrediente> resultats = query.getResultList();
			
			return resultats;
			
		} catch (Exception e) {
			System.err.println("Error: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}

	@Override
	public List<Ingrediente> stockMenorQue(int stock) {
		Session session = null;
		
		try {
			session = sessionFactory.openSession();
			
			List<Ingrediente> tots = this.list();
			
			List<Ingrediente> stockBaix = new ArrayList<>();
			for(Ingrediente ing : tots) {
				if(ing.getStock()<stock)stockBaix.add(ing);
			}
			
			return stockBaix;
			
		} catch (Exception e) {
			System.err.println("Error: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}

	@Override
	public int numCaducats(Date d) {
		Session session = null;
		
		try {
			session = sessionFactory.openSession();
			
			List<Ingrediente> tots = this.list();
			int caducados=0;
			for(Ingrediente ing : tots) {
				if(ing.getCaducidad().before(d))caducados=caducados+ing.getStock();
			}
			
			return caducados;
			
		} catch (Exception e) {
			System.err.println("Error: " + e.getMessage());
			throw e;
			
		} finally {
			if (session != null) {
				session.close();
			}
		}
	}
	
	
	

}
