using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace EFTest1
{
    class Program2
    {
        static void Main(string[] args)
        {
            using (var ctx = new TorneigConext())
            {


                Console.WriteLine("\nPrem ENTER per sortir...");
                Console.ReadLine();
            }
        }
    }
}