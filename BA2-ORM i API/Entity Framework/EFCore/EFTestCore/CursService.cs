
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace EFCoreTest1
{
    public class CursService
    {
        private ProvaContext _context;

        public CursService()
        {
            _context = new ProvaContext();
        }

        public bool ToggleActiu(int idCurs)
        {
            Curs curs = _context.Curs.Find(idCurs);
            curs.Actiu = !curs.Actiu;
            _context.SaveChanges();
            return curs.Actiu;
        }

        public bool AssignarProfessor(int idCurs, int idProfessor)
        {
            Curs curs = _context.Curs.Find(idCurs);
            Professor prof = _context.Professor.Find(idProfessor);
            if (prof == null || curs == null) return false;
            curs.Professor = prof;
            prof.Curs = curs;
            _context.SaveChanges();
            return true;
        }

        public int quantsModuls(int idCurs)
        {
            Curs curs = _context.Curs.Find(idCurs);
            // AMB LAZY LOADING, Moduls es carrega automàticament
            return curs.Moduls.Count();
        }

        public int quantsCursos()
        {
            return _context.Curs.Count();
        }
    }

}
