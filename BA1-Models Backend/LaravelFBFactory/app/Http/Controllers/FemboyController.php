<?php

namespace App\Http\Controllers;

use App\Enums\EstadoFemboy;
use App\Models\Factory;
use App\Models\Femboy;
use Illuminate\Http\Request;

class FemboyController extends Controller
{
    //

    //EL GET QUE MUESTRA EL FORMUALRIO
    public function create()
    {
        $this->authorize('create', Femboy::class);
        // Factory::all() és l'equivalent a Factory.objects.all() de Django
        $factories = Factory::all();
        //torna tots els estats possibles de l'enum
        $estados = EstadoFemboy::cases();
        // view() és com render() de Django
        // compact() crea un array associatiu amb les variables
        // Equivalent a: {'factories': factories, 'estados': estados} en el context de Django
        return view('femboys.create', compact('factories','estados'));
    }

    //EL POST DEL FORMULARIO COMPLETA
    public function store(Request $request){
        $femboy = new Femboy();
        // $request->input('nom_camp') és com request.POST['nom_camp'] en Django
        $femboy->nombre = $request->input('nombre');
        $femboy->edad = $request->input('edad');
        $femboy->procedencia = $request->input('procedencia');
        $femboy->descripcion = $request->input('descripcion');
        $femboy->fecha_cumpleanos = $request->input('fecha_cumpleanos');
        $femboy->estado = $request->input('estado');
        $femboy->factory_id = $request->input('factory_id');
        $femboy->save();

        // NOTA: Aquest mètode no retorna res, pero lo normal seria fer un redirect

    }


    public function edit(Femboy $femboy)
    {
        // Laravel ja ha carregat l'objecte Femboy automàticament!
        // Si no existeix, retorna 404 automàticament

        $factories = Factory::all();
        $estados = EstadoFemboy::cases();
        return view('femboys.edit', compact('femboy', 'factories','estados'));
    }

    /**
     * Actualitza un producte a la BD
     */
    public function update(Request $request, Femboy $femboy)
    {
        //Pots fer una validacio automatica desde el backend amb el validate
        $validated = $request->validate([
            //els | es per dir diferents requisits.
            //aqui estem dient uqe nom es required (obligatori, ha de ser una string,i max 255 caracters)
            'nombre' => 'required|string|max:255',
            //aqui al ser un integer min i max es el valor numeric
            'edad' => 'required|integer|min:18|max:100',
            'procedencia' => 'required|string|max:255',
            //aqui demanem que sigui una data, i abans d'avui
            'fecha_cumpleanos' => 'required|date|before:today',
            'descripcion' => 'required|string|max:500',
            //aqui demanem que sigui un dels valors ["Disponible","Reservado","No Disponible"]
            'estado' => 'required|in:Disponible,Reservado,No Disponible',
            //Aqui demanem que existeixi una factory amb aquest id. Aquest no hauria de ser possible eprquè vé d'un dropdown, pero ja que esticus ho poso
            'factory_id' => 'required|exists:factories,id'

        ]);

        $femboy->nombre = $request->nombre;
        $femboy->edad = $request->edad;
        $femboy->procedencia = $request->procedencia;
        $femboy->fecha_cumpleanos = $request->fecha_cumpleanos;
        $femboy->descripcion = $request->descripcion;
        $femboy->estado = $request->estado;
        $femboy->factory_id = $request->factory_id;
        $femboy->save();

        //redirect amb missatge.
        return redirect()->route('femboys.all')->with('success', 'Femboy actualizado correctamente');
    }



    public function all()
    {
        $this->authorize('viewAny', Femboy::class);

        $femboys = Femboy::all();
        return view('femboys.all', compact('femboys'));
    }

    public function porEstado(EstadoFemboy $estado)
    {
        /**
         * ELOQUENT QUERY - where() funciona com filter() de Django
         *
         * Django: Femboy.objects.filter(estado=estado)
         * Laravel: Femboy::where('estado', $estado)->get()
         *
         * Diferència important:
         * - En Django: .filter() ja retorna els resultats
         * - En Laravel: .where() crea la query, .get() l'executa
         */
        // Pots passar l'enum directament!
        $femboys = Femboy::where('estado', $estado)->get();
        return view('femboys.all', compact('femboys'));
    }

    /**
     * Retorna tots els femboys menors de 25 anys
     * Leonardo DiCaprio approves this message 🎬
     */
    public function DiCaprioApproves()
    {
        // Opció 1: Amb where
        $femboys = Femboy::where('edad', '<', 25)->get();

        // Opció 2: Amb where i orderBy per edat
        // $femboys = Femboy::where('edad', '<', 25)->orderBy('edad', 'asc')->get();

        // Opció 3: Amb whereBetween per ser més específic
        // $femboys = Femboy::whereBetween('edad', [18, 24])->get();

        return view('femboys.dicaprio', compact('femboys'));
    }

    //si tu pones que recibe un objeto, cuando por url reciba la id se convertira automaticamente en el objeto
    public function show(Femboy $femboy)
    {
        $this->authorize('view', $femboy);
        return view('femboys.show', compact('femboy'));
    }

    public function destroy(Femboy $femboy){
        $femboy->delete();
        //en un redirect indicas donde lelva y el with seria un message
        return redirect()->route('femboys.all')->with('success','Femboy deleted successfully');
    }

}
