<?php

App::uses('AppController', 'Controller');

class FamiliarsController extends AppController {

	var $name = 'Familiars';
	var $paginate = array('Familiar' => array('limit' => 3, 'order' => 'Familiar.id DESC'));

	function beforeFilter(){
	    parent::beforeFilter();
		/* ACCESOS SEGÚN ROLES DE USUARIOS (INICIO).
        *Si el usuario tiene un rol de superadmin le damos acceso a todo. Si no es así (se trata de un usuario "admin o usuario") tendrá acceso sólo a las acciones que les correspondan.
        */
        switch($this->Auth->user('role'))
		{
			case 'superadmin':
				if ($this->Auth->user('puesto') === 'Sistemas') {
					$this->Auth->allow();				
				} else {
					//En caso de ser ATEI
					$this->Auth->allow('view');	
				}
				break;
			case 'usuario':
			case 'admin':
				$this->Auth->allow('add', 'view', 'edit', 'autocompleteNombrePersona', 'autocompleteNombreAlumno');
				break;

			default:
				$this->Session->setFlash('No tiene permisos.', 'default', array('class' => 'alert alert-warning'));
				$this->redirect($this->referer());
				break;
		}
		/* FIN */
    }

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Familiar no valido', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('controller' => 'personas', 'action' => 'index'));
		}
		$this->set('familiar', $this->Familiar->read(null, $id));
		/* DATOS DE PERSONA DEL FAMILIAR.(INICIO) */
		//Obtención del ID de persona.
		$personaIdArray = $this->Familiar->findById($id, 'persona_id');
		$personaId = $personaIdArray['Familiar']['persona_id'];
		$this->loadModel('Persona');
        $this->Persona->recursive = 0;
        $this->Persona->Behaviors->load('Containable');
        //Obtención del nombre completo. 
        $familiarNombreArray = $this->Persona->findById($personaId, 'nombre_completo_persona');
        $familiarNombre = $familiarNombreArray['Persona']['nombre_completo_persona'];
        //Obtención de la nacionalidad. 
        $familiarNacionalidadArray = $this->Persona->findById($personaId, 'nacionalidad');
        $familiarNacionalidad = $familiarNacionalidadArray['Persona']['nacionalidad'];
        //Obtención del CUIL. 
        $familiarDNIArray = $this->Persona->findById($personaId, 'documento_nro');
        $familiarDNI = $familiarDNIArray['Persona']['documento_nro'];
        //Obtención de la ocupación. 
        $familiarOcupacionArray = $this->Persona->findById($personaId, 'ocupacion');
        $familiarOcupacion = $familiarOcupacionArray['Persona']['ocupacion'];
        //Obtención del lugar de trabajo. 
        $familiarLugarTrabajaArray = $this->Persona->findById($personaId, 'lugar_de_trabajo');
        $familiarLugarTrabaja = $familiarLugarTrabajaArray['Persona']['lugar_de_trabajo'];
		//Obtención del domicilio. 
        $familiarCiudadIdArray = $this->Persona->findById($personaId, 'ciudad_id');
        $familiarCiudadId = $familiarCiudadIdArray['Persona']['ciudad_id'];
        $this->loadModel('Ciudad');
        $this->Ciudad->recursive = 0;
        $this->Ciudad->Behaviors->load('Containable');
        if ($familiarCiudadId) {
        	$ciudadNombreArray = $this->Ciudad->findById($familiarCiudadId, 'nombre');
        	$ciudadNombre = $ciudadNombreArray['Ciudad']['nombre'];
        } else {
        	$ciudadNombre = '';
		}
		$familiarBarrioIdArray = $this->Persona->findById($personaId, 'barrio_id');
        $familiarBarrioId = $familiarBarrioIdArray['Persona']['barrio_id'];
		$this->loadModel('Barrio');
        $this->Barrio->recursive = 0;
        $this->Barrio->Behaviors->load('Containable');
        if ($familiarBarrioId) {
        	$barrioNombreArray = $this->Barrio->findById($familiarBarrioId, 'nombre');
        	$barrioNombre = $barrioNombreArray['Barrio']['nombre'];
        } else {
        	$barrioNombre = '';
        }
        $familiarCalleNombreArray = $this->Persona->findById($personaId, 'calle_nombre');
        $familiarCalleNombre = $familiarCalleNombreArray['Persona']['calle_nombre'];
        $familiarCalleNumeroArray = $this->Persona->findById($personaId, 'calle_nro');
		$familiarCalleNumero = $familiarCalleNumeroArray['Persona']['calle_nro'];
		$familiarDeptoCasaArray = $this->Persona->findById($personaId, 'depto_casa');
		$familiarDeptoCasa = $familiarDeptoCasaArray['Persona']['depto_casa'];
		$familiarTiraEdificioArray = $this->Persona->findById($personaId, 'tira_edificio');
        $familiarTiraEdificio = $familiarTiraEdificioArray['Persona']['tira_edificio'];
		$familiarTelefonoArray = $this->Persona->findById($personaId, 'telefono_nro');
        $familiarTelefono = $familiarTelefonoArray['Persona']['telefono_nro'];
        $familiarEmailArray = $this->Persona->findById($personaId, 'email');
        $familiarEmail = $familiarEmailArray['Persona']['email'];
        /* FIN */
        /* SETS DE DATOS PARA ALUMNOS RELACIONADOS (INICIO). */
        //Obtención del ID de persona del alumno del familiar.
        /*
        $alumnoId = $this->Familiar->AlumnosFamiliar->findById($id, 'alumno_id');
   		$alumnoPersonaId = $this->Persona->findById($alumnoId, 'persona_id');
        //Obtención de las denominaciones de los datos de persona.
        $alumnoDocumentoTipo = $this->Persona->find('list', array('fields'=>array('id', 'documento_tipo')));        
        $alumnoDocumentoNro = $this->Persona->find('list', array('fields'=>array('id', 'documento_nro')));
        /* FIN */
        //Obtención de otros datos.
        $familiarConvivienteArray = $this->Familiar->findById($id, 'conviviente');
        $familiarConviviente = $familiarConvivienteArray['Familiar']['conviviente']; 
        $familiarConvivienteRta = ($familiarConviviente == 1) ? 'SI' : 'NO';
        $familiarAutorizadoRetirarArray = $this->Familiar->findById($id, 'autorizado_retirar');
        $familiarAutorizadoRetirar = $familiarAutorizadoRetirarArray['Familiar']['autorizado_retirar'];
        $familiarAutorizadoRetirarRta = ($familiarAutorizadoRetirar == 1) ? 'SI' : 'NO';
        //Obtención de clave para permitir editar sólo a los usuarios "admin" del mismo centro al alumno relacionado o a los "superadmin" o "usuarios".
        $familiarAlumnoId = $this->Familiar->AlumnosFamiliar->find('list', array('fields'=>array('alumno_id'), 'conditions'=>array('familiar_id'=>$id)));
        $this->loadModel('Alumno');
        $this->Alumno->recursive = 0;
        $this->Alumno->Behaviors->load('Containable');
        $AlumnoCentroIds = $this->Alumno->find('list', array('fields'=>array('centro_id'), 'containable'=>true, 'conditions'=>array('id'=>$familiarAlumnoId)));
        $userCentroId = $this->getUserCentroId();
        $clave = array_search($userCentroId, $AlumnoCentroIds);
	    //Envío de datos a la vista.        
        $this->set(compact('familiarNombre', 'familiarNacionalidad', 'familiarDNI', 'familiarOcupacion', 'familiarLugarTrabaja', 'ciudadNombre', 'familiarCalleNombre', 'familiarCalleNumero', 'familiarTelefono', 'familiarEmail', 'familiarConvivienteRta', 'familiarAutorizadoRetirarRta'/*, 'alumnoPersonaId', 'alumnoDocumentoTipo', 'alumnoDocumentoNro'*/, 'clave', 'familiarDeptoCasa', 'familiarTiraEdificio', 'barrioNombre'));
    }

	function add() {
		  //abort if cancel button was pressed  
          if(isset($this->params['data']['cancel'])){
                $this->Session->setFlash('Los cambios no fueron guardados. Agregación cancelada.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect(array('controller' => 'personas', 'action' => 'index'));
		  }
   		  if (!empty($this->data)) {
			$this->Familiar->create();
			/* INICIO: VERIFICACION DE DEFINICIÓN DEL FAMILIAR */
			//Obtengo personaId
            $personaId = $this->request->data['Persona']['persona_id'];
            //Si no se definió la persona, vuelve al formulario anterior.
            if (empty($personaId)) {
                $this->Session->setFlash('No se definio el familiar.', 'default', array('class' => 'alert alert-danger'));
                $this->redirect($this->referer());
            }
            // Propone guardar el id de persona en el campo persona_id. 
            $this->request->data['Familiar']['persona_id'] = $personaId;
            /* FIN */
            /* INICIO: VERIFICACION DE DEFINICIÓN DEL ALUMNO */
			//Obtengo el ID de persona del alumno.
            $alumnoPersonaId = $this->request->data['Persona']['alumno_id'];
            //Si no se definió el alumno, vuelve al formulario anterior.
            if (empty($alumnoPersonaId)) {
                $this->Session->setFlash('No se definio el alumno.', 'default', array('class' => 'alert alert-danger'));
                $this->redirect($this->referer());
            }
            /* FIN */
            //Obtención del id del alumno del centro desde el id de persona.
            $this->loadModel('Alumno');
            $this->Alumno->Behaviors->load('Containable');
            $this->Alumno->recursive = 0;
            $userCentroId = $this->getUserCentroId();
            //Obtención de id del alumno del centro que vincula al familiar.
            $alumnoIdArray = $this->Alumno->findByPersonaIdAndCentroId($alumnoPersonaId, $userCentroId, 'id');
			$alumnoId = $alumnoIdArray['Alumno']['id'];
			//Propone guardar el id de alumno.
			$this->request->data['Alumno']['alumno_id'] = $alumnoId;
			/* INICIO: Verifica que el familiar ya esté vinculado al alumno. */
            //Obtención del/los id Familiar de la Persona.
            $verificaFamiliarIdPersonaArray = $this->Familiar->findByPersonaId($personaId, 'id');
            //Si la Persona está asociada a id de familiar.
            if ($verificaFamiliarIdPersonaArray) {
            	$verificaFamiliarIdPersona = $verificaFamiliarIdPersonaArray['Familiar']['id'];
            	//Obtención del id del alumno.
            	$AlumnoIdObtenidoArray = $this->Familiar->AlumnosFamiliar->findByFamiliarId($verificaFamiliarIdPersona, 'alumno_id');
            	$AlumnoIdObtenido = $AlumnoIdObtenidoArray['AlumnosFamiliar']['alumno_id'];
            	if ($AlumnoIdObtenido == $alumnoId) {
            		$this->Session->setFlash('El alumno ya está vinculado al familiar señalado.', 'default', array('class' => 'alert alert-danger'));
                        $this->redirect($this->referer());
            	}
            }
            /* FIN */
            if ($this->Familiar->save($this->data)) {
				$this->Session->setFlash('El familiar ha sido grabado', 'default', array('class' => 'alert alert-success'));
				$inserted_id = $this->Familiar->id;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
				$this->Session->setFlash('El familiar no fue grabado. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			}
		}
		$centroId = $this->getUserCentroId();
		$alumnoPersonaId = $this->Familiar->Alumno->find('list', array('fields'=>array('persona_id'), 'conditions'=>array('centro_id'=>$centroId)));
        $this->loadModel('Persona');
        $alumnosNombre = $this->Persona->find('list', array('fields'=>array('id', 'nombre_completo_persona'), 'conditions' => array('id' => $alumnoPersonaId)));
        $this->set(compact('alumnosNombre'));
    }

	function edit($id = null) {
		$this->Familiar->recursive = 1;
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Familiar no valido', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('controller' => 'personas', 'action' => 'index'));
		}
		if (!empty($this->data)) {
		  	//abort if cancel button was pressed  
          	if(isset($this->params['data']['cancel'])){
                $this->Session->setFlash('Los cambios no fueron guardados. Edición cancelada.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect( array('controller' => 'familiars', 'action' => 'view', $id));
		  	}
		  	//Obtengo personaId
            $personaId = $this->request->data['Persona']['persona_id'];
            /*
            //Si no se definió la persona, vuelve al formulario anterior.
            if (empty($personaId)) {
                $this->Session->setFlash('No se definio el familiar.', 'default', array('class' => 'alert alert-danger'));
                $this->redirect($this->referer());
            }
            */
            //Si no se redefinió la persona, obtiene el registrado en base de datos.
            if (empty($personaId)) {
            	$personaIdArray = $this->Familiar->findById($id, 'persona_id');    
            	$personaId = $personaIdArray['Familiar']['persona_id'];
            	$flag = 1;
            }
            // Propone guardar el id de persona en el campo persona_id. 
            $this->request->data['Familiar']['persona_id'] = $personaId;
		  	/* Verifica que el familiar ya esté vinculado al alumno. (INICIO) */
            //Obtención del/los id Familiar de la Persona.
            $verificaFamiliarIdPersonaArray = $this->Familiar->findByPersonaId($personaId, 'id');
            //Si la Persona está asociada a id de familiar.
            if ($verificaFamiliarIdPersonaArray && $flag != 1) {
            	$verificaFamiliarIdPersona = $verificaFamiliarIdPersonaArray['Familiar']['id'];
            	//Obtención del id del alumno.
            	$AlumnoIdObtenidoArray = $this->Familiar->AlumnosFamiliar->findByFamiliarId($verificaFamiliarIdPersona, 'alumno_id');
            	$AlumnoIdObtenido = $AlumnoIdObtenidoArray['AlumnosFamiliar']['alumno_id'];
            	//Obtención del ID del alumno.
				$alumnoIdArray = $this->Familiar->AlumnosFamiliar->findByFamiliarId($id, 'alumno_id');
				$alumnoId = $alumnoIdArray['AlumnosFamiliar']['alumno_id'];
            	if ($AlumnoIdObtenido == $alumnoId) {
            		$this->Session->setFlash('El alumno ya está vinculado al familiar señalado.', 'default', array('class' => 'alert alert-danger'));
                        $this->redirect($this->referer());
            	}
            }
            /* FIN */
		  	if ($this->Familiar->save($this->data)) {
				$this->Session->setFlash('El familiar ha sido grabado', 'default', array('class' => 'alert alert-success'));
				//$this->redirect($this->referer());
				//$this->redirect(array('controller' => 'alumnos','action' => 'index'));
				$inserted_id = $this->Familiar->id;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
				$this->Session->setFlash('El familiar no fue grabado. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Familiar->read(null, $id);
		}
		//Obtención del nombre del familiar.
		$familiarPersonaIdArray = $this->Familiar->findById($id, 'persona_id');
		$familiarPersonaId = $familiarPersonaIdArray['Familiar']['persona_id'];
		$this->loadModel('Persona');
        $this->Persona->recursive = 0;
        $this->Persona->Behaviors->load('Containable');
        $familiarPersonaNombreArray = $this->Persona->findById($familiarPersonaId, 'nombre_completo_persona');
        $familiarPersonaNombre = $familiarPersonaNombreArray['Persona']['nombre_completo_persona'];
		//Obtención del ID del alumno.
		$alumnoIdArray = $this->Familiar->AlumnosFamiliar->findByFamiliarId($id, 'alumno_id');
		$alumnoId = $alumnoIdArray['AlumnosFamiliar']['alumno_id'];
		//Obtención del nombre de la persona.
		$this->loadModel('Alumno');
        $this->Alumno->recursive = 0;
        $this->Alumno->Behaviors->load('Containable');
        $alumnoPersonaIdArray = $this->Alumno->findById($alumnoId, 'persona_id');
		$alumnoPersonaId = $alumnoPersonaIdArray['Alumno']['persona_id'];
		$alumnoPersonaNombreArray = $this->Persona->findById($alumnoPersonaId, 'nombre_completo_persona');
		$alumnoPersonaNombre = $alumnoPersonaNombreArray['Persona']['nombre_completo_persona'];
	    $this->set(compact('familiarPersonaId','familiarPersonaNombre', 'alumnoPersonaNombre', 'alumnoId'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Id no valido para familiar', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('controller' => 'personas', 'action' => 'index'));
		}
		if ($this->Familiar->delete($id)) {
			$this->Session->setFlash('El Familiar ha sido borrado', 'default', array('class' => 'alert alert-success'));
			$this->redirect(array('controller' => 'personas', 'action' => 'index'));
		}
		$this->Session->setFlash('Familiar no fue borrado', 'default', array('class' => 'alert alert-danger'));
		$this->redirect(array('controller' => 'personas', 'action' => 'index'));
	}

	/* AUTOCOMPLETE PARA EL FORMULARIO DE AGREGACIÓN (INICIO).
	*  Sólo muestra las personas con perfíl de familiar.
	*/
	public function autocompleteNombrePersona() {

		$conditions = array();
		$term = $this->request->query('term');

		if(!empty($term))
		{
			// Si se busca un numero de documento.. se raliza el siguiente filtro
			if(is_numeric($term)) {
				$conditions[] = array('Persona.documento_nro LIKE' => $term . '%');
			} else {
				// Se esta buscando por nombre y/o apellidos
				$terminos = explode(' ', trim($term));
				$terminos = array_diff($terminos,array(''));

				// Esto es posible porque nombre_completo_persona esta definido en el modelo como virtual
				foreach($terminos as $termino) {
					$conditions[] = array('nombre_completo_persona LIKE' => '%' . $termino . '%');
				}
			}

			$this->loadModel('Persona');
			$personaId = $this->Persona->find('list', array('fields'=>array('id'), 'conditions'=>array('familiar'=>1)));
			$personas = $this->Persona->find('all', array(
					'recursive'	=> -1,
					// Condiciona la búsqueda también por id de persona con perfil de alumno.
					'conditions' => array($conditions, 'id' => $personaId),
					'fields' 	=> array('id', 'nombre_completo_persona','documento_nro'))
			);

			echo json_encode($personas);
		}

		$this->autoRender = false;
	}
}
?>
