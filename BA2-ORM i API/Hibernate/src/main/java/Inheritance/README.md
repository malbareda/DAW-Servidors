# Herència a Hibernate

## Concepte Bàsic
L'herència a Hibernate permet mapejar jerarquies de classes Java a taules de base de dades. En els exemples, tenim dues jerarquies amb diferents estratègies:

**Jerarquia 1 - Alumnes (SINGLE_TABLE):**
- `Alumno` (classe base)
  - `AlumnoBueno` (classe filla)
  - `AlumnoMalo` (classe filla)

**Jerarquia 2 - Peluqueries (TABLE_PER_CLASS):**
- `Peluqueria` (classe base)
  - `PeluqueriaHipster` (classe filla)
  - `PeluqueriaPerroflauta` (classe filla)

---

## Estratègia SINGLE_TABLE

```java
@Entity
@Inheritance(strategy = InheritanceType.SINGLE_TABLE)
@DiscriminatorColumn(name="tipo_de_alumno", discriminatorType = DiscriminatorType.STRING)
public class Alumno {
    // ...
}
```

Aquesta estratègia:
- Crea una única taula per tota la jerarquia de classes
- Tots els atributs de les classes filles es guarden a la mateixa taula
- S'utilitza una columna discriminadora per diferenciar entre tipus d'alumnes
- És l'estratègia per defecte a Hibernate

### Columna Discriminadora
- Es defineix amb `@DiscriminatorColumn`
- En aquest cas s'anomena "tipo_de_alumno"
- Pot ser de tipus STRING o INTEGER (es recomana STRING per claredat)
- **És obligatòria** en SINGLE_TABLE

### Classes Filles

Les classes filles s'identifiquen amb `@DiscriminatorValue`:

```java
@Entity
@DiscriminatorValue("Buenos")
public class AlumnoBueno extends Alumno {
    private int cosaBuena;
}

@Entity
@DiscriminatorValue("Malos")
public class AlumnoMalo extends Alumno {
    private boolean cosaMala;
}
```

- Cada classe filla especifica el seu valor discriminador
- Els atributs específics (`cosaBuena`, `cosaMala`) es guarden a la mateixa taula que la classe pare

### Avantatges de SINGLE_TABLE
1. Rendiment òptim en consultes
2. No necessita JOINS per recuperar dades
3. Més simple d'implementar

### Desavantatges de SINGLE_TABLE
1. Els atributs específics de les classes filles han de ser **nullables**
2. Pot tenir moltes columnes NULL si hi ha moltes classes filles amb atributs diferents
3. Possible desperdici d'espai a la base de dades

---

## Estratègia TABLE_PER_CLASS

```java
@Entity
@Inheritance(strategy = InheritanceType.TABLE_PER_CLASS)
@DiscriminatorColumn(name="tipo", discriminatorType = DiscriminatorType.STRING)
public class Peluqueria {
    @Id 
    @GeneratedValue(strategy = GenerationType.TABLE)
    private int idPelu;
    
    private String nom;
}
```

Aquesta estratègia:
- Crea una taula separada per **cada classe concreta** (incloent la classe base si és concreta)
- Cada taula conté tots els camps: els propis i els heretats del pare
- Cada taula és completament independent

### Generació d'IDs
Important: amb TABLE_PER_CLASS, **no es pot utilitzar IDENTITY** per generar IDs. Cal utilitzar:
```java
@GeneratedValue(strategy = GenerationType.TABLE)
// o
@GeneratedValue(strategy = GenerationType.SEQUENCE)
```

Això és perquè les múltiples taules necessiten coordinar els IDs per evitar duplicats.

### Classes Filles

```java
@Entity
@Table
public class PeluqueriaHipster extends Peluqueria {
    private boolean olorPeculiar;
}

@Entity
@Table
public class PeluqueriaPerroflauta extends Peluqueria {
    private int chinches;
}
```

- Cada classe filla té la seva pròpia taula
- No es necessita `@DiscriminatorValue` (tot i que està definit a la classe base, no s'utilitza)
- Cada taula conté: `idPelu`, `nom` (del pare) + atributs propis

### Avantatges de TABLE_PER_CLASS
1. No hi ha columnes NULL
2. Cada taula és independent i normalitzada
3. Els atributs específics **no** han de ser nullables

### Desavantatges de TABLE_PER_CLASS
1. Consultes menys eficients (poden requerir UNION de múltiples taules)
2. Més complex de mantenir
3. Més espai utilitzat (duplicació de columnes heretades)

---

## Relacions amb Herència

### Relacions amb SINGLE_TABLE

#### ManyToOne des de la classe base
```java
public class Alumno {
    @ManyToOne 
    private Profe profe;
}
```

#### OneToMany amb la classe base
```java
public class Profe {
    @OneToMany(mappedBy = "profe") 
    private Set<Alumno> alumnos = new HashSet<>();
}
```

**Important:** El Set es defineix amb la classe base (`Alumno`), no amb les classes filles. Això permet que el conjunt contingui tant `AlumnoBueno` com `AlumnoMalo`.

#### ManyToOne des d'una classe filla específica
```java
public class AlumnoMalo extends Alumno {
    @ManyToOne 
    private Carcel carcel;
}
```

```java
public class Carcel {
    @OneToMany(mappedBy = "carcel") 
    private Set<AlumnoMalo> alumnos = new HashSet<>();
}
```

En aquest cas, el Set és específic de `AlumnoMalo` perquè només els alumnes dolents van a la presó.

### Relacions amb TABLE_PER_CLASS

```java
public class PeluqueriaPerroflauta extends Peluqueria {
    @ManyToOne 
    private Barcelona carcel;
}
```

```java
public class Barcelona {
    @OneToMany(mappedBy = "carcel") 
    private Set<PeluqueriaPerroflauta> pelus = new HashSet<>();
}
```

Les relacions funcionen igual, però cada taula emmagatzema les seves pròpies claus forànies.

---

## Exemples Pràctics

### Exemple 1: SINGLE_TABLE (Alumnes i Profe)

```java
Profe p = new Profe();
p.setNomProfe("Marc Llaó");

// Crear alumne bo
AlumnoBueno xaviC = new AlumnoBueno();
xaviC.setNombreAlumno("Xavi Conesa");
xaviC.setCosaBuena(2);

// Crear alumne dolent
AlumnoMalo ericH = new AlumnoMalo();
ericH.setNombreAlumno("Eric Homofobo");
ericH.setCosaMala(true);

// Crear presó
Carcel c = new Carcel();
c.setNombreCarcel("Frontend, feat. Eloi");

// Establir relacions bidireccionals
c.getAlumnos().add(ericH);
ericH.setCarcel(c);

p.getAlumnos().add(ericH);
p.getAlumnos().add(xaviC);

xaviC.setProfe(p);
ericH.setProfe(p);

// Persistir els objectes
session.persist(p);
session.persist(c);
session.persist(xaviC);
session.persist(ericH);
session.getTransaction().commit();
```

### Exemple 2: TABLE_PER_CLASS (Peluqueries i Barcelona)

```java
// Crear peluqueria hipster
PeluqueriaHipster p = new PeluqueriaHipster();
p.setNom("Anabel");
p.setOlorPeculiar(true);

// Crear peluqueria perroflauta
PeluqueriaPerroflauta f = new PeluqueriaPerroflauta();
f.setNom("Rastatistan");
f.setChinches(Integer.MAX_VALUE);

// Crear Barcelona
Barcelona b = new Barcelona();
b.setAlcaldesa("Colau");

// Establir relacions bidireccionals
b.getPelus().add(f);
f.setCarcel(b);

// Persistir els objectes
session.persist(p);
session.persist(f);
session.persist(b);
session.getTransaction().commit();
```

---

## Comparativa d'Estratègies

| Característica | SINGLE_TABLE | TABLE_PER_CLASS |
|----------------|--------------|-----------------|
| Nombre de taules | 1 per jerarquia | 1 per classe concreta |
| Columnes NULL | Sí (moltes) | No |
| Rendiment SELECT | Excel·lent | Regular (UNION) |
| Normalització | Baixa | Alta |
| ID Generation | IDENTITY (recomanat) | TABLE o SEQUENCE |
| Discriminator Column | Obligatòria | No s'utilitza |
| Complexitat | Baixa | Mitjana |

---

## Consideracions Generals

1. **Bidireccionalitat:** Cal mantenir sempre la bidireccionalitat en les relacions (establir ambdós costats)
2. **Inicialització de col·leccions:** És recomanable inicialitzar els Sets directament a la declaració: `new HashSet<>()`
3. **Elecció d'estratègia:**
   - Utilitza SINGLE_TABLE quan prioritzis el rendiment i tinguis poques columnes específiques
   - Utilitza TABLE_PER_CLASS quan necessitis normalització i tinguis moltes columnes específiques diferents
4. **Persistència:** Recorda fer `persist()` de tots els objectes nous en l'ordre adequat segons les dependències

Aquests exemples mostren com Hibernate gestiona diferents estratègies d'herència, cadascuna amb els seus avantatges i casos d'ús específics.
