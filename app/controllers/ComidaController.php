<?php
require_once __DIR__ . '/../models/Nutriente.php';

class ComidaController extends Controller
{
    private $comidaModel;
    private $nutrienteModel;

    public function __construct()
    {
        $this->comidaModel    = new Comida();
        $this->nutrienteModel = new Nutriente();
    }

    public function create()
    {
        $nutrientes = $this->nutrienteModel->all();
        $this->render('comidas/form', [
            'nutrientes' => $nutrientes
        ]);
    }

    public function store()
    {
        $this->comidaModel->create($_POST);
        redirect('comida', 'index');
    }

    public function edit()
    {
        $comida = $this->comidaModel->find($_GET['id']);
        $nutrientes = $this->nutrienteModel->all();

        $this->render('comidas/form', [
            'comida'     => $comida,
            'nutrientes' => $nutrientes
        ]);
    }

    public function update()
    {
        $this->comidaModel->update($_POST['id'], $_POST);
        redirect('comida', 'index');
    }
}
