<?php
App::uses('AppController', 'Controller');

class CursosController extends AppController {

	var $name = 'Cursos';
    public $uses = array('Curso', 'Inscripcion');
	var $paginate = array('Curso' => array(
		'contain' => array('Centro'),
		'limit' => 6,
		'order' => 'Curso.anio ASC'));

	public function beforeFilter() {
        parent::beforeFilter();
        /* ACCESOS SEGÚN ROLES DE USUARIOS (INICIO).
        *Si el usuario tiene un rol de superadmin le damos acceso a todo. Si no es así (se trata de un usuario "admin o usuario") tendrá acceso sólo a las acciones que les correspondan.
        */
        if ($this->Auth->user('role') === 'superadmin') {
	        $this->Auth->allow();
	    } elseif (($this->Auth->user('role') === 'usuario') || ($this->Auth->user('role') === 'admin')) { 
	        $this->Auth->allow('index', 'view');
	    } elseif (($this->Auth->user('role') === 'viewer') && ($this->Auth->user('puesto') === 'Unidad de Estadística Educativa')) {
			$this->Auth->allow('index', 'edit', 'delete', 'activate');
	    }
	    /* FIN */ 
    } 

	function index() {
		$this->Curso->recursive = 0;
		$this->paginate['Curso']['limit'] = 6;
		$this->paginate['Curso']['order'] = array('Curso.anio' => 'ASC');
		/* PAGINACIÓN SEGÚN ROLES DE USUARIOS (INICIO).
		*  Sí el usuario es "admin" muestra los cursos activos del establecimiento. 
		*  Sino sí es "usuario" externo muestra los cursos activos del nivel.
		*/ 
		$userRole = $this->Auth->user('role');
		$userCentroId = $this->getUserCentroId();
		$nivelCentroArray = $this->Curso->Centro->findById($userCentroId, 'nivel_servicio');
		$this->loadModel('Centro');
		$this->Centro->recursive = 0;
        $this->Centro->Behaviors->load('Containable');
		$nivelCentro = $nivelCentroArray['Centro']['nivel_servicio'];
		$this->Curso->Centro->recursive = 0;
		$nivelCentroId = $this->Curso->Centro->find('list', array('fields'=>array('id'), 'contain'=>false,'conditions'=>array('nivel_servicio'=>$nivelCentro)));
		if ($userRole === 'admin') {
			$this->paginate['Curso']['conditions'] = array('Curso.centro_id' => $userCentroId, 'Curso.status' => 1);
		} else if (($userRole === 'usuario') && ($nivelCentro === 'Común - Inicial - Primario')) {
			$nivelCentroId = $this->Curso->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>array('Común - Inicial', 'Común - Primario')))); 		
			$this->paginate['Curso']['conditions'] = array('Curso.centro_id' => $nivelCentroId, 'Curso.status' => 1);
		} else if ($userRole === 'usuario') {
			$nivelCentroId = $this->Curso->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>$nivelCentro))); 		
			$this->paginate['Curso']['conditions'] = array('Curso.centro_id' => $nivelCentroId, 'Curso.status' => 1);
		}
		/* FIN */
		/* PAGINACIÓN SEGÚN CRITERIOS DE BÚSQUEDAS (INICIO).
		*Pagina según búsquedas simultáneas ya sea por CENTRO y/o ANIO y/o DIVISIÓN y/o TURNO.
		*/
		$this->redirectToNamed();
		$conditions = array();
		if (!empty($this->params['named']['centro_id'])) {
			$conditions['Curso.centro_id ='] = $this->params['named']['centro_id'];
		}
		if (!empty($this->params['named']['anio'])) {
			$conditions['Curso.anio ='] = $this->params['named']['anio'];
		}
		if (!empty($this->params['named']['division'])) {
			$conditions['Curso.division ='] = $this->params['named']['division'];
		}
		if (!empty($this->params['named']['tipo'])) {
			$conditions['Curso.tipo ='] = $this->params['named']['tipo'];
		}
		if (!empty($this->params['named']['turno'])) {
			$conditions['Curso.turno ='] = $this->params['named']['turno'];
		}
		$cursos = $this->paginate('Curso',$conditions);
	    /* FIN */
	    /*
		 * *********************
		 * 		IMPORTANTE
		 * *********************
		 * Falta filtrar estos combobox segun el tipo de usuario logueado
		 *
		 */
		$comboAnio = $this->Curso->find('list', array(
			'contain'=>false,
			'fields'=> array('Curso.anio','Curso.anio')
		));
		$comboDivision = $this->Curso->find('list', array(
			'contain'=>false,
			'fields'=> array('Curso.division','Curso.division'),
			'conditions'=>array('centro_id'=>$userCentroId))
		);
		/* SETS DE DATOS PARA COMBOBOXS DEL FORM SEARCH (INICIO). 
        *  Sí es 'superadmin' el combobox de centros trae todas las instituciones.
        *  Sino sí es 'usuario' trae sólo los centros correspondientes al nivel. 
		*/
		if ($userRole === 'superadmin') {
			$centros= $this->Curso->Centro->find('list', array('fields'=>array('sigla'), 'contain'=>false));
		} else if (($userRole === 'usuario') && ($nivelCentro === 'Común - Inicial - Primario')) {
			$nivelCentroId = $this->Curso->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>array('Común - Inicial', 'Común - Primario')))); 		
			$centros = $this->Curso->Centro->find('list', array('fields'=>array('sigla'), 'contain'=>false, 'conditions'=>array('id'=>$nivelCentroId)));
		} else if ($userRole === 'usuario') {
			$centros = $this->Curso->Centro->find('list', array('fields'=>array('sigla'), 'contain'=>false, 'conditions'=>array('id'=>$nivelCentroId)));
		} else if ($userRole === 'admin') {
			$centros = $this->Curso->Centro->find('list', array('fields'=>array('sigla'), 'contain'=>false, 'conditions'=>array('id'=>$userCentroId)));
		}
		/* FIN */
		$this->set(compact('cursos', 'centros', 'comboAnio','comboDivision','comboSecciones'));
	}

	function view($id = null) {
		$this->Curso->recursive = 0;
		if (!$id) {
			$this->Session->setFlash('Curso no valido.', 'default', array('class' => 'alert alert-danger'));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('curso', $this->Curso->read(null, $id));
		/* OBTIENE EL CICLO ACTUAL */
		$this->loadModel('Ciclo');
		$this->Ciclo->recursive = 0;
        $this->Ciclo->Behaviors->load('Containable');
        $cicloIdActual = $this->getActualCicloId();
        $cicloIdActualArray = $this->Ciclo->findById($cicloIdActual, 'id');
        $cicloIdActualString = $cicloIdActualArray['Ciclo']['id'];

		/* FIN */
		/* GENERA NOMBRES PARA DATOS RELACIONADOS. (INICIO) */
/*		$this->loadModel('Persona');
		$this->Persona->recursive = 0;
        $this->Persona->Behaviors->load('Containable');
		$this->Curso->Inscripcion->Alumno->recursive = 1;*/
/*		$personaId = $this->Curso->Inscripcion->Alumno->find('list', array(
			'fields'=>array('persona_id'),
			'contain'=>false));
		$personaNombre = $this->Persona->find('list', array(
			'fields'=>array('nombre_completo_persona'),
			'contain'=>false));*/
		/* FIN */
		/* OBTIENE NÚMERO DE MATRICULADOS. (INICIO) */
		$cursoId = $this->Curso->id;
        // Obtiene el valor de plazas
        $cursoPlazasArray = $this->Curso->findById($cursoId, 'plazas');
		$cursoPlazasString = $cursoPlazasArray['Curso']['plazas'];
        // Obtiene el valor de matrícula
        $cursoMatriculaArray = $this->Curso->findById($cursoId, 'matricula');
		$cursoMatriculaString = $cursoMatriculaArray['Curso']['matricula'];
		$vacantes = $cursoPlazasString - $cursoMatriculaString;
		/* FIN */
		/* MUESTRA UNIDADES CURRICULARES RELACIONADAS DEPENDIENDO EL NIVEL (INICIO). 
		* Sólo muestra materias para los niveles secundario y superior.
		*/		
		$userCentroId = $this->getUserCentroId();
        $userCentroNivel = $this->getUserCentroNivel($userCentroId);
		/* FIN */
		$this->set(compact('inscripciones','cicloNombre', 'userCentroNivel', 'vacantes', 'cursoPlazasString', 'cursoMatriculaString', 'cicloIdActualString'));
	}

	function add() {
		$this->Curso->recursive = 0;
		//abort if cancel button was pressed  
        if(isset($this->params['data']['cancel'])){
                $this->Session->setFlash('Los cambios no fueron guardados. Agregación cancelada.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect( array( 'action' => 'index' ));
		}
		if (!empty($this->data)) {
			$this->Curso->create();
			/*
			//Antes de guardar sí el nivel_servicio del centro es INICIAL o PRIMARIO, obtiene la titulación.
            $centroId = $this->request->data['Curso']['centro_id'];
            $this->loadModel('Centro');
            $this->Centro->recursive = 0;
        	$this->Centro->Behaviors->load('Containable');
            $centroNivel = $this->Centro->find('list', array('fields'=>array('nivel_servicio'), 'contain'=>false, 'conditions'=>array('id'=>$centroId)));
			if ($centroNivel == 'INICIAL') {
				$this->request->data['Curso']['titulacion_id'] = 9;
			} else if ($centroNivel == 'PRIMARIA') {
				$this->request->data['Curso']['titulacion_id'] = 10;
			}
			*/
			// Antes de guardar obtiene los valores de matrícula y vacantes.
			$plazas = $this->request->data['Curso']['plazas'];
			$vacantes = $plazas;
			$this->request->data['Curso']['vacantes'] = $vacantes;
			// Antes de guardar activa el curso creado.
			$this->request->data['Curso']['status'] = 1;
			if ($this->Curso->save($this->data)) {
				$this->Session->setFlash('La sección ha sido grabada.', 'default', array('class' => 'alert alert-success'));
				$inserted_id = $this->Curso->id;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
				$this->Session->setFlash('La sección no fué grabada. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			}
		}
		$this->Curso->Titulacion->recursive = 0;
		$titulacions = $this->Curso->Titulacion->find('list');
		$this->Curso->Materia->recursive = 0;
		$materias = $this->Curso->Materia->find('list');
		$this->Curso->Centro->recursive = 0;
		$centros = $this->Curso->Centro->find('list');
		$this->Inscripcion->recursive = 0;
		$inscripcions = $this->Inscripcion->find('list');
		$this->set(compact('titulacions', 'materias', 'ciclos', 'inscripcions', $inscripcions, 'centros'));
	}

	function edit($id = null) {
		$this->Curso->recursive = 0;
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Sección no valida.', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
		  	//abort if cancel button was pressed  
          	if(isset($this->params['data']['cancel'])){
               $this->Session->setFlash('Los cambios no fueron guardados. Edición cancelada.', 'default', array('class' => 'alert alert-warning'));
               $this->redirect( array( 'action' => 'index' ));
		  	}
			/* Sí cambia las plazas:
            *  Actualiza valores de matrícula y vacantes del curso.
            */
            // Obtiene la plaza seleccionada.
            $cursoPlazaStringSeleccionada = $this->request->data['Curso']['plazas'];
            // Obtiene las plazas del curso asignado anteriormente.
            $cursoPlazaArrayAnterior = $this->Curso->findById($id, 'plazas');                
            $cursoPlazaStringAnterior = $cursoPlazaArrayAnterior['Curso']['plazas'];
            // Sí las plazas de los cursos son diferentes, se trata de un cambio de plazas. 
            if ($cursoPlazaStringSeleccionada != $cursoPlazaStringAnterior) { 
               // Actualiza los valores de matrícula y vacantes de la sección.
               $this->Curso->CursosInscripcion->recursive = 0;
               $matriculaActual = $this->Curso->CursosInscripcion->find('count', array('fields'=>array('curso_id'), 'contain'=>false, 'conditions'=>array('CursosInscripcion.curso_id'=>$id)));
               $this->Curso->id=$id;
               $this->Curso->saveField("matricula", $matriculaActual);
               $vacantesActual = $cursoPlazaStringSeleccionada - $matriculaActual;
               $this->Curso->saveField("vacantes", $vacantesActual);
            }
            /* FIN */
		  	if ($this->Curso->save($this->data)) {
				$this->Session->setFlash('La sección ha sido grabada.', 'default', array('class' => 'alert alert-success'));
				//$this->redirect(array('action' => 'index'));
				$inserted_id = $this->Curso->id;
				$userPuesto = $this->Auth->user('puesto');
				if ($userPuesto = 'Unidad de Estadística Educativa'): $this->redirect(array('action' => 'index'));
				endif;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
				$this->Session->setFlash('La sección no fue grabada. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Curso->read(null, $id);
		}
		$this->Curso->Centro->recursive = 0;
		$centros = $this->Curso->Centro->find('list');
		$this->Curso->Titulacion->recursive = 0;
		$titulacions = $this->Curso->Titulacion->find('list');
		$this->loadModel('Ciclo');
		$this->Ciclo->recursive = 0;
        $this->Ciclo->Behaviors->load('Containable');
		$ciclos = $this->Ciclo->find('list');
		$this->Inscripcion->recursive = 0;
		$inscripcions = $this->Inscripcion->find('list');
		$this->set(compact('centros', 'titulacions', 'modalidads', 'ciclos', 'inscripcions', $inscripcions));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Id no valido para el curso', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action'=>'index'));
		}
        $this->Curso->id = $id;
        if (!$this->Curso->exists()) {
            $this->Session->setFlash('ID inválido');
			$this->redirect(array('action'=>'index'));
        }
        if ($this->Curso->saveField('status', 0)) {
 			$this->Session->setFlash('El curso ha sido desactivado', 'default', array('class' => 'alert alert-success'));
            $this->redirect(array('action' => 'index'));
        }
		$this->Session->setFlash('El curso no fue desactivado', 'default', array('class' => 'alert alert-danger'));
        $this->redirect(array('action' => 'index'));
	}

	public function activate($id = null) {
		if (!$id) {
			$this->Session->setFlash('Id no valido para el curso', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action'=>'index'));
		}		
        $this->Curso->id = $id;
        if (!$this->Curso->exists()) {
            $this->Session->setFlash('ID inválido');
			$this->redirect(array('action'=>'index'));
        }
        if ($this->Curso->saveField('status', 1)) {
			$this->Session->setFlash('El curso ha sido reactivado', 'default', array('class' => 'alert alert-success'));
            $this->redirect(array('action' => 'index'));
        }
		$this->Session->setFlash('El curso no pudo ser reactivado', 'default', array('class' => 'alert alert-danger'));
        $this->redirect(array('action' => 'index'));
    }
}
?>