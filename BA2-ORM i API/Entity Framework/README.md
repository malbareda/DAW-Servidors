# ⚠️ IMPORTANT: Error "No es pot obrir la base de dades de SQL Server"

Si et surt un error similar a "Cannot open database" o "Unable to open the physical file" quan intentes executar el teu projecte Entity Framework a casa, segueix aquests passos:

---

## 🔧 Pas 1: Modificar el Registre de Windows (regedit)

### 1.1. Obrir l'Editor del Registre

- Prem **Win + R**
- Escriu `regedit` i prem Enter
- Accepta l'UAC (Control de Comptes d'Usuari)


### 1.2. Navegar a la clau correcta

Ves a la següent ubicació:

```
Computer\HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Services\stornvme\Parameters\Device
```


### 1.3. Crear la nova entrada

1. Fes clic dret a l'àrea buida del panell dret
2. Selecciona **Nou → Valor de cadenes múltiples** (New → Multi-String Value)
3. Anomena'l exactament: `ForcedPhysicalSectorSizeInBytes`


### 1.4. Establir el valor

1. Fes doble clic a la nova entrada `ForcedPhysicalSectorSizeInBytes`
2. Al camp "Dades del valor", escriu: `* 4095` (amb l'asterisc inclòs!)
3. Fes clic a **Acceptar**

![Establir valor](https://i.postimg.cc/ZnPbcQYL/image.png)

⚠️ **MOLT IMPORTANT:** El valor ha de ser `* 4095` (asterisc + espai + 4095)

### 1.5. Reiniciar l'ordinador

**Reinicia l'ordinador** perquè els canvis tinguin efecte.

---

## 💻 Pas 2: Comprovar i recrear SQL Server LocalDB

Després de reiniciar, obre **PowerShell com a administrador**:


### 2.1. Intentar iniciar LocalDB

Executa la següent comanda:

```powershell
sqllocaldb start mssqllocaldb
```


### 2.2. Si dona error, eliminar i recrear

Si la comanda anterior falla, executa aquestes dues comandes en ordre:

```powershell
# Eliminar la instància corrupta
sqllocaldb delete mssqllocaldb
```


```powershell
# Crear una nova instància
sqllocaldb create mssqllocaldb
```

### 2.3. Iniciar la nova instància

Torna a intentar iniciar-la:

```powershell
sqllocaldb start mssqllocaldb
```

Hauries de veure un missatge de confirmació que s'ha iniciat correctament.

![LocalDB iniciat correctament](https://i.postimg.cc/D0chsb5v/image.png)

---


## 🤔 Per què passa això?

Aquest problema sol passar en ordinadors amb discos SSD moderns (especialment NVMe) que tenen sectors físics de 4096 bytes. SQL Server LocalDB espera sectors de 512 bytes per defecte, i el canvi al registre força la compatibilitat.
