<?php
App::uses('AppController', 'Controller');

class ListaAlumnosController extends AppController {

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
			case 'usuario':
				$this->Auth->allow('index');
				break;
			case 'admin':
				$this->Auth->allow('index','updateFamiliar');
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

	public function index()
	{
		if(!$this->params['named']['centro_id'] || !$this->params['named']['curso_id'] ) {
			$this->Session->setFlash('ERROR: No fue posible determinar la seccion', 'default', array('class' => 'alert alert-danger'));
			$this->redirect(array('controller'=>'cursos','action' => 'index'));
		}

		// Visualizar lista de inscripciones del ciclo solicitado
		$hoyArray = getdate();
		$nombreCicloActual = $hoyArray['year'];
		$cicloActual = $nombreCicloActual;

		$cicloDatoAlumno = $cicloActual;
		if($this->params['named']['ciclo']) {
			$cicloDatoAlumno = $this->params['named']['ciclo'];
		}

		// Parametros para ejecutar API
		$apiParams = [];
		$apiParams['ciclo'] = $cicloDatoAlumno;
		$apiParams['centro_id'] = $this->params['named']['centro_id'];
		$apiParams['curso_id'] = $this->params['named']['curso_id'];
		$apiParams['estado_inscripcion'] = 'CONFIRMADA';
		$apiParams['with'] = 'inscripcion.alumno.familiares.familiar.persona';
		$apiParams['por_pagina'] = 'all';

		$cursosInscripcions = $this->Siep->consumeApi("api/v1/inscripcion/lista",$apiParams);
		if(isset($cursosInscripcions['error']))
		{
			$this->Session->setFlash('ERROR API(Inscripciones): '.$cursosInscripcions['error'], 'default', array('class' => 'alert alert-danger'));
			$this->redirect(array('action' => 'index'));
		}

		// Todas las inscripciones se encuentran en DATA
		$cursosInscripcions = $cursosInscripcions['data'];

		if(count($cursosInscripcions))
		{
			// Obtenemos Ciclo, Centro y Curso de la primera Inscripcion de la lista
			// Todas las inscripciones comparten estas 3 variables
			$first = reset($cursosInscripcions);
			$curso = $first['curso'];
			$centro = $first['inscripcion']['centro'];
			$ciclo = $first['inscripcion']['ciclo'];
		}

		$this->set(compact('cicloActual','cursosInscripcions','ciclo','centro','curso','apiParams','cicloDatoAlumno'));
	}

	public function updateFamiliar()
	{
		$this->autoRender = false;

		// Parametros para ejecutar API
		$apiParams = [];
		$apiParams['mode'] = $this->request->query['mode'];
		$apiParams['id'] = $this->request->query['id'];

		$apiResponse = $this->Siep->consumeApi("api/v1/alumnos_familiars/{$apiParams['id']}",$apiParams,'PUT');

		if(isset($apiResponse['error']))
		{
			$this->Session->setFlash('ERROR API(RelacionFamiliares): '.$apiResponse['error'], 'default', array('class' => 'alert alert-danger'));
			$this->redirect(array('action' => 'index'));
		}

		// Muestra el resultado de un Array como JSON
		$this->response->type('json');
		$json = json_encode($apiResponse);
		$this->response->body($json);
	}
}