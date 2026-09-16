# HQL: Hibernate Query Language
## Guia completa per a estudiants de Cicles Formatius de Grau Superior

### Índex
1. [Introducció a HQL](#1-introducció-a-hql)
2. [Sintaxi Bàsica](#2-sintaxi-bàsica)
3. [Clàusula WHERE](#3-clàusula-where)
4. [Paràmetres](#4-paràmetres)
5. [Operadors](#5-operadors)
6. [Funcions de Text](#6-funcions-de-text)
7. [Ordenació i Limitació](#7-ordenació-i-limitació)
8. [Agregacions](#8-agregacions)
9. [Agrupació](#9-agrupació)
10. [Joins](#10-joins)
11. [Subconsultes](#11-subconsultes)
12. [Projeccions](#12-projeccions)
13. [Operacions en Massa](#13-operacions-en-massa)
14. [Funcions Avançades](#14-funcions-avançades)
15. [Native Queries](#15-native-queries)
16. [Paginació](#16-paginació)
17. [Bones Pràctiques](#17-bones-pràctiques)

---

## 1. Introducció a HQL

### 1.1 Què és HQL?
HQL (Hibernate Query Language) és un llenguatge de consultes orientat a objectes similar a SQL, però que treballa amb entitats Java en lloc de taules de base de dades.

**Avantatges d'HQL:**
- **Portabilitat**: Les queries funcionen en qualsevol base de dades
- **Orientat a objectes**: Treballa amb classes i atributs Java
- **Type-safe**: El compilador detecta errors de tipus
- **Integració**: S'integra perfectament amb el cache d'Hibernate
- **Mantenibilitat**: Més fàcil de llegir i mantenir que SQL

**Diferències HQL vs SQL:**
```java
// SQL (dependent de la BD)
SELECT * FROM ingrediente WHERE tipo = 'VEGETABLE';

// HQL (independent de la BD, usa noms Java)
FROM Ingrediente i WHERE i.tipo = 'VEGETABLE'
```

### 1.2 Estructura Bàsica
```java
// Crear la query
Query<Ingrediente> query = session.createQuery(
    "FROM Ingrediente i WHERE i.stock > 10", 
    Ingrediente.class
);

// Executar i obtenir resultats
List<Ingrediente> resultats = query.getResultList();

// O un sol resultat
Ingrediente ingredient = query.getSingleResult();
```

---

## 2. Sintaxi Bàsica

### 2.1 SELECT Simple
```java
// SELECT implícit (retorna entitats completes)
FROM Ingrediente

// SELECT explícit (retorna entitats completes)
SELECT i FROM Ingrediente i

// SELECT camps específics (retorna Object[])
SELECT i.nombre, i.stock FROM Ingrediente i
```

**Exemple complet:**
```java
session.beginTransaction();

// Tots els ingredients
Query<Ingrediente> query = session.createQuery("FROM Ingrediente", Ingrediente.class);
List<Ingrediente> ingredients = query.getResultList();

for (Ingrediente ing : ingredients) {
    System.out.println(ing.getNombre());
}

session.getTransaction().commit();
```

### 2.2 Àlies
Els àlies fan les queries més llegibles:
```java
// Sense àlies
FROM Ingrediente WHERE nombre = 'Tomaquet'

// Amb àlies (recomanat)
FROM Ingrediente i WHERE i.nombre = 'Tomaquet'
```

---

## 3. Clàusula WHERE

### 3.1 Filtres Bàsics
```java
// Comparació simple
FROM Ingrediente i WHERE i.stock > 20

// Comparació d'Strings
FROM Ingrediente i WHERE i.nombre = 'Tomaquet'

// Comparació d'Enums
FROM Ingrediente i WHERE i.tipo = 'VEGETABLE'
```

### 3.2 LIKE (Patrons de Text)
```java
// Comença amb
FROM Ingrediente i WHERE i.nombre LIKE 'To%'

// Acaba amb
FROM Ingrediente i WHERE i.nombre LIKE '%quet'

// Conté
FROM Ingrediente i WHERE i.nombre LIKE '%oma%'

// Un sol caràcter qualsevol (_)
FROM Ingrediente i WHERE i.nombre LIKE 'T_maquet'
```

**Exemple amb paràmetres:**
```java
String hql = "FROM Ingrediente i WHERE i.nombre LIKE :patro";
Query<Ingrediente> query = session.createQuery(hql, Ingrediente.class);
query.setParameter("patro", "%a%");
List<Ingrediente> resultats = query.getResultList();
```

### 3.3 IN (Llista de Valors)
```java
// IN amb literals
FROM Ingrediente i WHERE i.tipo IN ('VEGETABLE', 'CARNE')

// IN amb paràmetres
String hql = "FROM Ingrediente i WHERE i.tipo IN :tipus";
query.setParameter("tipus", List.of(TipoIngrediente.VEGETABLE, TipoIngrediente.CARNE));
```

### 3.4 BETWEEN (Rangs)
```java
// BETWEEN numèric
FROM Producto p WHERE p.precio BETWEEN 2.0 AND 5.0

// BETWEEN dates
FROM Pedido p WHERE p.fechaPedido BETWEEN :dataInici AND :dataFi
```

### 3.5 IS NULL / IS NOT NULL
```java
// Ingredients sense caducitat
FROM Ingrediente i WHERE i.caducidad IS NULL

// Ingredients amb caducitat
FROM Ingrediente i WHERE i.caducidad IS NOT NULL
```

---

## 4. Paràmetres

### 4.1 Paràmetres Named (Recomanat)
```java
// Sintaxi: :nomParametre
String hql = "FROM Ingrediente i WHERE i.stock > :stockMinim AND i.tipo = :tipus";

Query<Ingrediente> query = session.createQuery(hql, Ingrediente.class);
query.setParameter("stockMinim", 25);
query.setParameter("tipus", TipoIngrediente.VEGETABLE);

List<Ingrediente> resultats = query.getResultList();
```

**Avantatges:**
- Més llegible
- L'ordre no importa
- Millor per mantenir

### 4.2 Paràmetres Positional (Menys Recomanat)
```java
// Sintaxi: ?1, ?2, ?3...
String hql = "FROM Producto p WHERE p.precio < ?1 AND p.nombre LIKE ?2";

Query<Producto> query = session.createQuery(hql, Producto.class);
query.setParameter(1, 5.00);
query.setParameter(2, "%Kebab%");

List<Producto> resultats = query.getResultList();
```

**Desavantatges:**
- Menys llegible
- L'ordre importa
- Propensos a errors

### 4.3 Paràmetres de Tipus Especials
```java
// Dates
query.setParameter("data", new Date());

// Enums
query.setParameter("tipus", TipoIngrediente.CARNE);

// Collections
query.setParameter("ids", List.of(1, 2, 3));
```

---

## 5. Operadors

### 5.1 Operadors de Comparació
```java
// Igualtat
FROM Ingrediente i WHERE i.stock = 50

// Diferent
FROM Ingrediente i WHERE i.stock <> 0
FROM Ingrediente i WHERE i.stock != 0

// Major/Menor
FROM Producto p WHERE p.precio > 5.0
FROM Producto p WHERE p.precio < 3.0
FROM Producto p WHERE p.precio >= 4.5
FROM Producto p WHERE p.precio <= 6.0
```

### 5.2 Operadors Lògics
```java
// AND
FROM Ingrediente i WHERE i.stock > 20 AND i.tipo = 'VEGETABLE'

// OR
FROM Ingrediente i WHERE i.tipo = 'CARNE' OR i.tipo = 'BEBIDA'

// NOT
FROM Ingrediente i WHERE NOT (i.stock < 10)
```

### 5.3 Operadors Aritmètics
```java
// Suma, resta, multiplicació, divisió
SELECT p.precio * 1.21 FROM Producto p

// Preu amb IVA
SELECT p.nombre, p.precio * 1.21 as precioConIVA FROM Producto p
```

---

## 6. Funcions de Text

### 6.1 Funcions de Conversió
```java
// UPPER - Convertir a majúscules
SELECT UPPER(i.nombre) FROM Ingrediente i

// LOWER - Convertir a minúscules
SELECT LOWER(i.nombre) FROM Ingrediente i
```

### 6.2 Funcions de Manipulació
```java
// CONCAT - Concatenar strings
SELECT CONCAT(c.nombre, ' - ', c.telf) FROM Cliente c

// SUBSTRING - Extreure substring
SELECT SUBSTRING(i.nombre, 1, 3) FROM Ingrediente i

// LENGTH - Longitud
SELECT i.nombre FROM Ingrediente i WHERE LENGTH(i.nombre) > 5

// TRIM - Eliminar espais
SELECT TRIM(c.nombre) FROM Cliente c
```

---

## 7. Ordenació i Limitació

### 7.1 ORDER BY
```java
// Ordenació ascendent (per defecte)
FROM Producto p ORDER BY p.precio

// Ordenació ascendent explícita
FROM Producto p ORDER BY p.precio ASC

// Ordenació descendent
FROM Producto p ORDER BY p.precio DESC

// Múltiples camps
FROM Ingrediente i ORDER BY i.tipo ASC, i.nombre ASC
```

**Exemple complet:**
```java
session.beginTransaction();

String hql = "FROM Producto p ORDER BY p.precio DESC";
Query<Producto> query = session.createQuery(hql, Producto.class);
List<Producto> productes = query.getResultList();

for (Producto p : productes) {
    System.out.println(p.getNombre() + ": " + p.getPrecio() + "€");
}

session.getTransaction().commit();
```

### 7.2 Limitació de Resultats
```java
// LIMIT (primers N resultats)
query.setMaxResults(10);

// Top 5 productes més cars
String hql = "FROM Producto p ORDER BY p.precio DESC";
Query<Producto> query = session.createQuery(hql, Producto.class);
query.setMaxResults(5);
List<Producto> top5 = query.getResultList();
```

---

## 8. Agregacions

### 8.1 Funcions d'Agregació Bàsiques
```java
// COUNT - Comptar registres
SELECT COUNT(i) FROM Ingrediente i

// COUNT DISTINCT - Comptar valors únics
SELECT COUNT(DISTINCT i.tipo) FROM Ingrediente i

// SUM - Sumar valors
SELECT SUM(i.stock) FROM Ingrediente i

// AVG - Mitjana
SELECT AVG(p.precio) FROM Producto p

// MAX - Valor màxim
SELECT MAX(p.precio) FROM Producto p

// MIN - Valor mínim
SELECT MIN(i.stock) FROM Ingrediente i
```

**Exemple complet:**
```java
session.beginTransaction();

// Stock total
Long stockTotal = session.createQuery(
    "SELECT SUM(i.stock) FROM Ingrediente i", 
    Long.class
).getSingleResult();

System.out.println("Stock total: " + stockTotal);

// Preu mitjà
Double preuMitja = session.createQuery(
    "SELECT AVG(p.precio) FROM Producto p", 
    Double.class
).getSingleResult();

System.out.println("Preu mitjà: " + String.format("%.2f", preuMitja) + "€");

session.getTransaction().commit();
```

### 8.2 Agregacions amb Filtres
```java
// Stock total de vegetables
SELECT SUM(i.stock) FROM Ingrediente i WHERE i.tipo = 'VEGETABLE'

// Preu mitjà de productes cars
SELECT AVG(p.precio) FROM Producto p WHERE p.precio > 3.0
```

---

## 9. Agrupació

### 9.1 GROUP BY
```java
// Agrupar per un camp
SELECT i.tipo, COUNT(i) 
FROM Ingrediente i 
GROUP BY i.tipo

// Agrupar per múltiples camps
SELECT c.nombre, COUNT(p) 
FROM Cliente c 
LEFT JOIN c.pedidos p 
GROUP BY c.nombre
```

**Exemple complet:**
```java
session.beginTransaction();

String hql = "SELECT i.tipo, COUNT(i), SUM(i.stock) " +
             "FROM Ingrediente i " +
             "GROUP BY i.tipo";

List<Object[]> grups = session.createQuery(hql, Object[].class).getResultList();

for (Object[] grup : grups) {
    TipoIngrediente tipo = (TipoIngrediente) grup[0];
    Long count = (Long) grup[1];
    Long stockTotal = (Long) grup[2];
    
    System.out.println(tipo + ": " + count + " ingredients, stock: " + stockTotal);
}

session.getTransaction().commit();
```

### 9.2 HAVING
```java
// Filtrar grups amb COUNT
SELECT i.tipo, COUNT(i) 
FROM Ingrediente i 
GROUP BY i.tipo 
HAVING COUNT(i) > 1

// Filtrar grups amb SUM
SELECT i.tipo, SUM(i.stock) 
FROM Ingrediente i 
GROUP BY i.tipo 
HAVING SUM(i.stock) > 50
```

**Diferència WHERE vs HAVING:**
```java
// WHERE: Filtra files ABANS d'agrupar
SELECT i.tipo, COUNT(i) 
FROM Ingrediente i 
WHERE i.stock > 10
GROUP BY i.tipo

// HAVING: Filtra grups DESPRÉS d'agrupar
SELECT i.tipo, COUNT(i) 
FROM Ingrediente i 
GROUP BY i.tipo 
HAVING COUNT(i) > 2
```

---

## 10. Joins

### 10.1 INNER JOIN
```java
// INNER JOIN - Només coincidències
SELECT c.nombre, p.id 
FROM Cliente c 
INNER JOIN c.pedidos p

// Forma curta (implícita)
SELECT c.nombre, p.id 
FROM Cliente c 
JOIN c.pedidos p
```

### 10.2 LEFT JOIN
```java
// LEFT JOIN - Tots els de l'esquerra + coincidències
SELECT c.nombre, COUNT(p) 
FROM Cliente c 
LEFT JOIN c.pedidos p 
GROUP BY c.nombre

// Resultat: Inclou clients sense pedidos (count = 0)
```

### 10.3 RIGHT JOIN
```java
// RIGHT JOIN - Tots els de la dreta + coincidències
SELECT p.id, c.nombre 
FROM Cliente c 
RIGHT JOIN c.pedidos p

// Nota: Menys comú en HQL
```

### 10.4 FETCH JOIN (Evitar N+1)
```java
// Sense FETCH JOIN - Múltiples queries (problema N+1)
FROM Cliente c
// Per cada client, carrega pedidos amb query separada

// Amb FETCH JOIN - Una sola query
SELECT DISTINCT c 
FROM Cliente c 
LEFT JOIN FETCH c.pedidos

// IMPORTANT: Carrega totes les relacions en una query
```

**Exemple problema N+1:**
```java
// ❌ MAL - Genera N+1 queries
String hql = "FROM Cliente c";
List<Cliente> clients = session.createQuery(hql, Cliente.class).getResultList();

for (Cliente c : clients) {
    // Cada iteració genera una query extra!
    System.out.println("Pedidos: " + c.getPedidos().size());
}

// ✅ BÉ - Una sola query
String hql = "SELECT DISTINCT c FROM Cliente c LEFT JOIN FETCH c.pedidos";
List<Cliente> clients = session.createQuery(hql, Cliente.class).getResultList();

for (Cliente c : clients) {
    // Pedidos ja estan carregats
    System.out.println("Pedidos: " + c.getPedidos().size());
}
```

### 10.5 Joins Múltiples
```java
// Join de múltiples taules
SELECT p.id, c.nombre, m.nombre 
FROM Pedido p 
JOIN p.cliente c 
JOIN p.menuses m
```

---

## 11. Subconsultes

### 11.1 Subconsultes Escalars
```java
// Subconsulta que retorna un sol valor
FROM Producto p 
WHERE p.precio > (SELECT AVG(p2.precio) FROM Producto p2)

// Productes més cars que la mitjana
```

### 11.2 Subconsultes amb IN
```java
// Subconsulta que retorna llista de valors
FROM Ingrediente i 
WHERE i.id IN (
    SELECT ing.id 
    FROM Producto p 
    JOIN p.ingredients ing 
    WHERE p.precio > 5.0
)

// Ingredients usats en productes cars
```

### 11.3 EXISTS
```java
// Comprovar existència
FROM Ingrediente i 
WHERE EXISTS (
    SELECT p 
    FROM Producto p 
    JOIN p.ingredients ing 
    WHERE ing.id = i.id
)

// Ingredients que s'usen en algun producte
```

### 11.4 NOT EXISTS
```java
// Comprovar no existència
FROM Ingrediente i 
WHERE NOT EXISTS (
    SELECT p 
    FROM Producto p 
    JOIN p.ingredients ing 
    WHERE ing.id = i.id
)

// Ingredients que NO s'usen en cap producte
```

---

## 12. Projeccions

### 12.1 Object[] (Array)
```java
// Seleccionar camps específics
SELECT p.nombre, p.precio FROM Producto p

// Resultat: List<Object[]>
List<Object[]> resultats = session.createQuery(hql, Object[].class).getResultList();

for (Object[] row : resultats) {
    String nom = (String) row[0];
    Double preu = (Double) row[1];
    System.out.println(nom + ": " + preu + "€");
}
```

### 12.2 Constructor Expression (DTO)
```java
// Crear objectes DTO directament
// 1. Crear classe DTO
public class ProductoDTO {
    private String nombre;
    private Double precio;
    
    public ProductoDTO(String nombre, Double precio) {
        this.nombre = nombre;
        this.precio = precio;
    }
    // Getters...
}

// 2. Usar en HQL
SELECT new paquete.ProductoDTO(p.nombre, p.precio) FROM Producto p

// Resultat: List<ProductoDTO>
List<ProductoDTO> dtos = session.createQuery(hql, ProductoDTO.class).getResultList();
```

### 12.3 Map
```java
// Crear mapes
SELECT new map(i.tipo as tipo, SUM(i.stock) as stockTotal) 
FROM Ingrediente i 
GROUP BY i.tipo

// Resultat: List<Map>
```

### 12.4 List
```java
// Crear llistes
SELECT new list(p.nombre, p.precio) FROM Producto p

// Similar a Object[] però retorna List en lloc d'array
```

---

## 13. Operacions en Massa

### 13.1 UPDATE
```java
// Actualitzar múltiples registres
String hql = "UPDATE Ingrediente i SET i.stock = i.stock + 10 WHERE i.tipo = :tipo";

session.beginTransaction();

Query query = session.createQuery(hql);
query.setParameter("tipo", TipoIngrediente.VEGETABLE);
int filesActualitzades = query.executeUpdate();

System.out.println("Actualitzats: " + filesActualitzades);

session.getTransaction().commit();
```

**Característiques:**
- Més ràpid que carregar objectes i fer merge()
- No passa per la cache d'Hibernate
- No dispara events (@PreUpdate, @PostUpdate)
- Retorna nombre de files afectades

### 13.2 DELETE
```java
// Eliminar múltiples registres
String hql = "DELETE FROM Ingrediente i WHERE i.stock = 0";

session.beginTransaction();

Query query = session.createQuery(hql);
int filesEliminades = query.executeUpdate();

System.out.println("Eliminats: " + filesEliminades);

session.getTransaction().commit();
```

**PRECAUCIÓ:**
- No respecta cascade
- No dispara events (@PreRemove, @PostRemove)
- Pot violar constraints de foreign key
- Usa amb compte!

### 13.3 INSERT ... SELECT (Hibernate 6+)
```java
// Copiar dades entre taules
INSERT INTO ArchivoIngrediente (nombre, tipo) 
SELECT i.nombre, i.tipo 
FROM Ingrediente i 
WHERE i.caducidad < CURRENT_DATE
```

---

## 14. Funcions Avançades

### 14.1 Funcions de Data
```java
// Data/hora actual
SELECT CURRENT_DATE FROM Ingrediente i
SELECT CURRENT_TIME FROM Ingrediente i
SELECT CURRENT_TIMESTAMP FROM Ingrediente i

// Extreure components
SELECT YEAR(p.fechaPedido), MONTH(p.fechaPedido), DAY(p.fechaPedido) 
FROM Pedido p
```

### 14.2 Funcions Matemàtiques
```java
// ABS - Valor absolut
SELECT ABS(p.precio - 5.0) FROM Producto p

// SQRT - Arrel quadrada
SELECT SQRT(i.stock) FROM Ingrediente i

// MOD - Mòdul
SELECT MOD(i.stock, 10) FROM Ingrediente i
```

### 14.3 COALESCE
```java
// Retorna el primer valor no nul
SELECT COALESCE(i.caducidad, CURRENT_DATE) FROM Ingrediente i

// Útil per valors per defecte
SELECT COALESCE(c.telf, 'Sense telèfon') FROM Cliente c
```

### 14.4 NULLIF
```java
// Retorna null si són iguals
SELECT NULLIF(i.stock, 0) FROM Ingrediente i

// Evitar divisions per zero
SELECT i.precio / NULLIF(i.stock, 0) FROM Ingrediente i
```

### 14.5 CASE WHEN
```java
// Lògica condicional
SELECT i.nombre,
    CASE 
        WHEN i.stock < 20 THEN 'BAIX'
        WHEN i.stock < 50 THEN 'MITJA'
        ELSE 'ALT'
    END as nivellStock
FROM Ingrediente i

// CASE simple
SELECT p.nombre,
    CASE p.tipo
        WHEN 'VEGETABLE' THEN 'Verdura'
        WHEN 'CARNE' THEN 'Carn'
        ELSE 'Altre'
    END
FROM Producto p
```

---

## 15. Native Queries

### 15.1 Què són les Native Queries?
SQL directe executat per Hibernate, útil per queries molt específiques o funcions SQL no suportades per HQL.

```java
// Native Query bàsica
String sql = "SELECT * FROM Ingrediente WHERE stock > :stock";

Query<Ingrediente> query = session.createNativeQuery(sql, Ingrediente.class);
query.setParameter("stock", 30);
List<Ingrediente> resultats = query.getResultList();
```

### 15.2 Native Query amb Resultats Escalars
```java
// Resultat no mapejat a entitat
String sql = "SELECT nombre, stock FROM Ingrediente WHERE tipo = 'VEGETABLE'";

Query<Object[]> query = session.createNativeQuery(sql, Object[].class);
List<Object[]> resultats = query.getResultList();

for (Object[] row : resultats) {
    String nom = (String) row[0];
    Integer stock = (Integer) row[1];
    System.out.println(nom + ": " + stock);
}
```

### 15.3 Quan Usar Native Queries
**Usa HQL quan:**
- Queries estàndard
- Vols portabilitat entre BDs
- Vols aprofitar cache d'Hibernate

**Usa Native SQL quan:**
- Funcions específiques de la BD (ex: funcions de PostgreSQL)
- Queries molt optimitzades
- Queries complexes que HQL no suporta

---

## 16. Paginació

### 16.1 setFirstResult i setMaxResults
```java
int midaPagina = 10;
int numeroPagina = 2; // Segona pàgina (0-indexed: 0, 1, 2...)

String hql = "FROM Ingrediente i ORDER BY i.nombre";
Query<Ingrediente> query = session.createQuery(hql, Ingrediente.class);

// OFFSET (saltar resultats)
query.setFirstResult((numeroPagina - 1) * midaPagina);

// LIMIT (limitar resultats)
query.setMaxResults(midaPagina);

List<Ingrediente> pagina = query.getResultList();
```

### 16.2 Exemple Complet de Paginació
```java
public class PaginacioHelper {
    
    public static <T> List<T> getPagina(
        Session session, 
        String hql, 
        Class<T> classe,
        int numeroPagina, 
        int midaPagina
    ) {
        Query<T> query = session.createQuery(hql, classe);
        query.setFirstResult((numeroPagina - 1) * midaPagina);
        query.setMaxResults(midaPagina);
        return query.getResultList();
    }
    
    public static Long getTotalResultats(Session session, String hql) {
        // Convertir HQL a COUNT
        String countHql = "SELECT COUNT(*) " + hql.substring(hql.indexOf("FROM"));
        return session.createQuery(countHql, Long.class).getSingleResult();
    }
}

// Ús
session.beginTransaction();

String hql = "FROM Ingrediente i ORDER BY i.nombre";
int midaPagina = 5;
int paginaActual = 1;

List<Ingrediente> pagina = PaginacioHelper.getPagina(
    session, hql, Ingrediente.class, paginaActual, midaPagina
);

Long total = PaginacioHelper.getTotalResultats(session, hql);
int totalPagines = (int) Math.ceil(total / (double) midaPagina);

System.out.println("Pàgina " + paginaActual + " de " + totalPagines);
System.out.println("Total resultats: " + total);

session.getTransaction().commit();
```

### 16.3 Paginació amb Joins
```java
// ⚠️ PRECAUCIÓ: FETCH JOIN pot causar problemes amb paginació
// Si hi ha múltiples collections, el resultat pot no ser el esperat

// ❌ Problemàtic
String hql = "SELECT DISTINCT c FROM Cliente c LEFT JOIN FETCH c.pedidos";
query.setMaxResults(10); // Pot no funcionar correctament

// ✅ Millor opció per paginació amb joins
String hql = "FROM Cliente c";
query.setMaxResults(10);
// I després carregar pedidos si cal amb query separada
```

---

## 17. Bones Pràctiques

### 17.1 Ús de Paràmetres
```java
// ❌ MAL - SQL Injection possible
String nom = "'; DROP TABLE Ingrediente; --";
String hql = "FROM Ingrediente WHERE nombre = '" + nom + "'";

// ✅ BÉ - Sempre usar paràmetres
String hql = "FROM Ingrediente WHERE nombre = :nom";
query.setParameter("nom", nom);
```

### 17.2 Àlies Descriptius
```java
// ❌ MAL - Àlies poc clars
FROM Ingrediente i JOIN i.productos p WHERE p.precio > 5

// ✅ BÉ - Àlies descriptius
FROM Ingrediente ing 
JOIN ing.productos prod 
WHERE prod.precio > 5
```

### 17.3 FETCH JOIN per Evitar N+1
```java
// ❌ MAL - Problema N+1
String hql = "FROM Cliente c";
List<Cliente> clients = session.createQuery(hql, Cliente.class).getResultList();
for (Cliente c : clients) {
    c.getPedidos().size(); // Query extra per cada client!
}

// ✅ BÉ - FETCH JOIN
String hql = "SELECT DISTINCT c FROM Cliente c LEFT JOIN FETCH c.pedidos";
List<Cliente> clients = session.createQuery(hql, Cliente.class).getResultList();
for (Cliente c : clients) {
    c.getPedidos().size(); // Ja carregat, sense query extra
}
```

### 17.4 Projeccions per Rendiment
```java
// ❌ MAL - Carrega entitat completa quan només cal un camp
String hql = "FROM Ingrediente i";
List<Ingrediente> ingredients = session.createQuery(hql, Ingrediente.class).getResultList();
for (Ingrediente i : ingredients) {
    System.out.println(i.getNombre());
}

// ✅ BÉ - Selecciona només el camp necessari
String hql = "SELECT i.nombre FROM Ingrediente i";
List<String> noms = session.createQuery(hql, String.class).getResultList();
for (String nom : noms) {
    System.out.println(nom);
}
```

### 17.5 Transaccions
```java
// ✅ SEMPRE usar transaccions
session.beginTransaction();
try {
    // Operacions
    Query query = session.createQuery(hql);
    query.executeUpdate();
    
    session.getTransaction().commit();
} catch (Exception e) {
    session.getTransaction().rollback();
    throw e;
}
```

### 17.6 Tancar Sessions
```java
// ✅ Sempre tancar sessions
Session session = null;
try {
    session = sessionFactory.openSession();
    // Operacions
} finally {
    if (session != null) {
        session.close();
    }
}
```

### 17.7 Limitar Resultats
```java
// ✅ Sempre limitar resultats en produció
Query<Ingrediente> query = session.createQuery(hql, Ingrediente.class);
query.setMaxResults(100); // Evitar carregar milers de registres

// Per queries d'agregació, assegura't que retornen un sol valor
Long count = session.createQuery("SELECT COUNT(i) FROM Ingrediente i", Long.class)
    .getSingleResult(); // getSingleResult() per queries que retornen 1 valor
```

### 17.8 Optimització de Queries
```java
// ❌ MAL - Múltiples queries
for (int id : ids) {
    Ingrediente ing = session.get(Ingrediente.class, id);
    // Processar...
}

// ✅ BÉ - Una query amb IN
String hql = "FROM Ingrediente i WHERE i.id IN :ids";
List<Ingrediente> ingredients = session.createQuery(hql, Ingrediente.class)
    .setParameter("ids", ids)
    .getResultList();
```

---

## Taula Resum: HQL vs SQL

| Característica | HQL | SQL |
|---------------|-----|-----|
| Entitats | `FROM Ingrediente` | `SELECT * FROM ingrediente` |
| Atributs | `i.nombre` | `nombre` |
| Joins | `JOIN i.productos` | `JOIN producto ON ...` |
| Paràmetres | `:parametre` | `?` o bind variable |
| Portabilitat | ✅ Sí | ❌ Dependent de BD |
| Cache | ✅ Usa cache | ❌ Bypass cache |
| Type-safe | ✅ Sí | ❌ No |

---

## Recursos Addicionals

- [Documentació oficial Hibernate](https://hibernate.org/orm/documentation/)
- [HQL Language Reference](https://docs.jboss.org/hibernate/orm/current/userguide/html_single/Hibernate_User_Guide.html#hql)
- [Criteria API](https://docs.jboss.org/hibernate/orm/current/userguide/html_single/Hibernate_User_Guide.html#criteria) - Alternativa type-safe a HQL

---

## Exercicis Pràctics

1. **Bàsic**: Crea queries per llistar tots els ingredients de tipus VEGETABLE
2. **Intermedi**: Calcula el preu mitjà dels productes i mostra aquells més cars que la mitjana
3. **Avançat**: Troba clients que han fet més de 2 pedidos i mostra el total gastat per cada un
4. **Expert**: Implementa paginació amb cerca per nom d'ingredient i filtre per tipus
