package daoAmbPatro;

import java.util.Date;

import elReyDelKebab.Ingrediente;
import elReyDelKebab.TipoIngrediente;

public class MainTestDAOIng {
	
	public static void main(String[] args) {
		
		
		IngredientDAO idao = new IngredientDAO();
		
		Ingrediente ing = new Ingrediente();
		
		ing.setNombre("Lechuga");
		Date d = new Date();
		d.setDate(d.getDate()-2);
		ing.setCaducidad(d);
		ing.setTipo(TipoIngrediente.VEGETABLE);
		ing.setStock(2);
		
		idao.saveOrUpdate(ing);
		
		System.out.println(idao.listPerTipus(TipoIngrediente.VEGETABLE));
		System.out.println(idao.numCaducats(new Date()));
		
		
	}

}
