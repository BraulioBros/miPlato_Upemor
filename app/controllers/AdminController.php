<?php
/**
 * CONTROLADOR DE ADMINISTRADOR
 * 
 * Maneja todas las funcionalidades administrativas del sistema:
 * - Dashboard con estadísticas generales
 * - Gestión de usuarios (crear, editar, eliminar)
 * - Gestión de nutrientes (crear, editar, eliminar)
 * - Gestión de comidas (crear, editar, eliminar)
 * - Gestión de nutriólogos (crear, editar, eliminar)
 * - Gestión de estudiantes
 * 
 * Todos los métodos requieren rol de 'admin' para ser accedidos
 */

require_once __DIR__.'/../models/User.php';
require_once __DIR__.'/../models/Nutriente.php';
require_once __DIR__.'/../models/Comida.php';
require_once __DIR__.'/../models/EstudianteDetalle.php';

class AdminController extends Controller{
  
  /**
   * Muestra el dashboard principal del administrador
   * 
   * Muestra estadísticas generales del sistema:
   * - Cantidad total de usuarios
   * - Cantidad total de nutrientes registrados
   * - Cantidad total de comidas registradas
   * - Cantidad total de estudiantes
   * - Cantidad total de nutriólogos
   */
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

/**
 * Muestra la lista de todos los usuarios (excepto nutriólogos)
 * 
 * Muestra:
 * - Tabla con todos los usuarios registrados en el sistema
 * - Excluye a los nutriólogos (se manejan aparte)
 * - Permite editar y eliminar usuarios
 */
public function usuarios(){ 
    $this->requireRole('admin'); 
    $allUsers = (new User())->all(); 
    $list = array_filter($allUsers, function($u) { return $u['rol'] !== 'nutriologo'; }); 
    $this->view('admin/usuarios/index',compact('list'));
}

/**
 * Muestra el formulario para crear o editar un usuario
 * 
 * Si viene con parámetro 'id': muestra el formulario con los datos del usuario
 * Si no viene con 'id': muestra el formulario vacío para crear un nuevo usuario
 */
public function usuarioForm(){ 
    $this->requireRole('admin'); 
    $id=$_GET['id']??null;
    $u=$id?(new User())->find($id):null; 
    $this->view('admin/usuarios/form',compact('u'));
}

/**
 * Procesa la creación o actualización de un usuario
 * 
 * - Si viene con 'id': actualiza el usuario existente
 * - Si no viene con 'id': crea un nuevo usuario
 * - La contraseña se hashea con PASSWORD_BCRYPT
 * - Valida que el email no esté duplicado
 */
public function usuarioSave(){ 
    $this->requireRole('admin'); 
    $uM=new User();
    $id=$_POST['id']??null;
    $email=trim($_POST['correo']??'');
    
    // Valida que el email no esté vacío
    if(empty($email)){
        redirect('admin','usuarios',['error'=>'⚠️ El correo no puede estar vacío']);
    }
    
    // Valida que sea un email válido
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        redirect('admin','usuarios',['error'=>'⚠️ El correo no es válido']);
    }
    
    $data=['nombre'=>trim($_POST['nombre']??''),'apellidos'=>trim($_POST['apellidos']??''),'correo'=>$email,'rol'=>$_POST['rol']]; 
    
    // Valida que los campos obligatorios no estén vacíos
    if(empty($data['nombre']) || empty($data['apellidos'])){
        redirect('admin','usuarios',['error'=>'⚠️ Nombre y apellidos son obligatorios']);
    }
    
    // Valida que el email no esté duplicado
    if($id){
        // Al actualizar, excluye el ID actual de la búsqueda
        if($uM->findByEmailExcludingId($email, $id)){
            redirect('admin','usuarios',['error'=>'⚠️ Este correo ya está registrado']);
        }
        try{
            $uM->update($id,$data);
        }catch(Exception $e){
            redirect('admin','usuarios',['error'=>'⚠️ Error al actualizar: '.$e->getMessage()]);
        }
    }else{
        // Al crear, valida que el email no exista
        if($uM->findByEmail($email)){
            redirect('admin','usuarios',['error'=>'⚠️ Este correo ya está registrado']);
        }
        $data['password']=password_hash($_POST['password'],PASSWORD_BCRYPT);
        try{
            $uM->create($data);
        }catch(Exception $e){
            redirect('admin','usuarios',['error'=>'⚠️ Error al crear usuario: Este correo ya está registrado']);
        }
    } 
    redirect('admin','usuarios');
}

/**
 * Elimina un usuario del sistema
 */
public function usuarioDelete(){ 
    $this->requireRole('admin'); 
    (new User())->delete($_GET['id']); 
    redirect('admin','usuarios');
}

/**
 * Muestra la lista de todos los nutrientes registrados
 */
public function nutrientes(){ 
    $this->requireRole('admin'); 
    $list=(new Nutriente())->all(); 
    $this->view('admin/nutrientes/index',compact('list'));
}

/**
 * Muestra el formulario para crear o editar un nutriente
 */
public function nutrienteForm(){ 
    $this->requireRole('admin'); 
    $id=$_GET['id']??null;
    $n=$id?(new Nutriente())->find($id):null; 
    $this->view('admin/nutrientes/form',compact('n'));
}

/**
 * Procesa la creación o actualización de un nutriente
 * 
 * Datos: nombre, calorías por gramo, unidad de medida, tipo
 * Valida que el nombre no esté duplicado
 */
public function nutrienteSave(){ 
    $this->requireRole('admin'); 
    $m=new Nutriente();
    $id=$_POST['id_nutriente']??null;
    $nombre=$_POST['nombre']??'';
    $d=['nombre'=>$nombre,'calorias_por_gramo'=>$_POST['calorias_por_gramo']??0,'unidad_medida'=>$_POST['unidad_medida'],'tipo'=>$_POST['tipo']]; 
    
    // Valida que el nombre no esté duplicado
    if($id){
        // Al actualizar, excluye el ID actual de la búsqueda
        if($m->findByNameExcludingId($nombre, $id)){
            redirect('admin','nutrientes',['error'=>'⚠️ Ya existe un nutriente con ese nombre']);
        }
        $m->update($id,$d);
    }else{
        // Al crear, valida que el nombre no exista
        if($m->findByName($nombre)){
            redirect('admin','nutrientes',['error'=>'⚠️ Ya existe un nutriente con ese nombre']);
        }
        $m->create($d);
    } 
    redirect('admin','nutrientes');
}

/**
 * Elimina un nutriente del sistema
 */
public function nutrienteDelete(){ 
    $this->requireRole('admin'); 
    (new Nutriente())->delete($_GET['id']); 
    redirect('admin','nutrientes');
}

/**
 * Muestra la lista de todas las comidas registradas
 */
public function comidas()
{
    $this->requireRole('admin');
    $list = (new Comida())->all();
    $this->view('admin/comidas/index', compact('list'));
}

/**
 * Muestra el formulario para crear o editar una comida
 * 
 * Obtiene:
 * - Datos de la comida a editar (si viene con parámetro 'id')
 * - Lista de nutrientes disponibles para asociar
 */
public function comidaForm()
{
    $this->requireRole('admin');
    $id = $_GET['id'] ?? null;
    $c  = $id ? (new Comida())->find($id) : null;
    $nutrientes = (new Nutriente())->all();

    $this->view('admin/comidas/form', compact('c', 'nutrientes'));
}

/**
 * Procesa la creación o actualización de una comida
 * 
 * Datos de la comida:
 * - nombre: nombre descriptivo (REQUERIDO)
 * - descripcion: detalles adicionales (REQUERIDO)
 * - calorias_por_100g: contenido calórico por 100 gramos (REQUERIDO)
 * - id_nutriente: nutriente principal asociado (REQUERIDO)
 * Valida que todos los campos obligatorios estén completos
 * Valida que el nombre no esté duplicado
 */
public function comidaSave()
{
    $this->requireRole('admin');
    $m  = new Comida();
    $id = $_POST['id_comida'] ?? null;
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $calorias = $_POST['calorias_por_100g'] ?? '';
    
    // Valida que todos los campos obligatorios estén completos
    if(empty($nombre) || empty($descripcion) || $calorias === '' || $calorias < 0){
        redirect('admin','comidas',['error'=>'⚠️ Completa todos los campos (nombre, descripción y calorías)']);
    }
    
    // Valida que el nombre tenga al menos 3 caracteres
    if(strlen($nombre) < 3){
        redirect('admin','comidas',['error'=>'⚠️ El nombre debe tener al menos 3 caracteres']);
    }
    
    // Valida que el nutriente esté seleccionado
    $id_nutriente = $_POST['id_nutriente'] ?? '';
    if(empty($id_nutriente)){
        redirect('admin','comidas',['error'=>'⚠️ Debes seleccionar un nutriente']);
    }
    
    $d = [
        'nombre' => $nombre,
        'descripcion' => $descripcion,
        'calorias_por_100g' => (float)$calorias,
        'id_nutriente' => (int)$id_nutriente
    ];

    // Valida que el nombre no esté duplicado
    if ($id) {
        // Al actualizar, excluye el ID actual de la búsqueda
        if($m->findByNameExcludingId($nombre, $id)){
            redirect('admin','comidas',['error'=>'⚠️ Ya existe una comida con ese nombre']);
        }
        $m->update($id, $d);
    } else {
        // Al crear, valida que el nombre no exista
        if($m->findByName($nombre)){
            redirect('admin','comidas',['error'=>'⚠️ Ya existe una comida con ese nombre']);
        }
        $m->create($d);
    }

    redirect('admin', 'comidas');
}

/**
 * Elimina una comida del sistema
 */
public function comidaDelete(){ 
    $this->requireRole('admin'); 
    (new Comida())->delete($_GET['id']); 
    redirect('admin','comidas');
}

/**
 * Muestra la lista de todos los nutriólogos registrados
 */
/**
 * Muestra la lista de todos los nutriólogos registrados
 */
public function nutriologos(){ 
    $this->requireRole('admin'); 
    require_once __DIR__.'/../models/NutriologoDetalle.php';
    $ndM = new NutriologoDetalle();
    $list = $ndM->allWithUsers();
    $this->view('admin/nutriologos/index', compact('list'));
}

/**
 * Muestra el formulario para crear o editar un nutriólogo
 */
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

/**
 * Procesa la creación o actualización de un nutriólogo
 * 
 * Datos:
 * - nombre, apellidos, correo: datos personales
 * - rol: siempre se establece como 'nutriologo'
 * - password: solo se requiere al crear un nuevo usuario
 * - Valida que el email no esté duplicado
 */
public function nutriologoSave(){ 
    $this->requireRole('admin'); 
    $uM = new User();
    require_once __DIR__.'/../models/NutriologoDetalle.php';
    $ndM = new NutriologoDetalle();
    
    $id = $_POST['id'] ?? null;
    $email = trim($_POST['correo'] ?? '');
    
    // Valida que el email no esté vacío
    if(empty($email)){
        redirect('admin','nutriologos',['error'=>'⚠️ El correo no puede estar vacío']);
    }
    
    // Valida que sea un email válido
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        redirect('admin','nutriologos',['error'=>'⚠️ El correo no es válido']);
    }
    
    $data = [
        'nombre' => trim($_POST['nombre'] ?? ''),
        'apellidos' => trim($_POST['apellidos'] ?? ''),
        'correo' => $email,
        'rol' => 'nutriologo'
    ];
    
    // Valida que los campos obligatorios no estén vacíos
    if(empty($data['nombre']) || empty($data['apellidos'])){
        redirect('admin','nutriologos',['error'=>'⚠️ Nombre y apellidos son obligatorios']);
    }
    
    if($id){
        // Al actualizar, valida que el email no esté duplicado (excluyendo el ID actual)
        if($uM->findByEmailExcludingId($email, $id)){
            redirect('admin','nutriologos',['error'=>'⚠️ Este correo ya está registrado']);
        }
        try{
            $uM->update($id, $data);
        }catch(Exception $e){
            redirect('admin','nutriologos',['error'=>'⚠️ Error al actualizar: Este correo ya está registrado']);
        }
    } else {
        // Al crear, valida que el email no exista
        if($uM->findByEmail($email)){
            redirect('admin','nutriologos',['error'=>'⚠️ Este correo ya está registrado']);
        }
        $data['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        try{
            $id = $uM->create($data);
        }catch(Exception $e){
            redirect('admin','nutriologos',['error'=>'⚠️ Error al crear nutriólogo: Este correo ya está registrado']);
        }
    }
    
    redirect('admin', 'nutriologos');
}

/**
 * Elimina un nutriólogo del sistema
 */
public function nutriologoDelete(){ 
    $this->requireRole('admin'); 
    (new User())->delete($_GET['id']); 
    redirect('admin', 'nutriologos');
}

/**
 * Muestra la lista de todos los estudiantes registrados
 */
public function estudiantes(){
    $this->requireRole('admin');
    $eM = new EstudianteDetalle();
    $list = $eM->allWithUsers();
    $this->view('admin/estudiantes/index', compact('list'));
}
}
