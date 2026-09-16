# Fetch Type a Hibernate: LAZY vs EAGER

## Introducció
El FetchType és una estratègia que defineix quan Hibernate ha de carregar les dades relacionades amb una entitat. És especialment important en relacions entre entitats (com @OneToMany, @ManyToOne, etc.).

## Els dos tipus de Fetch

### LAZY (Càrrega Tardana)
- Les dades relacionades NO es carreguen fins que s'accedeix específicament a elles
- Més eficient en memòria i rendiment inicial
- És l'opció per defecte en relacions @OneToMany i @OneToOne
- Requereix una sessió de Hibernate activa per accedir a les dades

### EAGER (Càrrega Immediata)
- Les dades relacionades es carreguen immediatament amb l'entitat principal
- Pot ser menys eficient si no necessitem sempre les dades relacionades
- És l'opció per defecte en relacions @ManyToOne
- No requereix sessió activa per accedir a les dades ja carregades

## Exemple Pràctic
En el codi d'exemple tenim dues entitats relacionades:

### Classe AF
```java
@Entity
public class AF {
    @OneToMany(mappedBy = "a", fetch = FetchType.LAZY)
    private Set<BF> b = new HashSet<BF>();
    // ...
}
```

### Classe BF
```java
@Entity
public class BF {
    @ManyToOne(fetch = FetchType.LAZY)
    @JoinColumn(name="a_id", nullable=false)
    private AF a;
    // ...
}
```

## Anàlisi del comportament LAZY

### Escenari que genera LazyInitializationException

```java
session = getSessionFactory().openSession();
session.beginTransaction();

AF a = session.find(AF.class, 1);
session.getTransaction().commit();
session.close();

// Aquest codi generarà LazyInitializationException
System.out.println(a.getNomA()); // Això funciona
System.out.println(a.getB());    // Això llença l'excepció
```

### Per què passa?
1. Quan fem `session.find(AF.class, 1)`, només es carrega l'entitat AF amb les seves propietats directes (id, nomA)
2. La col·lecció 'b' es marca com a "no inicialitzada" degut al FetchType.LAZY
3. Quan tanquem la sessió amb `session.close()`, perdem la connexió amb la base de dades
4. En intentar accedir a `a.getB()`, Hibernate necessita carregar les dades, però no pot perquè:
   - La sessió està tancada
   - No pot fer la consulta SQL necessària
   - Resultat: LazyInitializationException

### Solucions possibles

1. **Mantenir la sessió oberta**
```java
session.beginTransaction();
AF a = session.find(AF.class, 1);
System.out.println(a.getB()); // Funciona perquè la sessió està oberta
session.getTransaction().commit();
session.close();
```

2. **Inicialitzar les dades abans de tancar la sessió**
```java
session.beginTransaction();
AF a = session.find(AF.class, 1);
Hibernate.initialize(a.getB()); // Força la càrrega
session.getTransaction().commit();
session.close();
System.out.println(a.getB()); // Ara funcionarà
```

3. **Canviar a FetchType.EAGER**
```java
@OneToMany(mappedBy = "a", fetch = FetchType.EAGER)
private Set<BF> b = new HashSet<BF>();
```

## Recomanacions d'ús

### Quan usar LAZY
- Quan no sempre necessitem les dades relacionades
- En relacions amb moltes dades (per exemple, llistes llargues)
- Quan volem optimitzar el rendiment inicial

### Quan usar EAGER
- Quan sempre necessitem les dades relacionades
- En relacions amb poques dades
- Quan necessitem accedir a les dades fora de la sessió

## Bones pràctiques
1. Utilitzar LAZY com a estratègia per defecte
2. Planificar bé el cicle de vida de la sessió
3. Considerar l'ús de DTOs per transferir dades fora de la sessió
4. Utilitzar eines com el "join fetch" en les consultes JPQL quan necessitem dades relacionades específiques

## Conclusió
El FetchType és una eina poderosa per optimitzar el rendiment de les aplicacions amb Hibernate. La clau és entendre el cicle de vida de la sessió i escollir l'estratègia adequada segons les necessitats específiques de cada cas d'ús.