¡using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using WebApplication1;

namespace EFTest1.API.Controllers
{
    //LA RUTA GENERAL SERA "api/curs"
    [Route("api/[controller]")]
    [ApiController]
    public class CursController : ControllerBase
    {
        private readonly ProvaContext _context;

        // Injecció de dependències del DbContext (molt millor que EF6!)
        public CursController(ProvaContext context)
        {
            _context = context;
        }

        // GET: api/curs
        [HttpGet]
        //Per que Task<ActionResult<IEnumerable<Curs>>> en comptes de IEnumerable<Curs>?
        //1. Pots tornar el IEnumerable<Curs> però també pots tornar coses com Ok(), NotFound(), BadRequest()
        //Et dona moltes mes possibilitats per una cosa que acabes automatitzant
        public async Task<ActionResult<IEnumerable<Curs>>> GetAllCursos()
        {
            //Per què async/await?
            //Crea un thread asincron que pot anar fent i desfent al seu gust sense haver d'esperar
            /*
            - Tens 100 usuaris connectats simultàniament
            - Cada un fa una petició que triga 2 segons a llegir de la BD

            **Versió SÍNCRONA:**
            ```
            Thread 1: [====BD====] (bloquejat esperant)
            Thread 2: [====BD====] (bloquejat esperant)
            Thread 3: [====BD====] (bloquejat esperant)
            ...necessites 100 threads!
            ```

            **Versió ASÍNCRONA:**
            ```
            Thread 1: [BD] → alliberat → [BD] → alliberat
            Thread 2: [BD] → alliberat → [BD] → alliberat
            ...només necessites 5-10 threads!*/
            return await _context.Curs.ToListAsync();
        }

        // GET: api/curs/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Curs>> GetCursById(int id)
        {
            //si vas amb Async has de fer servir mètodes com FindAsync, ToListAsync, etc.
            var curs = await _context.Curs.FindAsync(id);

            if (curs == null)
            {
                //Aixo retorna automàticament un 404 Not Found sense que l'hagis de construir
                //Això és gracies al Task<ActionResult>
                return NotFound();
            }
            //ho retorna com a JSON
            return curs;
        }

        // GET: api/curs/actius
        [HttpGet("actius")]
        public async Task<ActionResult<IEnumerable<Curs>>> GetCursosActius()
        {
            //En Await el LINQ funciona normal
            return await _context.Curs
                .Where(c => c.Actiu)
                .ToListAsync();
        }

        // GET: api/curs/cicle/DAM
        [HttpGet("cicle/{tipusCicle}")]
        public async Task<ActionResult<IEnumerable<Curs>>> GetCursosByCicle(string tipusCicle)
        {
            if (tipusCicle != "ASIX" && tipusCicle != "DAM" && tipusCicle != "DAMVI")
            {
                return BadRequest($"Cicle formatiu '{tipusCicle}' no vàlid. Valors: DAM, ASIX, DAMVI");
            }
            CicleFormatiu cicle = (CicleFormatiu)Enum.Parse(typeof(CicleFormatiu), tipusCicle); //El parse de string a Enum en .NET es lletgissim
            var cursos = await _context.Curs
                .Where(c => c.Cicle == cicle)
                .ToListAsync();

            return Ok(cursos);
        }

        // POST: api/curs
        [HttpPost]
        public async Task<ActionResult<Curs>> CreateCurs(CursDTO cursDto)
        {
            if (cursDto.Any < 2020 || cursDto.Any > 2030)
            {
                return BadRequest("L'any ha d'estar entre 2020 i 2030");
            }
            //AQUI ESTEM REBENT UN DTO. UN DTO ES UNA CLASSE QUE REPRESENTA DADES SIMPLES, FACILMENT CONVERTIBLES A JSON
            //PER TANT, AQUI ENS ARRIBA UN JSON QUE CONVERTIM A DTO. A APRTIR DEL DTO PODEM CREAR FACILMENT UN CURS.
            //LA DEFINICIO DE DTO ESTA A BAIX DE TOT


            //CREEM EL NOU CURS
            var nouCurs = new Curs
            {
                Actiu = cursDto.Actiu,
                Any = cursDto.Any,
                Cicle = cursDto.Cicle
            };

            // Assignar professor si existeix
            if (cursDto.ProfessorID.HasValue)
            {
                var professor = await _context.Professor.FindAsync(cursDto.ProfessorID.Value);
                if (professor == null)
                {
                    return BadRequest($"Professor amb ID {cursDto.ProfessorID} no existeix");
                }
                nouCurs.Professor = professor;
            }

            _context.Curs.Add(nouCurs);
            //el SaveChangesAsync permet tornar el frontend a l'usuari mentre es guarden els canvis.
            await _context.SaveChangesAsync();

            //Torna un 201 (Created), amb el nom
            return CreatedAtAction( //TOTS AQUESTS CAMPS SON INNECESSARIS, PODRIES TORNAR "OK"
                nameof(GetCursById), //aixo creara la URL del get, perque sigui mes professional
                new { id = nouCurs.CursID }, //passes la ID A LA URL
                nouCurs //Al body retornaras l'objecte creat
                );
        }

        // PUT: api/curs/5
        [HttpPut("{id}")]
        public async Task<IActionResult> UpdateCurs(int id, CursDTO cursDto)
        {
            var curs = await _context.Curs.FindAsync(id);

            if (curs == null)
            {
                return NotFound();
            }

            curs.Actiu = cursDto.Actiu;
            curs.Any = cursDto.Any;
            curs.Cicle = cursDto.Cicle;

            if (cursDto.ProfessorID.HasValue)
            {
                var professor = await _context.Professor.FindAsync(cursDto.ProfessorID.Value);
                if (professor == null)
                {
                    return BadRequest($"Professor amb ID {cursDto.ProfessorID} no existeix");
                }
                curs.Professor = professor;
            }

            try
            {
                await _context.SaveChangesAsync();
            }
            catch (DbUpdateConcurrencyException)
            {
                if (!CursExists(id))
                {
                    return NotFound();
                }
                throw;
            }

            return NoContent();
        }

        // PATCH: api/curs/5/toggle-actiu
        [HttpPatch("{id}/toggle-actiu")]
        public async Task<ActionResult> ToggleActiu(int id)
        {
            var curs = await _context.Curs.FindAsync(id);

            if (curs == null)
            {
                return NotFound();
            }

            curs.Actiu = !curs.Actiu;
            await _context.SaveChangesAsync();

            return Ok(new
            {
                cursId = curs.CursID,
                actiu = curs.Actiu,
                missatge = $"Curs {(curs.Actiu ? "activat" : "desactivat")} correctament"
            });
        }

        // DELETE: api/curs/5
        [HttpDelete("{id}")]
        public async Task<IActionResult> DeleteCurs(int id)
        {
            var curs = await _context.Curs.FindAsync(id);

            if (curs == null)
            {
                return NotFound();
            }

            // Comprovar si té mòduls associats
            if (curs.Moduls.Any())
            {
                return BadRequest("No es pot eliminar un curs amb mòduls associats");
            }

            _context.Curs.Remove(curs);
            await _context.SaveChangesAsync();

            return Ok(new
            {
                missatge = "Curs eliminat correctament",
                cursId = id
            });
        }

        // GET: api/curs/5/moduls
        [HttpGet("{id}/moduls")]
        public async Task<ActionResult> GetModulsCurs(int id)
        {
            var curs = await _context.Curs.FindAsync(id);

            if (curs == null)
            {
                return NotFound();
            }

            //aqui no es ToListAsync perque curs ja es async haha
            var moduls = curs.Moduls.ToList();

            //Retornes un 200 OK amb un body personalitzat. Podries haver tornat "curs" al body i ja
            return Ok(new
            {
                cursId = curs.CursID,
                cicle = curs.Cicle.ToString(),
                any = curs.Any,
                quantitatModuls = moduls.Count,
                moduls = moduls
            });
        }

        // ============================================================
        // EXEMPLES AMB QUERY PARAMS
        // ============================================================

        // GET: api/curs/search?any=2024&actiu=true&cicle=DAM
        // Query params opcionals amb filtres combinats
        [HttpGet("search")]
        public async Task<ActionResult<IEnumerable<Curs>>> SearchCursos(
        [FromQuery] int? any,           // Opcional: ?any=2024
        [FromQuery] bool? actiu,        // Opcional: ?actiu=true
        [FromQuery] string? cicle)      // Opcional: ?cicle=DAM
        {
            var query = _context.Curs.AsQueryable();

            // Aplicar filtres només si s'han proporcionat
            if (any.HasValue)
            {
                query = query.Where(c => c.Any == any.Value);
            }

            if (actiu.HasValue)
            {
                query = query.Where(c => c.Actiu == actiu.Value);
            }

            if (!string.IsNullOrEmpty(cicle))
            {
                if (Enum.TryParse<CicleFormatiu>(cicle, true, out var cicleEnum))
                {
                    query = query.Where(c => c.Cicle == cicleEnum);
                }
                else
                {
                    return BadRequest($"Cicle '{cicle}' no vàlid. Valors: DAM, ASIX, DAMVI");
                }
            }

            var cursos = await query.ToListAsync();

            return Ok(new
            {
                total = cursos.Count,
                filtres = new
                {
                    any = any,
                    actiu = actiu,
                    cicle = cicle
                },
                resultats = cursos
            });
        }

        // GET: api/curs/paginated?page=1&pageSize=10
        // Paginació amb query params
        [HttpGet("paginated")]
        public async Task<ActionResult> GetCursosPaginated(
        [FromQuery] int page = 1,           // Valor per defecte: 1
        [FromQuery] int pageSize = 10)      // Valor per defecte: 10
        {
            if (page < 1)
            {
                return BadRequest("El número de pàgina ha de ser >= 1");
            }

            if (pageSize < 1 || pageSize > 100)
            {
                return BadRequest("La mida de pàgina ha d'estar entre 1 i 100");
            }

            var totalItems = await _context.Curs.CountAsync();
            var totalPages = (int)Math.Ceiling(totalItems / (double)pageSize);

            var cursos = await _context.Curs
                .OrderBy(c => c.CursID)
                .Skip((page - 1) * pageSize)
                .Take(pageSize)
                .ToListAsync();

            return Ok(new
            {
                page = page,
                pageSize = pageSize,
                totalItems = totalItems,
                totalPages = totalPages,
                items = cursos
            });
        }

        // GET: api/curs/filter?anyMin=2023&anyMax=2025&ordenar=any
        // Filtres amb rangs i ordenació
        [HttpGet("filter")]
        public async Task<ActionResult<IEnumerable<Curs>>> FilterCursos(
        [FromQuery] int? anyMin,
        [FromQuery] int? anyMax,
        [FromQuery] string? ordenar = "id")  // Valor per defecte
        {
            var query = _context.Curs.AsQueryable();

            if (anyMin.HasValue)
            {
                query = query.Where(c => c.Any >= anyMin.Value);
            }

            if (anyMax.HasValue)
            {
                query = query.Where(c => c.Any <= anyMax.Value);
            }

            // Ordenació dinàmica
            query = ordenar?.ToLower() switch
            {
                "any" => query.OrderBy(c => c.Any),
                "cicle" => query.OrderBy(c => c.Cicle),
                "any_desc" => query.OrderByDescending(c => c.Any),
                _ => query.OrderBy(c => c.CursID)
            };

            var cursos = await query.ToListAsync();

            return Ok(cursos);
        }


        private bool CursExists(int id)
        {
            return _context.Curs.Any(e => e.CursID == id);
        }
    }

    // DTO per rebre dades del client
    public class CursDTO
    {
        public bool Actiu { get; set; }
        public int Any { get; set; }
        public CicleFormatiu Cicle { get; set; }
        public int? ProfessorID { get; set; }
    }
}