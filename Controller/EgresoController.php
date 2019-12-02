<?php
App::uses('AppController', 'Controller');

class EgresoController extends AppController {

	public $paginate = array('CursosInscripcion' => array('limit' => 2, 'order' => 'CursosInscripcion.curso_id ASC'));

	// Permite agregar el Helper de Siep a las vistas
	public $helpers = array('Siep');

	public function beforeFilter() {
        parent::beforeFilter();
		/* ACCESOS SEGÚN ROLES DE USUARIOS (INICIO).
        *Si el usuario tiene un rol de superadmin le damos acceso a todo. Si no es así (se trata de un usuario "admin o usuario") tendrá acceso sólo a las acciones que les correspondan.
        */
		switch($this->Auth->user('role'))
		{
			case 'superadmin':
				$this->Auth->allow();
				break;
			case 'admin':
				$this->Auth->allow('index','confirmarAlumnos');
				break;
			case 'usuario':
				$this->Auth->allow('index','confirmarAlumnos');
				break;

			default:
			$this->Session->setFlash('No tiene permisos.', 'default', array('class' => 'alert alert-warning'));
			$this->redirect($this->referer());
			break;
		}

		// Importa el Helper de Siep al controlador es accesible mediante $this->Siep
		App::import('Helper', 'Siep');
		$this->Siep= new SiepHelper(new View());

		App::uses('HttpSocket', 'Network/Http');
    } 

/**
 * index method
 *
 * @return void
 */
	public function index()
	{
		// Datos del usuario
		//$userCentroId = $this->getUserCentroId();
		//$userRole = $this->Auth->user('role');
		//$hoyArray = getdate();

		if(!$this->params['named']['centro_id'] || !$this->params['named']['curso_id'] ) {
			$this->Session->setFlash('ERROR: No fue posible determinar la seccion', 'default', array('class' => 'alert alert-danger'));
			$this->redirect(array('controller'=>'cursos','action' => 'index'));
		}

		$serverDate = getdate();
		$cicloActual = $serverDate['year'];
		$cicloEgreso = $cicloActual; // Solo en febrero el cicloActual no es igual al cicloEgreso

		if(isset($this->params['named']['ciclo'])) {
			$cicloActual = $this->params['named']['ciclo'];
			$cicloEgreso = $cicloActual;
		}

		// Parametros para ejecutar API
		$apiParams = [];
		$apiParams['ciclo'] = $cicloActual;
		$apiParams['centro_id'] = $this->params['named']['centro_id'];
		$apiParams['curso_id'] = $this->params['named']['curso_id'];
		//$apiParams['estado_inscripcion'] = 'CONFIRMADA';
		$apiParams['por_pagina'] = 'all';

		// Consumo de API
		$cursosInscripcions = $this->Siep->consumeApi("api/v1/inscripcion/lista",$apiParams);
		if(isset($cursosInscripcions['error']))
		{
			$this->Session->setFlash('ERROR API(Inscripciones): '.$cursosInscripcions['error'], 'default', array('class' => 'alert alert-danger'));
			$this->redirect(array('action' => 'index'));
		}

		$cursosInscripcions = $cursosInscripcions['data'];

		// Obtenemos Ciclo, Centro y Curso de la primera Inscripcion de la lista
		// Todas las inscripciones comparten estas 3 variables
		$first = reset($cursosInscripcions);
		$curso = $first['curso'];
		$centro = $first['inscripcion']['centro'];
		$ciclo = $first['inscripcion']['ciclo'];

		$this->set(compact('cursosInscripcions','ciclo','cicloActual','cicloEgreso','centro','curso'));
	}

	public function confirmarAlumnos()
	{
		$userId = $this->Auth->user('id');
		$request = $this->request->data;

		if(!$request)
		{
			$this->Session->setFlash("ERROR Egresos::confirmarAlumnos(): No fue posible realizar el egreso, los datos estan incompletos, vuelva a intentarlo", 'default', array('class' => 'alert alert-danger'));
			$this->redirect($this->referer());
		}

		try {
			// Parametros para ejecutar API
			$apiParams = [];
			$apiParams['user_id'] = $userId;
			$apiParams['centro_id'] = $request['centro_id'];
			$apiParams['curso_id'] = $request['curso_id'];
			$apiParams['id'] = $request['id'];

			// Consumo de API
			$apiResponse = $this->Siep->consumeApi("api/inscripcion/egreso",$apiParams,"POST");

			if(isset($apiResponse ['error']))
			{
				// El api puede devolver mas de 1 error, hay que mostrarlos a todos
				$err = $apiResponse['error'];
				$msgError = "";
				foreach ($err as $errParam) {
					$msgError .= $errParam."<br>";
				}
				$this->Session->setFlash("Error API(Egreso) Error: ".$msgError, 'default', array('class' => 'alert alert-danger'));
				$this->redirect($this->referer());
			} else {
				if( isset($apiResponse['success']) && count($apiResponse['success'])>0 ) {
					$this->Session->setFlash("Egreso realizado con exito", 'default', array('class' => 'alert alert-success'));
					$this->redirect($this->referer());
				} else {
					$this->Session->setFlash("Error API(Egreso) !done: No se determinó si la operación se efectuo con exito", 'default', array('class' => 'alert alert-warning'));
					$this->redirect($this->referer());
				}
			}
		} catch(\Exception $ex){
			$this->Session->setFlash("Error API(Egreso) TryError: ".$ex->getMessage(), 'default', array('class' => 'alert alert-danger'));
			$this->redirect($this->referer());
		}
	}
}