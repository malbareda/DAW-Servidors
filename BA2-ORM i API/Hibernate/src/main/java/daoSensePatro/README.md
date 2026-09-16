# El Patró DAO en Hibernate: Guia Completa

## 1. Introducció al Patró DAO

El patró DAO (Data Access Object) és un patró de disseny que permet separar la lògica d'accés a dades de la lògica de negoci. Els principals avantatges són:

- **Encapsulació**: Oculta els detalls de la implementació de la persistència
- **Mantenibilitat**: Facilita els canvis en la capa de persistència
- **Reusabilitat**: Permet reutilitzar el codi d'accés a dades
- **Testabilitat**: Facilita el testing aïllant la lògica de negoci

## 2. Estructura Bàsica del DAO

### 2.1 Interfície Genèrica

```java
public interface IGenericDao<T, ID extends Serializable> {
    void saveOrUpdate(T entity);
    T get(ID id);
    void delete(ID id);
    void delete(T entity);
    List<T> list();
}
```

Aquesta interfície defineix les operacions CRUD bàsiques:
- **C**reate/Update (saveOrUpdate)
- **R**ead (get)
- **U**pdate (saveOrUpdate)
- **D**elete (delete)

### 2.2 Implementació Genèrica

```java
public class GenericDao<T, ID extends Serializable> implements IGenericDao<T, ID> {
    protected SessionFactory sessionFactory;
    
    // Implementació dels mètodes CRUD
}
```

## 3. Gestió de Transaccions

Cada operació que modifica dades ha de ser executada dins d'una transacció:

```java
public void saveOrUpdate(T entity) {
    Session session = sessionFactory.getCurrentSession();
    try {
        session.beginTransaction();
        session.saveOrUpdate(entity);
        session.getTransaction().commit();
    } catch (HibernateException e) {
        if (session.getTransaction() != null) {
            session.getTransaction().rollback();
        }
        e.printStackTrace();
    }
}
```

### Punts Clau en la Gestió de Transaccions:
1. Sempre començar la transacció abans de les operacions
2. Fer commit si tot va bé
3. Fer rollback si hi ha errors
4. Tancar la sessió quan acabem

## 4. Especialització del DAO

### 4.1 Interfície Específica

```java
public interface IRatonDAO extends IGenericDao<Raton, Integer> {
    public int numRatones();
    int cumpleanos(int id);
}
```

### 4.2 Implementació Específica

```java
public class RatonDAO extends GenericDao<Raton, Integer> implements IRatonDAO {
    @Override
    public int numRatones() {
        Session session = sessionFactory.getCurrentSession();
        try {
            session.beginTransaction();
            int size = this.list().size();
            session.getTransaction().commit();
            return size;
        } catch (HibernateException e) {
            // Gestió d'errors
        }
        return -1;
    }
}
```

## 5. Ús de Genèrics en el DAO

Els genèrics ens permeten:
- Reutilitzar codi per diferents entitats
- Tenir type safety en temps de compilació
- Evitar castings explícits

### Exemple d'Ús de Reflection
```java
private Class<T> getEntityClass() {
    return (Class<T>) ((ParameterizedType) getClass()
            .getGenericSuperclass()).getActualTypeArguments()[0];
}
```

Aquest mètode permet obtenir la classe de l'entitat en temps d'execució.

## 6. Exemple d'Ús Complet

```java
public class MainDAO {
    public static void main(String[] args) {
        // Crear instància del DAO
        RatonDAO ratonDAO = new RatonDAO();
        
        // Crear nou ratolí
        Raton rat = new Raton("Miquel", 22, "Negro", 0);
        rat.setFechaNacimiento(new Date());
        
        // Operacions CRUD
        ratonDAO.saveOrUpdate(rat);          // Create
        Raton retrieved = ratonDAO.get(1);   // Read
        ratonDAO.delete(1);                  // Delete
        
        // Operacions específiques
        int total = ratonDAO.numRatones();
        int edad = ratonDAO.cumpleanos(1);
    }
}
```

## 7. Bones Pràctiques

### 7.1 Gestió de Sessions
- Utilitzar `getCurrentSession()` en comptes de `openSession()`
- Tancar sempre les sessions després d'utilitzar-les
- Gestionar correctament les transaccions

### 7.2 Gestió d'Errors
```java
try {
    session.beginTransaction();
    // Operacions...
    session.getTransaction().commit();
} catch (HibernateException e) {
    if (session.getTransaction() != null) {
        session.getTransaction().rollback();
    }
    e.printStackTrace();
} finally {
    if (session != null) {
        session.close();
    }
}
```

### 7.3 Disseny de DAOs
1. Mantenir la interfície genèrica el més simple possible
2. Afegir mètodes específics només quan sigui necessari
3. Documentar els mètodes específics
4. Seguir el principi de responsabilitat única

## 8. Patrons Relacionats

El patró DAO sovint s'utilitza en combinació amb:
- **Factory Pattern**: Per crear instàncies de DAOs
- **Singleton Pattern**: Per gestionar la SessionFactory
- **Repository Pattern**: Com a capa addicional d'abstracció

## 9. Consideracions de Rendiment

1. **Gestió de Sessions**:
   - Reutilitzar sessions quan sigui possible
   - Netejar la sessió periòdicament

2. **Consultes**:
   - Utilitzar consultes específiques en lloc de carregar totes les dades
   - Implementar paginació quan sigui necessari

3. **Transaccions**:
   - No fer transaccions més llargues del necessari
   - Agrupar operacions relacionades en una mateixa transacció

## 10. Troubleshooting Comú

1. **LazyInitializationException**:
   - Causa: Intentar accedir a col·leccions lazy fora d'una transacció
   - Solució: Assegurar-se que l'accés es fa dins de la transacció

2. **StaleObjectStateException**:
   - Causa: Conflictes de concurrència
   - Solució: Implementar estratègies de versioning

3. **TransactionRequiredException**:
   - Causa: Operacions de modificació sense transacció
   - Solució: Assegurar-se d'iniciar la transacció abans de les operacions