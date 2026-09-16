using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using MiniEjemploAPI;

namespace MiniEjemploAPI.Controllers
{

    [Route("api/[controller]")] //api/pokemon
    [ApiController]
    public class PokemonController : ControllerBase
    {
        private readonly PokemonContext _context;

        public PokemonController(PokemonContext context)
        {
            this._context = context;
        }

        [HttpGet]
        public async Task<ActionResult> HazAlgo()
        {
            return Ok("Hola a ti también");
        }

        //api/pokemon/all
        [HttpGet("all")]
        public async Task<ActionResult<IEnumerable<Pokemon>>> GetAllPokemons()
        {
            return await _context.Pokemon.ToListAsync(); //OBVIAMENTE SI LA FUNCION ES ASYNC LOS METODOS ESTANDARES DEL CONTEXT USAREMOS SU VERSION ASYNC
        }


        //GET: api/pokemon/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Pokemon>> GetEPokemonById(int id)
        {
            var entrenador = await _context.Pokemon.FindAsync(id);
            if (entrenador == null)
            {
                return NotFound();
            }
            return Ok(entrenador);
        }

        [HttpGet("tipus/{tipus}")]
        public async Task<ActionResult<IEnumerable<Pokemon>>> Pokamios(TipusPokemon tipus)
        {
            var pokemons = await _context.Pokemon
                .Where(p => p.Tipus == tipus)
                .ToListAsync();

            return Ok(pokemons);
        }


        [HttpPost]
        public async Task<ActionResult<Pokemon>> CreatePokemon(PokemonDTO dto)
        {
            Pokemon nouEntrenador = new Pokemon
            {
                Nom = dto.Nom,
                Tipus = dto.Tipus,
                Nivell = dto.Nivell,
                PuntsVida = 100,
                PuntsVidaMaxims = 100,
                Experiencia = 0,
                EstaDebilitat = false
            };

            _context.Pokemon.Add(nouEntrenador);
            await _context.SaveChangesAsync();

            //OPCIONAL, NOMES PER SI VOLS QUE TORNI UN 201
            return CreatedAtAction(nameof(GetEPokemonById),
                new { id = nouEntrenador.PokemonID },
                nouEntrenador);

        }

        //PUT ES LA COMANDA HTTP QUE SE SUELE USAR PARA LOS UDPATES
        [HttpPut("{idPokemon}/assignar/{idEntrenador}")]
        public async Task<IActionResult> AssignarEntrenador(int idPokemon, int idEntrenador)
        {
            Pokemon pok = await _context.Pokemon.FindAsync(idPokemon);
            if (pok == null) return NotFound("Hay menos Pokemon aquí que Xavi Garcia en mi clase");
            Entrenador ent = await _context.Entrenador.FindAsync(idEntrenador);
            if (ent == null) return NotFound("Los entrenadores aqui son como el liquido en mis pulmones, inexistentes");
            pok.Entrenador = ent;
            await _context.SaveChangesAsync();

            return Ok(new
            {
                message = " EL " + pok.Nom + " HA ESTAT ASSIGNAT A " + ent.Nom + " . ",
                pokemon = pok
            });
        }


        [HttpDelete("{nom}")]
        public async Task<IActionResult> EsborrarPokemon(string nom)
        {
            List<Pokemon> poks = await _context.Pokemon.Where(p => p.Nom == nom).ToListAsync();
            foreach(Pokemon p in poks) _context.Pokemon.Remove(p);
            await _context.SaveChangesAsync();
            return Ok(new
            {
                message = "El perro de Adri Malo se ha comido a " + poks.Count + " gatos de Adri Bueno"
            });
        }






    }

    public class PokemonDTO
    {
        public string Nom { get; set; } = string.Empty;
        public TipusPokemon Tipus { get; set; } 
        public int Nivell { get; set; } = 5;
    }


}
