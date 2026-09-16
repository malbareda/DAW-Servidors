package daoAmbPatro;

import java.util.Date;
import java.util.List;

import elReyDelKebab.Ingrediente;
import elReyDelKebab.TipoIngrediente;

public interface IIngredientDAO extends IGenericDao<Ingrediente, Integer> {
	
	public List<Ingrediente> listPerTipus(TipoIngrediente tipus);
	
	public List<Ingrediente> stockMenorQue(int stock);
	
	public int numCaducats(Date d);
	

}
