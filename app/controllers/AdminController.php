<?php
require_once __DIR__.'/../models/User.php';require_once __DIR__.'/../models/Nutriente.php';require_once __DIR__.'/../models/Comida.php';require_once __DIR__.'/../models/EstudianteDetalle.php';
class AdminController extends Controller{
public function dashboard(){ 
    $this->requireRole('admin');
    $uM=new User();
    $nM=new Nutriente();
    $cM=new Comida();
    $eM=new EstudianteDetalle();
    $nutriologos = $uM->countByRole('nutriologo');
    $stats=[
        'usuarios'=>count($uM->all()),
        'nutrientes'=>count($nM->all()),
        'comidas'=>count($cM->all()),
        'estudiantes'=>count($eM->allWithUsers()),
        'nutriologos'=>$nutriologos
    ]; 
    $this->view('dashboard/admin',compact('stats'));
}
public function usuarios(){ $this->requireRole('admin'); $allUsers = (new User())->all(); $list = array_filter($allUsers, function($u) { return $u['rol'] !== 'nutriologo'; }); $this->view('admin/usuarios/index',compact('list'));}
public function usuarioForm(){ $this->requireRole('admin'); $id=$_GET['id']??null;$u=$id?(new User())->find($id):null; $this->view('admin/usuarios/form',compact('u'));}
public function usuarioSave(){ $this->requireRole('admin'); $uM=new User();$id=$_POST['id']??null;$data=['nombre'=>$_POST['nombre'],'apellidos'=>$_POST['apellidos'],'correo'=>$_POST['correo'],'rol'=>$_POST['rol']]; if($id){$uM->update($id,$data);}else{$data['password']=password_hash($_POST['password'],PASSWORD_BCRYPT);$uM->create($data);} redirect('admin','usuarios');}
public function usuarioDelete(){ $this->requireRole('admin'); (new User())->delete($_GET['id']); redirect('admin','usuarios');}
public function nutrientes(){ $this->requireRole('admin'); $list=(new Nutriente())->all(); $this->view('admin/nutrientes/index',compact('list'));}
public function nutrienteForm(){ $this->requireRole('admin'); $id=$_GET['id']??null;$n=$id?(new Nutriente())->find($id):null; $this->view('admin/nutrientes/form',compact('n'));}
public function nutrienteSave(){ $this->requireRole('admin'); $m=new Nutriente();$id=$_POST['id_nutriente']??null;$d=['nombre'=>$_POST['nombre'],'calorias_por_gramo'=>$_POST['calorias_por_gramo']??0,'unidad_medida'=>$_POST['unidad_medida'],'tipo'=>$_POST['tipo']]; if($id){$m->update($id,$d);}else{$m->create($d);} redirect('admin','nutrientes');}
public function nutrienteDelete(){ $this->requireRole('admin'); (new Nutriente())->delete($_GET['id']); redirect('admin','nutrientes');}
public function comidas()
{
    $this->requireRole('admin');
    $list = (new Comida())->all();
    $this->view('admin/comidas/index', compact('list'));
}

public function comidaForm()
{
    $this->requireRole('admin');
    $id = $_GET['id'] ?? null;
    $c  = $id ? (new Comida())->find($id) : null;
    $nutrientes = (new Nutriente())->all();

    $this->view('admin/comidas/form', compact('c', 'nutrientes'));
}

public function comidaSave()
{
    $this->requireRole('admin');
    $m  = new Comida();
    $id = $_POST['id_comida'] ?? null;
    
    $d = [
        'nombre' => $_POST['nombre'] ?? '',
        'descripcion' => $_POST['descripcion'] ?? '',
        'calorias_por_100g' => $_POST['calorias_por_100g'] ?? 0,
        'id_nutriente' => empty($_POST['id_nutriente']) ? null : $_POST['id_nutriente']
    ];

    if ($id) {
        $m->update($id, $d);
    } else {
        $m->create($d);
    }

    redirect('admin', 'comidas');
}

public function comidaDelete(){ $this->requireRole('admin'); (new Comida())->delete($_GET['id']); redirect('admin','comidas');}

public function nutriologos(){ 
    $this->requireRole('admin'); 
    require_once __DIR__.'/../models/NutriologoDetalle.php';
    $ndM = new NutriologoDetalle();
    $list = $ndM->allWithUsers();
    $this->view('admin/nutriologos/index', compact('list'));
}

public function nutriologoForm(){ 
    $this->requireRole('admin'); 
    $id = $_GET['id'] ?? null;
    $u = $id ? (new User())->find($id) : null;
    $nd = null;
    if($u) {
        require_once __DIR__.'/../models/NutriologoDetalle.php';
        $nd = (new NutriologoDetalle())->findByUserId($id);
    }
    $this->view('admin/nutriologos/form', compact('u', 'nd'));
}

public function nutriologoSave(){ 
    $this->requireRole('admin'); 
    $uM = new User();
    require_once __DIR__.'/../models/NutriologoDetalle.php';
    $ndM = new NutriologoDetalle();
    
    $id = $_POST['id'] ?? null;
    $data = [
        'nombre' => $_POST['nombre'],
        'apellidos' => $_POST['apellidos'],
        'correo' => $_POST['correo'],
        'rol' => 'nutriologo'
    ];
    
    if($id){
        $uM->update($id, $data);
    } else {
        $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $id = $uM->create($data);
    }
    
    redirect('admin', 'nutriologos');
}

public function nutriologoDelete(){ 
    $this->requireRole('admin'); 
    (new User())->delete($_GET['id']); 
    redirect('admin', 'nutriologos');
}

public function estudiantes(){
    $this->requireRole('admin');
    $eM = new EstudianteDetalle();
    $list = $eM->allWithUsers();
    $this->view('admin/estudiantes/index', compact('list'));
}
}
