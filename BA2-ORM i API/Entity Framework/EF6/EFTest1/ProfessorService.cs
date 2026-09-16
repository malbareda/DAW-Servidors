using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace EFTest1
{
    public class ProfessorService
    {
        private ProvaContext _context;
        public ProfessorService()
        {
            _context = new ProvaContext();
        }

        public Professor cercaPerCognom(string professorName)
        {

            Professor prof = _context.Professor
            .Where(p => p.cognom == professorName)
            .First();

            return prof;
        }

        public List<Professor> cercaPerPrimeraLletraNom(string lletra)
        {
            List<Professor> a = _context.Professor
                .Where(p => p.nom.StartsWith(lletra))
                .ToList();
            return a;
        }


    }
}
