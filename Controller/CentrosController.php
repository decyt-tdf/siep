<?php

App::uses('AppController', 'Controller');

class CentrosController extends AppController {

	var $name = 'Centros';
    public $uses = array('Centro', 'Titulacion');
	var $paginate = array('Centro' => array('limit' => 4, 'order' => 'Centro.cue ASC'));

 	public function beforeFilter() {
        parent::beforeFilter();
        //Si el usuario tiene un rol de superadmin le damos acceso a todo.
        //Si no es así (se trata de un usuario "admin o usuario") tendrá acceso sólo a las acciones que les correspondan.
        if(($this->Auth->user('role') === 'superadmin') || ($this->Auth->user('role') === 'viewer')) {
	        $this->Auth->allow();
	    } elseif (($this->Auth->user('role') === 'admin') || ($this->Auth->user('role') === 'usuario')) {
	        $this->Auth->allow('index', 'view', 'autocompleteCentro', 'autocompleteSeccionDependiente');
	    }
    }

 	function index() {
		$this->Centro->recursive = -1;

		$this->paginate['Centro']['limit'] = 4;
		$this->paginate['Centro']['order'] = array('Centro.nivel' => 'ASC');
		$this->redirectToNamed();
		$conditions = array();

		if(!empty($this->params['named']['cue']))
		{
			$conditions['Centro.cue ='] = $this->params['named']['cue'];
		}

		$centros = $this->paginate('Centro', $conditions);
		$this->set(compact('centros'));

		$this->loadModel('Ciudad');
		$ciudades = $this->Ciudad->find('list', array('fields' => array('nombre')));
		$this->set('ciudades', $ciudades);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Centro no valido', 'default', array('class' => 'alert alert-danger'));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('centro', $this->Centro->read(null, $id));
		$this->loadModel('Barrio');
		$barrios = $this->Barrio->find('list', array('fields' => array('nombre')));
		$this->set('barrios', $barrios);

		$this->loadModel('Ciudad');
		$ciudades = $this->Ciudad->find('list', array('fields' => array('nombre')));
		$this->set('ciudades', $ciudades);

		$this->loadModel('Departamento');
		$departamentos = $this->Departamento->find('list', array('fields' => array('nombre')));
		$this->set('departamentos', $departamentos);
	}

	function add() {
		//abort if cancel button was pressed
        if(isset($this->params['data']['cancel'])){
                $this->Session->setFlash('Los cambios no fueron guardados. Agregación cancelada.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect( array( 'action' => 'index' ));
		}
		if (!empty($this->data)) {
			$this->Centro->create();
			if ($this->Centro->save($this->data)) {
				$this->Session->setFlash('El centro ha sido grabado', 'default', array('class' => 'alert alert-success'));
				//$this->redirect(array('action' => 'index'));
				$inserted_id = $this->Centro->id;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
				$this->Session->setFlash('El centro no fue grabado. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			  }
		}
		$empleados = $this->Centro->Empleado->find('list', array('fields'=>array('id', 'nombre_completo_empleado')));
		$this->set(compact('empleados'));

		$this->loadModel('Barrio');
		$barrios = $this->Barrio->find('list', array('fields' => array('nombre')));
		$this->set('barrios', $barrios);

		$this->loadModel('Ciudad');
		$ciudades = $this->Ciudad->find('list', array('fields' => array('nombre')));
		$this->set('ciudades', $ciudades);

		$this->loadModel('Departamento');
		$departamentos = $this->Departamento->find('list', array('fields' => array('nombre')));
		$this->set('departamentos', $departamentos);
	}


	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Centro no valido', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			//abort if cancel button was pressed
	        if(isset($this->params['data']['cancel'])){
	                $this->Session->setFlash('Los cambios no fueron guardados. Edición cancelada.', 'default', array('class' => 'alert alert-warning'));
	                $this->redirect( array( 'action' => 'index' ));
			}
			if ($this->Centro->save($this->data)) {
				$this->Session->setFlash('El centro ha sido grabado', 'default', array('class' => 'alert alert-success'));
				//$this->redirect(array('action' => 'index'));
				$inserted_id = $this->Centro->id;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
				$this->Session->setFlash('El centro no fue grabado. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Centro->read(null, $id);
		}
		$empleados = $this->Centro->Empleado->find('list', array('fields'=>array('id', 'nombre_completo_empleado')));
		$titulacions = $this->Titulacion->find('list');
		$this->set(compact('titulacions', $titulacions));
		$this->set(compact('empleados'));

		$this->loadModel('Barrio');
		$barrios = $this->Barrio->find('list', array('fields' => array('nombre')));
		$this->set('barrios', $barrios);

		$this->loadModel('Ciudad');
		$ciudades = $this->Ciudad->find('list', array('fields' => array('nombre')));
		$this->set('ciudades', $ciudades);

		$this->loadModel('Departamento');
		$departamentos = $this->Departamento->find('list', array('fields' => array('nombre')));
		$this->set('departamentos', $departamentos);
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('id no valido para centro', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Centro->delete($id)) {
			$this->Session->setFlash('El centro ha sido borrado', 'default', array('class' => 'alert alert-success'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash('El centro no fue borrado. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
		$this->redirect(array('action' => 'index'));
	}

	/*
	function imprimir($id = null) {

	    $this->idEmpty($id,'index');
		$centro = $this->Centro->read(null, $id);
	    $this->__createCentroPDF($centro);
	}
	*/

	public function listarCiudad($id) {
		if (is_numeric($id)) {

			$this->layout = 'ajax';
			$this->loadModel('Ciudad');

			$lista_Ciudad=$this->Ciudad->find('list',array('conditions' => array('departamento_id' => $id)));
			$this->set('lista_Ciudad',$lista_Ciudad);
		}
		echo json_encode($lista_Ciudad);
		$this->autoRender = false;
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

	// metodos privados.
	/*
	function __createCentroPDF($centro)
	{
		App::import(null,null,true,array(),'vendors/tcpdf/examples/example_001',false);
		Configure::write('debug',0);
        $this->layout = 'pdf'; /* esto utilizara el layout 'pdf.ctp' */
        /* Operaciones que deseamos realizar y variables que pasaremos a la vista. 
        $this->render();
	}
	*/

	public function autocompleteCentro() {
		$term = null;

		$conditions = array();
		$term = $this->request->query('term');

		// Primero obtiene el termino a buscar
		if(!empty($term))
		{
			// Si el termino es numerico, filtro por cue
			if(is_numeric($term)) {
				$conditions[] = array('cue LIKE ' => '%'.$term.'%');
			} else {
				// Se esta buscando por nombre del centro
				$terminos = explode(' ', trim($term));
				$terminos = array_diff($terminos,array(''));

				foreach($terminos as $termino) {
					$conditions[] = array('sigla LIKE' => '%' . $termino . '%');
				}
			}
		}

		$centro = $this->Centro->find('all', array(
			'recursive'	=> -1,
			'conditions' => $conditions,
			'fields' 	=> array('id', 'sigla'))
			);


		$this->RequestHandler->respondAs('json'); // Responde con el header correspondiente a json
		echo json_encode($centro);
		$this->autoRender = false;
	}

	public function autocompleteSeccionDependiente() {
		$id = $this->request->query('id');

		$this->loadModel('Curso');
		$secciones = $this->Curso->find('list', array(
			'recursive'=>-1,
			'fields'=>array('id','nombre_completo_curso'),
			'conditions'=>array(
				'centro_id'=>$id)
		));

		$this->RequestHandler->respondAs('json'); // Responde con el header correspondiente a json
		$this->autoRender = false;
		echo json_encode($secciones);
	}
}
?>
