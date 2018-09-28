<?php
App::uses('AppController', 'Controller');

class PersonasController extends AppController {

	var $name = 'Personas';
	var $paginate = array('Persona' => array('limit' => 3, 'order' => 'Persona.id DESC'));

	function beforeFilter(){
        parent::beforeFilter();
	    /* ACCESOS SEGÚN ROLES DE USUARIOS (INICIO).
        *  Si el usuario tiene un rol de superadmin le damos acceso a todo.
        *  Si no es así (se trata de un usuario "admin o usuario") tendrá acceso sólo a las acciones que les correspondan.
        */
        if ($this->Auth->user('role') === 'superadmin') {
	        $this->Auth->allow();
	    } elseif (($this->Auth->user('role') === 'admin') || ($this->Auth->user('role') === 'usuario')) {
	        $this->Auth->allow('index', 'add' , 'view', 'edit', 'autocompletePersonas','listarBarrios','listarAsentamientos');
	    }
	    /* FIN */
	    /* FUNCIÓN PRIVADA "LISTS" (INICIO).
        *Si se ejecutan las acciones add/edit activa la función privada "lists".
		*/
		if ($this->ifActionIs(array('add', 'edit'))) {
			$this->__lists();
		}
		/* FIN */
		App::uses('HttpSocket', 'Network/Http');
    }

	public function index() {
		$this->Persona->recursive = 0;
		$this->paginate['Persona']['limit'] = 4;
		$this->paginate['Persona']['order'] = array('Persona.id' => 'ASC');
		/* PAGINACIÓN SEGÚN ROLES DE USUARIOS (INICIO).
		*  Sí es "admin" no muestra alumnos ni familiares.
		*/
		$userCentroId = $this->getUserCentroId();
		$userRole = $this->Auth->user('role');
		if ($userRole === 'admin') {
			$this->paginate['Persona']['conditions'] = array('Persona.id' => 0);
		}
		/* FIN */
		/* PAGINACIÓN SEGÚN CRITERIOS DE BÚSQUEDAS (INICIO).
		*Pagina según búsquedas simultáneas ya sea por NOMBRE COMPLETO y/o DNI.
		*/
		$this->redirectToNamed();
		$conditions = array();
		if(!empty($this->params['named']['nombre_completo_persona']))
		{
			$conditions['Persona.nombre_completo_persona ='] = $this->params['named']['nombre_completo_persona'];
		}
		if(!empty($this->params['named']['documento_nro']))
		{
			$conditions['Persona.documento_nro ='] = $this->params['named']['documento_nro'];
		}
        $personas = $this->paginate('Persona', $conditions);
        /* Evalúa si existe foto (INICIO). */
		if(empty($this->params['named']['foto'])){
			$foto = 0;
		} else {
			$foto = 1;
		}
		/* FIN */
		/* SETS DE DATOS PARA COMBOBOXS DEL FORM SEARCH (INICIO).*/
		$this->loadModel('Ciudad');
        $this->Ciudad->Behaviors->load('Containable');
        $this->Ciudad->recursive=0;
        $ciudades = $this->Ciudad->find('list', array('fields' => array('nombre'),'contain'=>false));
        // Obtiene el nivel del centro para que se muestre o no el botón "AGREGAR" persona.
     	$this->loadModel('Centro');
     	$this->Centro->Behaviors->load('Containable');
        $this->Centro->recursive=0;
		$nivelCentro = $this->Centro->find('list', array(
			'fields'=>array('id','nivel_servicio'), 
			'contain'=>false,
			'conditions'=>array('id'=>$userCentroId)));
		$nivelCentroId = $this->Centro->find('list', array(
			'fields'=>array('id'),
			'contain'=>false,
			'conditions'=>array('nivel_servicio'=>$nivelCentro)));
		$nivelCentroArray = $this->Centro->findById($nivelCentroId, 'nivel_servicio');
		$nivelCentroString = $nivelCentroArray['Centro']['nivel_servicio'];
		/* FIN */
		$this->set(compact('personas', 'foto', 'ciudades', 'nivelCentroString'));
	}

	public function view($id = null) {
		$this->Persona->recursive = 0;
		if (!$id) {
			$this->Session->setFlash('Persona no valida', 'default', array('class' => 'alert alert-danger'));
			$this->redirect(array('action' => 'index'));
		}
		$options = array('conditions' => array('Persona.' . $this->Persona->primaryKey => $id));
		$this->set('persona', $this->Persona->read(null, $id));
        //Evalúa si existe foto.
		if(empty($this->params['named']['foto'])){
			$foto = 0;
		} else {
			$foto = 1;
		}
    	$persona = $this->Persona->findById($id,'alumno');
        $personaAlumno = $persona['Persona']['alumno'];
        if ($personaAlumno == 1) {
        	/*INICIO: Identificación de la inscripción actual y su estado en una persona con perfil de alumno*/
    		//Obtención de DNI de la persona.
	    	$persona = $this->Persona->findById($id,'id, documento_nro');
	        $personaDni = $persona['Persona']['documento_nro'];
	    	//Obtención del ciclo actual.
	    	$ciclo = '18';
	    	//Obtención del tipo y estado de inscripción actual.
	    	$this->loadModel('Inscripcion');
	        $this->Inscripcion->recursive = 0;
	        $this->Inscripcion->Behaviors->load('Containable');
	    	//Obtención de los posibles códigos de inscripción (Ordinaria o Pase).
	    	$codigoActualPosible = $this->__getCodigo($ciclo, $personaDni);
	    	$paseNro = 1; 
	        $codigoActualPasePosible = $this->__getCodigoPase($ciclo, $personaDni, $paseNro);
	    	//Verificación de existencia de inscripciones Ordinarias o por Pase con los códigos posibles.
	    	$existeInscripcionOrdinaria = $this->Inscripcion->find('first',array(
	                 'contain' => false,
	                 'conditions' => array('Inscripcion.legajo_nro' => $codigoActualPosible)));
	    	$existeInscripcionPase = $this->Inscripcion->find('first',array(
	                 'contain' => false,
	                 'conditions' => array('Inscripcion.legajo_nro' => $codigoActualPasePosible)));
	    	//Identificación del código de inscripción cualquiera sea el tipo.
	    	if (isset($existeInscripcionPase['Inscripcion']['legajo_nro'])) {
	    		$codigoActual = $codigoActualPasePosible;
	    		$tipoInscripcion = 'Pase';
	    	} else if (isset($existeInscripcionOrdinaria['Inscripcion']['legajo_nro'])) {
	    		$codigoActual = $codigoActualPosible;
	    		$tipoInscripcionArray = $this->Inscripcion->findByLegajoNro($codigoActual,'tipo_inscripcion');
	        	$tipoInscripcion = $tipoInscripcionArray['Inscripcion']['tipo_inscripcion'];
	        } else {
	        	$this->Session->setFlash('No registra inscripción en el ciclo actual', 'default', array('class' => 'alert alert-info'));
	        }
	    	if (($existeInscripcionPase) || ($existeInscripcionOrdinaria)) {
	    		//Obtención del id y del estado de esa inscripción.
	   		 	$estadoInscripcionArray = $this->Inscripcion->findByLegajoNro($codigoActual,'id, estado_inscripcion');
	   		 	$idInscripcion = $estadoInscripcionArray['Inscripcion']['id'];
		    	$estadoInscripcion = $estadoInscripcionArray['Inscripcion']['estado_inscripcion'];
		    	//Obtención del centro de esa inscripción.
		        $this->loadModel('Centro');
		        $this->Centro->recursive = 0;
		        $this->Centro->Behaviors->load('Containable');
		        $idCentroInscripcionArray = $this->Inscripcion->findByLegajoNro($codigoActual,'centro_id');
		        $idCentroInscripcion = $idCentroInscripcionArray['Inscripcion']['centro_id'];
		        $nombreCentroInscripcionArray = $this->Centro->findById($idCentroInscripcion,'nombre');
		        $nombreCentroInscripcion = $nombreCentroInscripcionArray['Centro']['nombre'];
		        //Obtención de los datos de la sección de esa inscripción.
		        $this->loadModel('CursosInscripcions');
		        $this->CursosInscripcions->recursive = 0;
		        $this->CursosInscripcions->Behaviors->load('Containable');
		        $idSeccionInscripcionArray = $this->CursosInscripcions->findByInscripcionId($idInscripcion,'curso_id');
		        $idSeccionInscripcion = $idSeccionInscripcionArray['CursosInscripcions']['curso_id'];
		        $this->loadModel('Curso');
		        $this->Curso->recursive = 0;
		        $this->Curso->Behaviors->load('Containable');
		        $nombreSeccionArray = $this->Curso->findById($idSeccionInscripcion,'nombre_completo_curso');
		        $nombreSeccion = $nombreSeccionArray['Curso']['nombre_completo_curso'];
		        //Visualización del mensaje al usuario de los datos de inscripción en el ciclo actual.
		        $this->Session->setFlash("En el ciclo actual registra inscripción en".' '.$nombreCentroInscripcion.' '.' en '.$nombreSeccion.' con estado: '.' '.$estadoInscripcion, 'default', array('class' => 'alert alert-info'));
	    	}
	    	/*FIN*/
        }
    	$this->set(compact('foto'));
     }

	public function add() {
		$this->Persona->recursive = 0;
		//abort if cancel button was pressed
        if(isset($this->params['data']['cancel'])){
                $this->Session->setFlash('Los cambios no fueron guardados. Agregación cancelada.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect( array( 'action' => 'index' ));
		}
		if (!empty($this->data)) {
			$this->Persona->create();
		    // Antes de guardar pasa a mayúsculas el nombre completo.
			$apellidosMayuscula = strtr(strtoupper($this->request->data['Persona']['apellidos']), "àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
		  	$nombresMayuscula = strtr(strtoupper($this->request->data['Persona']['nombres']), "àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
			// Genera el nombre completo en mayúsculas y se deja en los datos que se intentaran guardar
			$this->request->data['Persona']['apellidos'] = $apellidosMayuscula;
			$this->request->data['Persona']['nombres'] = $nombresMayuscula;
			// Antes de guardar calcula la edad, por algun motivo no puedo usar en la vista el nombre fecha_nac
			$fechaNacimiento = $this->request->data['Persona']['fecha_nacimiento'];
			if(!empty($fechaNacimiento)) {
			  $fechaNacimiento = explode('/',$fechaNacimiento);
			  $day = $fechaNacimiento[0];
			  $month = $fechaNacimiento[1];
			  $year = $fechaNacimiento[2];
			  $this->request->data['Persona']['fecha_nac'] = [
				  'day' => $day,
				  'month' => $month,
				  'year' => $year
			  ];
			// Calcula la edad y se deja en los datos que se intentaran guardar
			$this->request->data['Persona']['edad'] = $this->__getEdad($day, $month, $year);
			}
			if ($this->Persona->save($this->data)) {
				$this->Session->setFlash('La persona ha sido grabada.', 'default', array('class' => 'alert alert-success'));
				//$inserted_id = $this->Persona->id;
				//$this->redirect(array('action' => 'view', $inserted_id));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash('La persona no fué grabada. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			}
		}
		$this->set(compact('fechaNacimiento'));
	}

	function edit($id = null) {
	    $this->Persona->recursive = 0;
	    if (!$id && empty($this->data)) {
			$this->Session->setFlash('Persona no válida', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action' => 'index'));
		}
		if(!$this->adminCanEdit($id)) {
			$this->Session->setFlash('No tiene permisos para editar a esta persona, no pertenece a su establecimiento', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
		  //abort if cancel button was pressed
        	if(isset($this->params['data']['cancel'])) {
                $this->Session->setFlash('Los cambios no fueron guardados. Edición cancelada.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect( array( 'action' => 'index' ));
		  	}
          	// Antes de guardar pasa a mayúsculas el nombre completo.
		  	$apellidosMayuscula = strtr(strtoupper($this->request->data['Persona']['apellidos']), "àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
		  	$nombresMayuscula = strtr(strtoupper($this->request->data['Persona']['nombres']), "àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
		  	// Genera el nombre completo en mayúsculas y se deja en los datos que se intentaran guardar
		  	$this->request->data['Persona']['apellidos'] = $apellidosMayuscula;
		  	$this->request->data['Persona']['nombres'] = $nombresMayuscula;
    	  	// Antes de guardar calcula la edad
  		  	$fechaNacimiento = $this->request->data['Persona']['fecha_nacimiento'];
			if(!empty($fechaNacimiento)) {
				$fechaNacimiento = explode('/',$fechaNacimiento);
				$day = $fechaNacimiento[0];
				$month = $fechaNacimiento[1];
				$year = $fechaNacimiento[2];
				// Calcula la edad y se deja en los datos que se intentaran guardar
				$this->request->data['Persona']['edad'] = $this->__getEdad($day, $month, $year);
				// Es necesario para guardar en la DB
				$this->request->data['Persona']['fecha_nac'] = [
					'day' => $day,
					'month' => $month,
					'year' => $year
				];
			}
		  	if ($this->Persona->save($this->data)) {
				$this->Session->setFlash('La persona ha sido grabada', 'default', array('class' => 'alert alert-success'));
				$inserted_id = $this->Persona->id;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
				$this->Session->setFlash('La persona no ha sido grabado. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Persona->read(null, $id);
			list($año,$mes,$dia) = explode('-',$this->request->data['Persona']['fecha_nac']);
			$fechaNacimiento =  "$dia/$mes/$año";
		}
		// Esta fecha tiene el formato dia/mes/año
		$this->set(compact('fechaNacimiento'));
	}

	public function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Id no válido para la persona', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Alumno->delete($id)) {
			$this->Session->setFlash('La persona ha sido borrado', 'default', array('class' => 'alert alert-success'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('La persona no fue borrado', 'default', array('class' => 'alert alert-danger'));
		$this->redirect(array('action' => 'index'));
	}

	//Métodos privados
	private function __lists(){
	    $this->loadModel('Ciudad');
   		$this->Ciudad->recursive = 0;
        $this->Ciudad->Behaviors->load('Containable');
   		$ciudades = $this->Ciudad->find('list', array('fields' => array('nombre'),'contain'=>false));
   		$this->loadModel('Barrio');
   		$this->Barrio->recursive = 0;
   		$this->Barrio->Behaviors->load('Containable');
       	$barrios = $this->Barrio->find('list', array('fields' => array('nombre'),'contain'=>false));
       	$this->loadModel('PuebloOriginario');
       	$this->PuebloOriginario->recursive = 0;
       	$this->PuebloOriginario->Behaviors->load('Containable');
	 	$nativos = $this->PuebloOriginario->find('list', array('fields' => array('nombre'),'contain'=>false));
	 	$this->loadModel('Asentamiento');
	  	$this->Asentamiento->recursive = 0;
	  	$this->Asentamiento->Behaviors->load('Containable');
	  	$asentamientos = $this->Asentamiento->find('list', array('fields' => array('nombre'),'contain'=>false));
    	$this->set(compact('ciudades', 'barrios', 'nativos', 'asentamientos'));
	}

	public function listarBarrios($id) {
		if (is_numeric($id)) {
			$this->layout = 'ajax';
			$this->loadModel('Barrio');
			$lista_barrios=$this->Barrio->find('list',array('conditions' => array('ciudad_id' => $id)));
			$this->set('lista_barrios',$lista_barrios);
	    }
		echo json_encode($lista_barrios);
		$this->autoRender = false;
	}
	
	public function listarAsentamientos($id) {
		if (is_numeric($id)) {
			$this->layout = 'ajax';
			$this->loadModel('Asentamiento');
			$lista_asentamientos=$this->Asentamiento->find('list',array('conditions' => array('Asentamiento.ciudad_id' => $id)));
			$this->set('lista_asentamientos',$lista_asentamientos);
	    }
		echo json_encode($lista_asentamientos);
		$this->autoRender = false;
	}

	//Métodos Privados
	
	private function __getEdad($day, $month, $year) {
		$from = new DateTime($year . '-' . $month . '-' . $day );
		$to   = new DateTime('today');
		return $from->diff($to)->y;
    }
	
	private function adminCanEdit($personaId) {
		//Se obtiene el rol del usuario
		$userRole = $this->Auth->user('role');
		$userData = $this->Auth->user();
		//  El rol ADMIN puede editar a la persona solo si ésta pertenece a su institucion como alumno
		if($userRole == 'admin') {
			//Obtenemos algunos datos de esa personaId
			$apiPersona = $this->consumeApiPersona($personaId);
			// Si no existe error al consumir el api
			if(!isset($apiPersona['error'])) {
				$userCentroId = (int) $userData['Centro']['id'];
				$ultimaInscripcionCentroId = (int) $apiPersona['inscripcion']['centro_id'];
				// Si la ultima inscripcion de la persona pertenece al establecimiento del usuario admin actual, puede editar
				if($userCentroId == $ultimaInscripcionCentroId) {
					return true;
				} else {
					return false;
				}
			} else {
				// Error al consumir el API
				$this->Session->setFlash($apiPersona['error'], 'default', array('class' => 'alert alert-danger'));
				$this->redirect(array('action' => 'index'));
				return false;
			}
		} else {
			// El resto de los roles puede editar
			return true;
		}
	}

	public function consumeApiPersona($personaId) {
		try 
		{
			$hostApi = getenv('HOSTAPI');
			$httpSocket = new HttpSocket();
			$request = array('header' => array('Content-Type' => 'application/json'));
			// Datos de la ultima inscripcion de la persona
			$data['ver'] = 'ultima';
			$response = $httpSocket->get("http://$hostApi/api/inscripcion/find/persona/$personaId", $data, $request);
			$response = $response->body;
			$apiResponse = json_decode($response,true);
			return $apiResponse;
		} catch(Exception $ex)
		{
			return ['error'=>$ex->getMessage()];
		}
	}

	public function autocompletePersonas() {
		$conditions = array();
		$term = $this->request->query('term');
		if(!empty($term)) {
			// Si se busca un numero de documento.. se raliza el siguiente filtro
			if(is_numeric($term)) {
				$conditions[] = array(
						'OR' => array(
							array('documento_nro LIKE' => $term . '%')
						)
				);
			} else {
				// Se esta buscando por nombre y/o apellidos
				$terminos = explode(' ', trim($term));
				$terminos = array_diff($terminos,array(''));
				foreach($terminos as $termino) {
					$conditions[] = array(
							'OR' => array(
								array('nombres LIKE' => '%' . $termino . '%'),
								array('apellidos LIKE' => '%' . $termino . '%')
							)
					);
				}
			}
			$personas = $this->Persona->find('all', array(
					'recursive'	=> -1,
					'conditions' => $conditions,
					'fields' 	=> array('id', 'nombres','apellidos','documento_nro'))
			);
		}
		echo json_encode($personas);
		$this->autoRender = false;
	}

	private function __getCodigo($ciclo, $personaDocString){
		$legajo = $personaDocString."-".$ciclo;
		return $legajo;
    }

    private function __getCodigoPase($ciclo, $personaDocString, $paseNro){
        $legajo = $personaDocString."-".$ciclo."-"."PASE"."_".$paseNro;
        return $legajo;
    }
}
?>
