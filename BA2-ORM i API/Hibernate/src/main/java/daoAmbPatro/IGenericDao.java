package daoAmbPatro;

import java.io.Serializable;
import java.util.List;

/**
 * Interfície genèrica per implementar el patró DAO (Data Access Object)
 * @param <T> Tipus de l'entitat que es gestionarà (per exemple, Raton)
 * @param <ID> Tipus de l'identificador de l'entitat (ha de ser Serializable, normalment Integer)
 */
public interface IGenericDao<T, ID extends Serializable> {
    // Mètodes CRUD bàsics que ha d'implementar qualsevol DAO
	// En un DAO han d'estar sempre els 4 tipus CRUD bàsics
	// C - Create
	// R - Read
	// U - Update
	// D - Delete
    
    /**
     * Guarda o actualitza una entitat a la base de dades
     * @param entity Entitat a guardar o actualitzar
     */
    void saveOrUpdate(T entity);

    /**
     * Obté una entitat per la seva ID
     * @param id Identificador de l'entitat
     * @return Entitat trobada o null si no existeix
     */
    T get(ID id);

    /**
     * Elimina una entitat per la seva ID
     * @param id Identificador de l'entitat a eliminar
     */
    void delete(ID id);
    
    /**
     * Elimina una entitat directament
     * @param entity Entitat a eliminar
     */
    void delete(T entity);

    /**
     * Obté totes les entitats d'aquest tipus
     * @return Llista amb totes les entitats
     */
    List<T> list();
}
