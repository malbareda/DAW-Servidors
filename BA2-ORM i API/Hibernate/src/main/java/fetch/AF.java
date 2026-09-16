package fetch;

import java.util.HashSet;
import java.util.Set;
import jakarta.persistence.*;

@Entity  // Marca aquesta classe com una entitat JPA
public class AF {
    @Id  // Marca aquest camp com la clau primària
    @GeneratedValue(strategy = GenerationType.IDENTITY)  // Generació automàtica de l'ID
    int id;
    
    @Column  // Marca aquest camp com una columna de la taula
    String nomA;
    
    //FETCH LAZY
    //NOMES CARREGARA ELS OBJECTES NECESSARIS EN EL MOMENT DE FER LA TRANSACCION
    //SI DINTRE DE LA MATEIXA SESSION NECESSITA MES, HO CARREGARA
    //PERO SI ACABES LA SESSION ES QUEDARÀ AIXI, SI NECESSITES MÉS DONARÀ ERROR A L'ACCEDIR!
    @OneToMany(mappedBy = "a", fetch = FetchType.EAGER)
    private Set<BF> b = new HashSet<BF>();

    // Getters i setters
    public String getNomA() { return nomA; }
    public void setNomA(String nomA) { this.nomA = nomA; }
    public Set<BF> getB() { return b; }
    public void setB(Set<BF> b) { this.b = b; }
}
