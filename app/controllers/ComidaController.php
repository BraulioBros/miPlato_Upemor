<?php
/**
 * CONTROLADOR DE COMIDAS
 * 
 * Maneja las operaciones CRUD de comidas:
 * - Crear nuevas comidas
 * - Editar comidas existentes
 * - Listar todas las comidas (heredado de methods en vistas)
 * - Actualizar comidas
 */

require_once __DIR__ . '/../models/Nutriente.php';
require_once __DIR__ . '/../models/Comida.php';

class ComidaController extends Controller
{
    private $comidaModel;
    private $nutrienteModel;

    public function __construct()
    {
        $this->comidaModel    = new Comida();
        $this->nutrienteModel = new Nutriente();
    }

    /**
     * Muestra el formulario para crear una nueva comida
     * 
     * Obtiene la lista de nutrientes disponibles para asociar a la comida
     */
    public function create()
    {
        $nutrientes = $this->nutrienteModel->all();
        $this->render('comidas/form', [
            'nutrientes' => $nutrientes
        ]);
    }

    /**
     * Procesa la creación de una nueva comida
     * 
     * Guarda los datos POST en la base de datos y redirige al listado
     */
    public function store()
    {
        $this->comidaModel->create($_POST);
        redirect('comida', 'index');
    }

    /**
     * Muestra el formulario para editar una comida existente
     * 
     * Obtiene:
     * - Datos de la comida a editar
     * - Lista de nutrientes disponibles
     */
    public function edit()
    {
        $comida = $this->comidaModel->find($_GET['id']);
        $nutrientes = $this->nutrienteModel->all();

        $this->render('comidas/form', [
            'comida'     => $comida,
            'nutrientes' => $nutrientes
        ]);
    }

    /**
     * Procesa la actualización de una comida existente
     * 
     * Actualiza los datos en la base de datos y redirige al listado
     */
    public function update()
    {
        $this->comidaModel->update($_POST['id'], $_POST);
        redirect('comida', 'index');
    }
}
