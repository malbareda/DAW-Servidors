using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using MiniEjemploAPI;

namespace MiniEjemploAPI.Controllers
{

    [Route("api/[controller]")] //api/Entrenador
    [ApiController]
    public class EntrenadorController : ControllerBase
    {
        private readonly PokemonContext _context;

        public EntrenadorController(PokemonContext context)
        {
            this._context = context;
        }

        [HttpGet]
        public async Task<ActionResult> HazAlgo()
        {
            return Ok("Hola");
        }

        //api/entrenador/all
        [HttpGet("all")]
        public async Task<ActionResult<IEnumerable<Entrenador>>> GetAllEntrenadors()
        {
            return await _context.Entrenador.ToListAsync(); //OBVIAMENTE SI LA FUNCION ES ASYNC LOS METODOS ESTANDARES DEL CONTEXT USAREMOS SU VERSION ASYNC
        }


        //GET: api/entrenador/5
        [HttpGet("{id}")]
        public async Task<ActionResult<Entrenador>> GetEntrenadorById(int id)
        {
            var entrenador = await _context.Entrenador.FindAsync(id);
            if (entrenador == null)
            {
                return NotFound();
            }
            return Ok(entrenador);
        }

        [HttpGet("{id}/pokemons")]
        public async Task<ActionResult<IEnumerable<Pokemon>>> Pokamios(int id)
        {
            var entrenador = await _context.Entrenador.FindAsync(id);
            if (entrenador == null) return NotFound();
            return Ok(entrenador.Pokemons.ToList());
        }

        [HttpGet("{id}/pokemons/resumen")]
        public async Task<ActionResult> Pokamios2(int id)
        {
            var entrenador = await _context.Entrenador.FindAsync(id);
            if (entrenador == null) return NotFound();
            var pokamios = entrenador.Pokemons.ToList();
            return Ok(new
            {
                entrenador = new
                {
                    entrenador.EntrenadorID,
                    entrenador.Nom,
                    entrenador.Medalles,
                    pokamios.Count
                },
                pokemons = pokamios

            }
                
                
             );
        }

        [HttpPost]
        public async Task<ActionResult<Entrenador>> CreateEntrenador(EntrenadorDTO dto)
        {
            Entrenador nouEntrenador = new Entrenador
            {
                Nom = dto.Nom,
                Cognoms = dto.Cognom,
                Medalles = 0,
                DinersPokeDollars = dto.PokeDollars
            };

            _context.Entrenador.Add(nouEntrenador);
            await _context.SaveChangesAsync();

            return Ok(nouEntrenador);
        }







    }

    public class EntrenadorDTO
    {
        public string Nom { get; set; } = string.Empty;
        public string Cognom { get; set; } = string.Empty;
        public int PokeDollars { get; set; } = 0;
    }


}
