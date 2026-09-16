using System.Collections.Generic;
using System.Linq;

namespace EFCoreTest1
{
    // ProfessorService - EXACTAMENT IGUAL que EF6
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
