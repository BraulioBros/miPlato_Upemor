<?php
/**
 * CONTROLADOR DE NUTRIÓLOGO
 * 
 * Maneja todas las funcionalidades del nutriólogo:
 * - Dashboard con estadísticas de estudiantes, nutrientes y comidas
 * - Completar su perfil (cédula y teléfono) en el primer login
 * - Gestión de estudiantes (ver, editar datos antropométricos)
 * - Gestión de nutrientes (crear, editar, eliminar)
 * - Gestión de comidas (crear, editar, eliminar)
 * 
 * Solo accesible por usuarios con rol 'nutriologo'
 */

require_once __DIR__.'/../models/Nutriente.php';require_once __DIR__.'/../models/Comida.php';require_once __DIR__.'/../models/EstudianteDetalle.php';require_once __DIR__.'/../models/NutriologoDetalle.php';
class NutriologoController extends Controller{
  
  /**
   * Muestra el dashboard principal del nutriólogo
   * 
   * Muestra estadísticas generales:
   * - Cantidad de estudiantes bajo su supervisión
   * - Cantidad total de nutrientes en el sistema
   * - Cantidad total de comidas en el sistema
   */
  public function dashboard(){ 
    $this->requireRole('nutriologo'); 
    $nM=new Nutriente();
    $cM=new Comida();
    $eM=new EstudianteDetalle();
    $stats=['estudiantes'=>count($eM->allWithUsers()),'nutrientes'=>count($nM->all()),'comidas'=>count($cM->all())]; 
    $this->view('dashboard/nutriologo',compact('stats'));
  }
  
  /**
   * Muestra el formulario para completar el perfil del nutriólogo
   * 
   * Se muestra al primer login si no ha completado sus datos
   * Requiere: cédula y teléfono
   */
  public function completar(){ 
    $this->requireRole('nutriologo'); 
    $this->view('nutriologo/completar',[]);
  }
  
  /**
   * Procesa el guardado de los datos de perfil del nutriólogo
   * 
   * Guarda:
   * - Cédula de identidad
   * - Teléfono de contacto
   * - Marca el perfil como completo (completed = 1)
   */
  public function completarSave(){ 
    $this->requireRole('nutriologo'); 
    $uid=$_SESSION['user']['id']; 
    (new NutriologoDetalle())->upsert($uid,$_POST['cedula'],$_POST['telefono'],1); 
    redirect('nutriologo','dashboard');
  }
  
  /**
   * Muestra la lista de todos los estudiantes del sistema
   * 
   * Muestra:
   * - Tabla con datos personales de los estudiantes
   * - Permite editar datos antropométricos (peso, altura, actividad, etc)
   */
  public function estudiantes(){ 
    $this->requireRole('nutriologo'); 
    $list=(new EstudianteDetalle())->allWithUsers(); 
    $this->view('nutriologo/estudiantes/index',compact('list'));
  }
  
  /**
   * Muestra el formulario para editar los datos antropométricos de un estudiante
   * 
   * Campos editables:
   * - Peso (kg)
   * - Altura (cm)
   * - Fecha de nacimiento
   * - Sexo (M/F)
   * - Factor de actividad
   */
  public function estudianteForm(){ 
    $this->requireRole('nutriologo'); 
    $id=$_GET['id']??null; 
    $det=$id?(new EstudianteDetalle())->findByUserId($id):null; 
    $this->view('nutriologo/estudiantes/form',['det'=>$det,'id'=>$id]);
  }
  
  /**
   * Procesa la actualización de datos antropométricos del estudiante
   * 
   * Valida:
   * - Que la fecha de nacimiento esté entre 1980-2009
   * - Guarda los datos: peso, altura, fecha_nacimiento, sexo, actividad
   */
  public function estudianteSave(){ 
    $this->requireRole('nutriologo'); 
    $id=$_POST['usuario_id']; 
    $fecha_nacimiento=$_POST['fecha_nacimiento']??null; 
    if($fecha_nacimiento){ 
      $min_fecha='1980-01-01'; 
      $max_fecha='2009-12-31'; 
      $timestamp=strtotime($fecha_nacimiento); 
      if($timestamp<strtotime($min_fecha) || $timestamp>strtotime($max_fecha)){ 
        redirect('nutriologo','estudiantes',['error'=>'La fecha de nacimiento debe estar entre 01/01/1980 y 31/12/2009']); 
      } 
    } 
    (new EstudianteDetalle())->upsert($id,$_POST['peso'],$_POST['altura'],$_POST['fecha_nacimiento'],$_POST['sexo'],$_POST['actividad']); 
    redirect('nutriologo','estudiantes');
  }
  
  /**
   * Muestra la lista de todos los nutrientes del sistema
   * 
   * Permite:
   * - Ver nombre, calorías por gramo, unidad de medida, tipo
   * - Editar y eliminar nutrientes
   */
  public function nutrientes(){ 
    $this->requireRole('nutriologo'); 
    $list=(new Nutriente())->all(); 
    $this->view('nutriologo/nutrientes/index',compact('list'));
  }
  
  /**
   * Muestra el formulario para crear o editar un nutriente
   */
  public function nutrienteForm(){ 
    $this->requireRole('nutriologo'); 
    $id=$_GET['id']??null; 
    $n=$id?(new Nutriente())->find($id):null; 
    $this->view('nutriologo/nutrientes/form',compact('n'));
  }
  
  /**
   * Procesa la creación o actualización de un nutriente
   * 
   * Datos:
   * - nombre: nombre descriptivo
   * - calorias_por_gramo: contenido calórico
   * - unidad_medida: unidad (g, ml, etc)
   * - tipo: clasificación del nutriente
   * Valida que el nombre no esté duplicado
   */
  public function nutrienteSave(){ 
    $this->requireRole('nutriologo'); 
    $m=new Nutriente(); 
    $id_nutriente=$_POST['id_nutriente']??null; 
    $nombre=$_POST['nombre']??'';
    $d=['nombre'=>$nombre,'calorias_por_gramo'=>$_POST['calorias_por_gramo']??0,'unidad_medida'=>$_POST['unidad_medida'],'tipo'=>$_POST['tipo']]; 
    
    // Valida que el nombre no esté duplicado
    if($id_nutriente){
      // Al actualizar, excluye el ID actual de la búsqueda
      if($m->findByNameExcludingId($nombre, $id_nutriente)){
        redirect('nutriologo','nutrientes',['error'=>'⚠️ Ya existe un nutriente con ese nombre']);
      }
      $m->update($id_nutriente,$d);
    }else{
      // Al crear, valida que el nombre no exista
      if($m->findByName($nombre)){
        redirect('nutriologo','nutrientes',['error'=>'⚠️ Ya existe un nutriente con ese nombre']);
      }
      $m->create($d);
    } 
    redirect('nutriologo','nutrientes');
  }
  
  /**
   * Elimina un nutriente del sistema
   */
  public function nutrienteDelete(){ 
    $this->requireRole('nutriologo'); 
    (new Nutriente())->delete($_GET['id']); 
    redirect('nutriologo','nutrientes');
  }
  
  /**
   * Muestra la lista de todas las comidas del sistema
   * 
   * Permite:
   * - Ver nombre, descripción, calorías por 100g, nutriente asociado
   * - Editar y eliminar comidas
   */
  public function comidas(){ 
    $this->requireRole('nutriologo'); 
    $list=(new Comida())->all(); 
    $this->view('nutriologo/comidas/index',compact('list'));
  }
  
  /**
   * Muestra el formulario para crear o editar una comida
   * 
   * Obtiene:
   * - Datos de la comida a editar (si viene con 'id')
   * - Lista de nutrientes disponibles para asociar
   */
  public function comidaForm(){ 
    $this->requireRole('nutriologo'); 
    $id=$_GET['id']??null; 
    $c=$id?(new Comida())->find($id):null; 
    $nutrientes=(new Nutriente())->all(); 
    $this->view('nutriologo/comidas/form',compact('c','nutrientes'));
  }
  
  /**
   * Procesa la creación o actualización de una comida
   * 
   * Datos:
   * - nombre: nombre descriptivo (REQUERIDO)
   * - descripcion: detalles adicionales (REQUERIDO)
   * - calorias_por_100g: contenido calórico por 100g (REQUERIDO)
   * - id_nutriente: nutriente principal asociado (REQUERIDO)
   * Valida que todos los campos obligatorios estén completos
   * Valida que el nombre no esté duplicado
   */
  public function comidaSave(){ 
    $this->requireRole('nutriologo'); 
    $m=new Comida(); 
    $id=$_POST['id_comida']??null; 
    $nombre=trim($_POST['nombre']??'');
    $descripcion=trim($_POST['descripcion']??'');
    $calorias=$_POST['calorias_por_100g']??'';
    $id_nutriente=$_POST['id_nutriente']??''; 
    
    // Valida que todos los campos obligatorios estén completos
    if(empty($nombre) || empty($descripcion) || $calorias === '' || $calorias < 0){
      redirect('nutriologo','comidas',['error'=>'⚠️ Completa todos los campos (nombre, descripción y calorías)']);
    }
    
    // Valida que el nombre tenga al menos 3 caracteres
    if(strlen($nombre) < 3){
      redirect('nutriologo','comidas',['error'=>'⚠️ El nombre debe tener al menos 3 caracteres']);
    }
    
    // Valida que el nutriente esté seleccionado
    if(empty($id_nutriente)){
      redirect('nutriologo','comidas',['error'=>'⚠️ Debes seleccionar un nutriente']);
    }
    
    $d=['nombre'=>$nombre,'descripcion'=>$descripcion,'calorias_por_100g'=>(float)$calorias,'id_nutriente'=>(int)$id_nutriente]; 
    
    // Valida que el nombre no esté duplicado
    if($id){
      // Al actualizar, excluye el ID actual de la búsqueda
      if($m->findByNameExcludingId($nombre, $id)){
        redirect('nutriologo','comidas',['error'=>'⚠️ Ya existe una comida con ese nombre']);
      }
      $m->update($id,$d);
    }else{
      // Al crear, valida que el nombre no exista
      if($m->findByName($nombre)){
        redirect('nutriologo','comidas',['error'=>'⚠️ Ya existe una comida con ese nombre']);
      }
      $m->create($d);
    } 
    redirect('nutriologo','comidas');
  }
  
  /**
   * Elimina una comida del sistema
   */
  public function comidaDelete(){ 
    $this->requireRole('nutriologo'); 
    (new Comida())->delete($_GET['id']); 
    redirect('nutriologo','comidas');
  }
}
