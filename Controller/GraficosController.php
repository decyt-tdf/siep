<?php
App::uses('AppController', 'Controller');

class GraficosController extends AppController {
	var $name = 'Graficos';

    public $helpers = array('Session', 'Siep');
	public $components = array('Auth','Session', 'RequestHandler');
	
	function beforeFilter(){
		parent::beforeFilter();
		// Importa el Helper de Siep al controlador es accesible mediante $this->Siep
		App::import('Helper', 'Siep');
		$this->Siep= new SiepHelper(new View());
		/* ACCESOS SEGÚN ROLES DE USUARIOS (INICIO).
        *Si el usuario tiene un rol de superadmin le damos acceso a todo. Si no es así (se trata de un usuario "admin o usuario") tendrá acceso sólo a las acciones que les correspondan.
        */
        switch($this->Auth->user('role'))
		{
			case 'admin':
			case 'superadmin':
			case 'usuario':
				$this->Auth->allow('index');
				break;
			
			default:
				$this->Session->setFlash('No tiene permisos para ver TABLERO 2019.', 'default', array('class' => 'alert alert-warning'));
				$this->redirect($this->referer());
				break;
		}
		/* FIN */
		App::uses('HttpSocket', 'Network/Http');
	}

	public function index() {
		// Obtención del ciclo actual, anterior y posterior.
		$this->loadModel('Ciclo');
		$this->Ciclo->recursive = 0;
		$this->Ciclo->Behaviors->load('Containable');
		$cicloIdActual = $this->getActualCicloId();
		$cicloIdActualArray = $this->Ciclo->findById($cicloIdActual, 'id');
		$cicloIdActual = $cicloIdActualArray['Ciclo']['id'];
		$ciclosVecinos = $this->Ciclo->find('neighbors', ['field' => 'id', 'value' => $cicloIdActual]);
		$cicloIdAnterior = $ciclosVecinos ['prev']['Ciclo']['id'];
		$cicloIdPosterior = $ciclosVecinos ['next']['Ciclo']['id'];		
		// Obtención del nombre y el nivel del centro del usuario.
		$this->loadModel('Centro');
		$this->Centro->recursive = 0;
		$this->Centro->Behaviors->load('Containable');
		// Según el rol del usuario, muestra seleccionar institución del nivel que corresponda.
		switch ($this->Auth->user('role')) {
			case 'superadmin':
			case 'usuario':
				$centroId = null;	
				$this->redirectToNamed();
				$conditions = array();
				if (!empty($this->params['named']['centro_id'])) {
					$centroId = $this->params['named']['centro_id'];
					$userCentroId = $centroId;
				} else {
					if ($this->Auth->user('puesto') == 'Supervisión Inicial/Primaria') {
						$userCentroId = 1;	
					} else {
						$userCentroId = 15;
					}
				}
				break;
			case 'admin':
				$userCentroId = $this->getUserCentroId();
				break;

			default:
				# code...
				break;
		}
		$centroNombreArray = $this->Centro->findById($userCentroId,'nombre');
		$centroNombre = $centroNombreArray['Centro']['nombre'];
		$nivelCentroArray = $this->Centro->findById($userCentroId, 'nivel_servicio');
        $nivelCentro = $nivelCentroArray['Centro']['nivel_servicio'];
		switch ($this->Auth->user('puesto')) {
			case 'Supervisión Inicial/Primaria':
				if ($nivelCentro == 'Común - Inicial' || $nivelCentro == 'Común - Primario' || $nivelCentro == 'Adultos - Primario' || $nivelCentro == 'Especial - Primario') {
					// Puede ver el Tablero 2019 del centro seleccionado.
				} else {
					$this->Session->setFlash('No tiene permisos para ver el TABLERO 2019 solicitado.', 'default', array('class' => 'alert alert-warning'));
					$this->redirect($this->referer());
				}
				break;
			case 'Supervisión Secundaria':
			if ($nivelCentro == 'Común - Secundario' || $nivelCentro == 'Adultos - Secundario') {
				// Puede ver el Tablero 2019 del centro seleccionado.
			} else {
				$this->Session->setFlash('No tiene permisos para ver el TABLERO 2019 solicitado.', 'default', array('class' => 'alert alert-warning'));
				$this->redirect($this->referer());
			}
			break;

			default:
				# code...
				break;
		}

				
		/* INICIO: conteos generales */
		// Conteo de los usuarios.
		$this->loadModel('User');
		/*
		$this->User->recursive = 0;
		$this->User->Behaviors->load('Containable');
		*/
		// Obtención y conteo de datos de ids de empleados de los usuarios activos relacionados al centro. 
		$empleadosId = $this->User->find('list', array(
			'fields' => 'empleado_id',
			'conditions' => array(
				'User.centro_id' => $userCentroId,
				'User.status' => 1)
			)
		);
		// Conteo de usuarios activos del centro.
		$usuarios = count($empleadosId); 
		// Obtención de los nombres completos de los empleados.
		$empleados = $this->User->Empleado->find('list', array(
			'fields' => 'nombre_completo_empleado',
			'conditions' => array('id' => $empleadosId)
		));
		// Conteo de las titulaciones activas en Secundarios (Modalidades: Común y Adultos).
		if ($nivelCentro == 'Común - Secundario' || $nivelCentro == 'Adultos - Secundario') {
			$this->loadModel('Titulacion');
			$this->Titulacion->recursive = 0;
			$this->Titulacion->Behaviors->load('Containable');
			$titulacionesIdTodas = $this->Titulacion->CentrosTitulacion->find('list', array(
				'fields' => array('titulacion_id'),
				'conditions' => array(
					'CentrosTitulacion.centro_id' => $userCentroId
				)
			));			
			$titulacionesIdActivas = $this->Titulacion->find('count', array(
				'conditions' => array(
					'Titulacion.id' => $titulacionesIdTodas,
					'Titulacion.status' => 1
				)
			));
		}
		// Conteo de las secciones.
		$this->loadModel('Curso');
		$this->Curso->recursive = 0;
		$this->Curso->Behaviors->load('Containable');
		$cursos = $this->Curso->find('count', array(
			'conditions' => array(
				'Curso.centro_id' => $userCentroId,
				'Curso.division !=' => ' ',
				'Curso.turno !=' => 'Otro',
				'Curso.status' => 1
			)
		));
		// Conteo de matrícula actual.
		$this->loadModel('Inscripcion');
		$this->Inscripcion->recursive = 0;
		$this->Inscripcion->Behaviors->load('Containable');
		$matricula = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.estado_inscripcion' => 'CONFIRMADA',
				'Inscripcion.ciclo_id' => $cicloIdActual
			))
		);
		// Conteo de baja de matrícula.
		$legajoTipo = 'SINVACANTE';
		$matriculaBaja = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.estado_inscripcion' => 'BAJA',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.legajo_nro NOT LIKE' => '%'.$legajoTipo.'%'
			))
		);
		// Conteo de promociones Totales.		
		$this->loadModel('CursosInscripcion');
		$matriculaPromociones = $this->CursosInscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.promocion_id !=' => '',
				'Inscripcion.centro_id' => $userCentroId,
			))
		);
		// Conteo de repitentes. (PENDIENTE CON el campo repitencia_id) 
		$matriculaRepitencias = $this->CursosInscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.repitencia_id !=' => '',
				'Inscripcion.centro_id' => $userCentroId,
			))
		);
		// Conteo de egresos.
		$matriculaEgresos = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.estado_inscripcion' => 'EGRESO',
				'Inscripcion.ciclo_id' => $cicloIdActual
			))
		);
		/* Conteo de PASES ENTRANTES*/
		//$matriculaPases = $inscripcionesPorPase + $bajasSalidosConPase;
		$matriculaPasesEntrantes = $this->CursosInscripcion->Inscripcion->find('count', array(
			'contain'=>false,
			'conditions'=>array(
				'ciclo_id'=>$cicloIdActual, 
				'centro_id'=>$userCentroId,
				'tipo_inscripcion'=>'Pase',
				'estado_inscripcion'=>'CONFIRMADA')
			)
		);
		/* Conteo de PASES SALIENTES*/
		$matriculaPasesSalientes = $this->CursosInscripcion->Inscripcion->find('count', array(
			'contain'=>false,
			'conditions'=>array(
				'ciclo_id'=>$cicloIdActual,
				'centro_id'=>$userCentroId,
				'estado_inscripcion'=>'BAJA',
				'tipo_baja'=>'Salido con pase')
			)
		);
		/* FIN: conteos generales */
		/* INICIO: conteos específicos */
		/* CONTEO de secciones por año para usuarios con role "admin". 
		** Según el nivel_servicio, realiza el conteo de Salas (Inicial) o Grados (Primario) o Cursos (Secundario).
		*/
        switch ($nivelCentro) {
			case 'Común - Inicial':
				$cursosTresAnios = $this->Curso->find('count', array(
					'conditions' => array(
						'Curso.centro_id' => $userCentroId,
						'Curso.anio' => array('Sala de 3 años'),
						'Curso.division !=' => '',
						'Curso.turno !=' => 'Otro',
						'Curso.tipo' => 'Independiente',
						'Curso.status' => 1
					)
				));
				$cursosTresAniosMultiple = $this->Curso->find('count', array(
					'conditions' => array(
						'Curso.centro_id' => $userCentroId,
						'Curso.anio' => array('Sala de 3 años'),
						'Curso.division !=' => '',
						'Curso.turno !=' => 'Otro',
						'Curso.tipo' => 'Múltiple',
						'Curso.status' => 1
					)
				));
				$cursosCuatroAnios = $this->Curso->find('count', array(
					'conditions' => array(
						'Curso.centro_id' => $userCentroId,
						'Curso.anio' => array('Sala de 4 años'),
						'Curso.division !=' => '',
						'Curso.turno !=' => 'Otro',
						'Curso.tipo' => 'Independiente',
						'Curso.status' => 1
					)
				));
				$cursosCuatroAniosMultiple = $this->Curso->find('count', array(
					'conditions' => array(
						'Curso.centro_id' => $userCentroId,
						'Curso.anio' => array('Sala de 4 años'),
						'Curso.division !=' => '',
						'Curso.turno !=' => 'Otro',
						'Curso.tipo' => 'Múltiple',
						'Curso.status' => 1
					)
				));
				$cursosCincoAnios = $this->Curso->find('count', array(
					'conditions' => array(
						'Curso.centro_id' => $userCentroId,
						'Curso.anio' => array('Sala de 5 años'),
						'Curso.division !=' => '',
						'Curso.turno !=' => 'Otro',
						'Curso.tipo' => 'Independiente',
						'Curso.status' => 1
					)
				));
				break;
			case 'Común - Primario':
			case 'Común - Secundario':
			case 'Adultos - Primario':
			case 'Adultos - Secundario':
				// Todos los niveles-servicios cuentan 3 años.
				$cursosPrimerosAnios = $this->Curso->find('count', array(
					'conditions' => array(
						'Curso.centro_id' => $userCentroId,
						'Curso.anio' => array('1ro'),
						'Curso.division !=' => '',
						'Curso.turno !=' => 'Otro',
						//'Curso.tipo' => 'Independiente',
						'Curso.status' => 1
					)
				));
				$cursosSegundosAnios = $this->Curso->find('count', array(
					'conditions' => array(
						'Curso.centro_id' => $userCentroId,
						'Curso.anio' => array('2do'),
						'Curso.division !=' => '',
						'Curso.turno !=' => 'Otro',
						//'Curso.tipo' => 'Independiente',
						'Curso.status' => 1
					)
				));
				$cursosTercerosAnios = $this->Curso->find('count', array(
					'conditions' => array(
						'Curso.centro_id' => $userCentroId,
						'Curso.anio' => array('3ro'),
						'Curso.division !=' => '',
						'Curso.turno !=' => 'Otro',
						//'Curso.tipo' => 'Independiente',
						'Curso.status' => 1
					)
				));
				// Sólo Primario y Secundario cuenta 4tos a 6tos.
				if ($nivelCentro != 'Adulto - Primario' || $nivelCentro != 'Adulto - Secundario') {
					$cursosCuartosAnios = $this->Curso->find('count', array(
						'conditions' => array(
							'Curso.centro_id' => $userCentroId,
							'Curso.anio' => array('4to'),
							'Curso.division !=' => '',
							'Curso.turno !=' => 'Otro',
							//'Curso.tipo' => 'Independiente',
							'Curso.status' => 1
						)
					));
					$cursosQuintosAnios = $this->Curso->find('count', array(
						'conditions' => array(
							'Curso.centro_id' => $userCentroId,
							'Curso.anio' => array('5to'),
							'Curso.division !=' => '',
							'Curso.turno !=' => 'Otro',
							//'Curso.tipo' => 'Independiente',
							'Curso.status' => 1
						)
					));
					$cursosSextosAnios = $this->Curso->find('count', array(
						'conditions' => array(
							'Curso.centro_id' => $userCentroId,
							'Curso.anio' => array('6to'),
							'Curso.division !=' => '',
							'Curso.turno !=' => 'Otro',
							//'Curso.tipo' => 'Independiente',
							'Curso.status' => 1
						)
					));
				}
				// Sólo Secundarios cuentan 7mos.
				if ($nivelCentro == 'Común - Secundario') {
					$cursosSeptimosAnios = $this->Curso->find('count', array(
						'conditions' => array(
							'Curso.centro_id' => $userCentroId,
							'Curso.anio' => array('7mo'),
							'Curso.division !=' => '',
							'Curso.turno !=' => 'Otro',
							//'Curso.tipo' => 'Independiente',
							'Curso.status' => 1
						)
					));
				}
				// Sólo Adultos - Primario cuentan ALFABETIZACIÓN.
				if ($nivelCentro == 'Adultos - Primario') {
					$cursosAlfabetizacion = $this->Curso->find('count', array(
						'conditions' => array(
							'Curso.centro_id' => $userCentroId,
							'Curso.anio' => array('ALFABETIZACIÓN'),
							'Curso.division !=' => '',
							'Curso.turno !=' => 'Otro',
							'Curso.tipo' => 'Múltiple',
							'Curso.status' => 1
						)
					));
					$cursosCAP = $this->Curso->find('count', array(
						'conditions' => array(
							'Curso.centro_id' => $userCentroId,
							'Curso.anio' => array('CAP'),
							'Curso.division !=' => '',
							'Curso.turno !=' => 'Otro',
							//'Curso.tipo' => 'Idependiente',
							'Curso.status' => 1
						)
					));
				}				
				break;

			default:
				# code...
				break;
		}
		// Conteo de inscripciones por hermanos.
		$inscripcionesPorHermano = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.tipo_inscripcion' => 'Hermano de alumno regular',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.estado_inscripcion' => 'CONFIRMADA',
			))
		);
		// Conteo de inscripciones comunes.
		$inscripcionesComunes = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.tipo_inscripcion' => 'Común',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.estado_inscripcion' => 'CONFIRMADA',
			))
		);
		// Conteo de inscripciones por situación social.
		$inscripcionesPorSituacionSocial = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.tipo_inscripcion' => 'Situación Social',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.estado_inscripcion' => 'CONFIRMADA',
			))
		);
		// Conteo de inscripciones por Pase (Entradas).
		$inscripcionesPorPase = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.tipo_inscripcion' => 'Pase',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.estado_inscripcion' => 'CONFIRMADA',
			))
		);
		// Conteo de bajas por salidos con pase.
		$bajasSalidosConPase = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.estado_inscripcion' => 'BAJA',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.tipo_baja' => 'Salido con pase',
				'Inscripcion.legajo_nro NOT LIKE' => '%'.$legajoTipo.'%'
			))
		);
		// Conteo de bajas por salidos sin pase.
		$bajasSalidosSinPase = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.estado_inscripcion' => 'BAJA',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.tipo_baja' => 'Salido sin pase',
				'Inscripcion.legajo_nro NOT LIKE' => '%'.$legajoTipo.'%'
			))
		);
		// Conteo de bajas por pérdida de regularidad.
		$bajasPerdidaRegularidad = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.estado_inscripcion' => 'BAJA',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.tipo_baja' => 'Pérdida de regularidad',
				'Inscripcion.legajo_nro NOT LIKE' => '%'.$legajoTipo.'%'
			))
		);
		// Conteo de bajas por fallecimiento.
		$bajasFallecimiento = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.estado_inscripcion' => 'BAJA',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.tipo_baja' => 'Fallecimiento',
				'Inscripcion.legajo_nro NOT LIKE' => '%'.$legajoTipo.'%'
			))
		);
		// Conteo de bajas sin especificar tipo.
		$bajasSinEspecificar = $this->Inscripcion->find('count', array(
			'conditions' => array(
				'Inscripcion.centro_id' => $userCentroId,
				'Inscripcion.estado_inscripcion' => 'BAJA',
				'Inscripcion.ciclo_id' => $cicloIdActual,
				'Inscripcion.tipo_baja' => 'Sin especificar',
				'Inscripcion.legajo_nro NOT LIKE' => '%'.$legajoTipo.'%'
			))
		);
		/* Conteo de PASES ENTRANTES por AÑO */
		switch ($nivelCentro) {
			case 'Común - Inicial':
				// Sala de 3 años.
				$matriculaPasesEntrantes3anios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.tipo_inscripcion'=>'Pase',
						'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
						'Curso.anio' => 'Sala de 3 años'
					))
				);
				// Sala de 4 años.
				$matriculaPasesEntrantes4anios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.tipo_inscripcion'=>'Pase',
						'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
						'Curso.anio' => 'Sala de 4 años'
					))
				);
				// Sala de 5 años.
				$matriculaPasesEntrantes5anios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.tipo_inscripcion'=>'Pase',
						'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
						'Curso.anio' => 'Sala de 5 años'
					))
				);
				break;
			
			case 'Común - Primario':
			case 'Común - Secundario':
			case 'Adultos - Primario':
			case 'Adultos - Secundario':
				// 1ro.
				$matriculaPasesEntrantes1ro = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.tipo_inscripcion'=>'Pase',
						'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
						'Curso.anio' => '1ro'
					))
				);
				// 2do.
				$matriculaPasesEntrantes2do = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.tipo_inscripcion'=>'Pase',
						'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
						'Curso.anio' => '2do'
					))
				);
				// 3ro.
				$matriculaPasesEntrantes3ro = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.tipo_inscripcion'=>'Pase',
						'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
						'Curso.anio' => '3ro'
					))
				);
				// 4to.
				$matriculaPasesEntrantes4to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.tipo_inscripcion'=>'Pase',
						'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
						'Curso.anio' => '4to'
					))
				);
				// 5to.
				$matriculaPasesEntrantes5to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.tipo_inscripcion'=>'Pase',
						'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
						'Curso.anio' => '5to'
					))
				);
				// 6to.
				$matriculaPasesEntrantes6to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.tipo_inscripcion'=>'Pase',
						'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
						'Curso.anio' => '6to'
					))
				);
				// Sólo Secundarios cuentan 7mos.
				if ($nivelCentro == 'Común - Secundario') {
					// 7mo.
					$matriculaPasesEntrantes7mo = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id'=>$cicloIdActual,
							'Inscripcion.centro_id'=>$userCentroId,
							'Inscripcion.tipo_inscripcion'=>'Pase',
							'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
							'Curso.anio' => '7mo'
						))
					);
				}
				if ($nivelCentro == 'Adultos - Primario') {
					// Alfabetizacion.
					$matriculaPasesEntrantesAlfabetizacion = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id'=>$cicloIdActual,
							'Inscripcion.centro_id'=>$userCentroId,
							'Inscripcion.tipo_inscripcion'=>'Pase',
							'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
							'Curso.anio' => 'ALFABETIZACIÓN'
						))
					);
					// CAP.
					$matriculaPasesEntrantesCAP = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id'=>$cicloIdActual,
							'Inscripcion.centro_id'=>$userCentroId,
							'Inscripcion.tipo_inscripcion'=>'Pase',
							'Inscripcion.estado_inscripcion'=>'CONFIRMADA',
							'Curso.anio' => 'CAP'
						))
					);
				}	
				break;
			
			default:
				# code...
				break;
		}
		/* Conteo de PASES SALIENTES por AÑO */
		switch ($nivelCentro) {
			case 'Común - Inicial':
				// Sala de 3 años.
				$matriculaPasesSalientes3anios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.estado_inscripcion'=>'BAJA',
						'Inscripcion.tipo_baja'=>'Salido con pase',
						'Curso.anio' => 'Sala de 3 años'
					))
				);
				// Sala de 4 años.
				$matriculaPasesSalientes4anios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.estado_inscripcion'=>'BAJA',
						'Inscripcion.tipo_baja'=>'Salido con pase',
						'Curso.anio' => 'Sala de 4 años'
					))
				);
				// Sala de 5 años.
				$matriculaPasesSalientes5anios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.estado_inscripcion'=>'BAJA',
						'Inscripcion.tipo_baja'=>'Salido con pase',
						'Curso.anio' => 'Sala de 5 años'
					))
				);
				break;
			
			case 'Común - Primario':
			case 'Común - Secundario':
			case 'Adultos - Primario':
			case 'Adultos - Secundario':
				// 1ro.
				$matriculaPasesSalientes1ro = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.estado_inscripcion'=>'BAJA',
						'Inscripcion.tipo_baja'=>'Salido con pase',
						'Curso.anio' => '1ro'
					))
				);
				// 2do.
				$matriculaPasesSalientes2do = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.estado_inscripcion'=>'BAJA',
						'Inscripcion.tipo_baja'=>'Salido con pase',
						'Curso.anio' => '2do'
					))
				);
				// 3ro.
				$matriculaPasesSalientes3ro = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.estado_inscripcion'=>'BAJA',
						'Inscripcion.tipo_baja'=>'Salido con pase',
						'Curso.anio' => '3ro'
					))
				);
				// 4to.
				$matriculaPasesSalientes4to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.estado_inscripcion'=>'BAJA',
						'Inscripcion.tipo_baja'=>'Salido con pase',
						'Curso.anio' => '4to'
					))
				);
				// 5to.
				$matriculaPasesSalientes5to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.estado_inscripcion'=>'BAJA',
						'Inscripcion.tipo_baja'=>'Salido con pase',
						'Curso.anio' => '5to'
					))
				);
				// 6to.
				$matriculaPasesSalientes6to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id'=>$cicloIdActual,
						'Inscripcion.centro_id'=>$userCentroId,
						'Inscripcion.estado_inscripcion'=>'BAJA',
						'Inscripcion.tipo_baja'=>'Salido con pase',
						'Curso.anio' => '6to'
					))
				);
				// Sólo Secundarios cuentan 7mos.
				if ($nivelCentro == 'Común - Secundario') {
					// 7mo.
					$matriculaPasesSalientes7mo = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id'=>$cicloIdActual,
							'Inscripcion.centro_id'=>$userCentroId,
							'Inscripcion.estado_inscripcion'=>'BAJA',
							'Inscripcion.tipo_baja'=>'Salido con pase',
							'Curso.anio' => '7mo'
						))
					);
				}
				if ($nivelCentro == 'Adultos - Primario') {
					// Alfabetizacion.
					$matriculaPasesSalientesAlfabetizacion = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id'=>$cicloIdActual,
							'Inscripcion.centro_id'=>$userCentroId,
							'Inscripcion.estado_inscripcion'=>'BAJA',
							'Inscripcion.tipo_baja'=>'Salido con pase',
							'Curso.anio' => 'ALFABETIZACIÓN'
						))
					);
					// CAP.
					$matriculaPasesSalientesCAP = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id'=>$cicloIdActual,
							'Inscripcion.centro_id'=>$userCentroId,
							'Inscripcion.estado_inscripcion'=>'BAJA',
							'Inscripcion.tipo_baja'=>'Salido con pase',
							'Curso.anio' => 'CAP'
						))
					);
				}	
				break;
			
			default:
				# code...
				break;
		}
		/* Conteo de PROMOCIONES por años. */
		switch ($nivelCentro) {
			case 'Común - Inicial':
				// Sala de 3 años.
				$matriculaPromocionesTresAnios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.promocion_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => 'Sala de 3 años'
					))
				);
				// Sala de 4 años.
				$matriculaPromocionesCuatroAnios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.promocion_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => 'Sala de 4 años'
					))
				);
				// Sala de 5 años.
				$matriculaPromocionesCincoAnios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.promocion_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => 'Sala de 5 años'
					))
				);
				break;
			case 'Común - Primario':
			case 'Común - Secundario':
			case 'Adultos - Primario':
			case 'Adultos - Secundario':
				// Primer año.
				$matriculaPromociones1ro = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.promocion_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '1ro'
					))
				);
				// Segundo año.
				$matriculaPromociones2do = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.promocion_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '2do'
					))
				);
				// Tercer año.
				$matriculaPromociones3ro = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.promocion_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '3ro'
					))
				);
				// Cuarto año.
				$matriculaPromociones4to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.promocion_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '4to'
					))
				);
				// Quinto año.
				$matriculaPromociones5to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.promocion_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '5to'
					))
				);
				// Sexto año.
				$matriculaPromociones6to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.promocion_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '6to'
					))
				);
				if ($nivelCentro == 'Adultos - Primario') {
					// Alfabetizacion.
					$matriculaPromocionesAlfabetizacion = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id' => $cicloIdActual,
							'Inscripcion.promocion_id !=' => '',
							'Inscripcion.centro_id' => $userCentroId,
							'Curso.anio' => 'ALFABETIZACIÓN'
						))
					);
					// CAP.
					$matriculaPromocionesCAP = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id' => $cicloIdActual,
							'Inscripcion.promocion_id !=' => '',
							'Inscripcion.centro_id' => $userCentroId,
							'Curso.anio' => 'CAP'
						))
					);
				}
				break;

			default:
				# code...
				break;
		}
		/* Conteo de REPITENCIAS por años. */
		switch ($nivelCentro) {
			case 'Común - Inicial':
				// Sala de 3 años.
				$matriculaRepitenciasTresAnios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => 'Sala de 3 años'
					))
				);
				// Sala de 4 años.
				$matriculaRepitenciasCuatroAnios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => 'Sala de 4 años'
					))
				);
				// Sala de 5 años.
				$matriculaRepitenciasCincoAnios = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => 'Sala de 5 años'
					))
				);
				break;
			case 'Común - Primario':
			case 'Común - Secundario':
			case 'Adultos - Primario':
			case 'Adultos - Secundario':
				// Primer año.
				$matriculaRepitencias1ro = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '1ro'
					))
				);
				// Segundo año.
				$matriculaRepitencias2do = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '2do'
					))
				);
				// Tercer año.
				$matriculaRepitencias3ro = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '3ro'
					))
				);
				// Cuarto año.
				$matriculaRepitencias4to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '4to'
					))
				);
				// Quinto año.
				$matriculaRepitencias5to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '5to'
					))
				);
				// Sexto año.
				$matriculaRepitencias6to = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '6to'
					))
				);
				// Sexto año.
				$matriculaRepitencias7mo = $this->CursosInscripcion->find('count', array(
					'conditions' => array(
						'Inscripcion.ciclo_id' => $cicloIdActual,
						'Inscripcion.repitencia_id !=' => '',
						'Inscripcion.centro_id' => $userCentroId,
						'Curso.anio' => '7mo'
					))
				);
				if ($nivelCentro == 'Adultos - Primario') {
					// Alfabetizacion.
					$matriculaRepitenciasAlfabetizacion = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id' => $cicloIdActual,
							'Inscripcion.repitencia_id !=' => '',
							'Inscripcion.centro_id' => $userCentroId,
							'Curso.anio' => 'ALFABETIZACIÓN'
						))
					);
					// CAP.
					$matriculaRepitenciasCAP = $this->CursosInscripcion->find('count', array(
						'conditions' => array(
							'Inscripcion.ciclo_id' => $cicloIdActual,
							'Inscripcion.repitencia_id !=' => '',
							'Inscripcion.centro_id' => $userCentroId,
							'Curso.anio' => 'CAP'
						))
					);
				}
			break;

			default:
				# code...
			break;
		}
		// Envío de valores a la vista.
		$this->set(compact('centroNombre', 'usuarios', 'empleados', 'cursos', 'matricula', 'ingresantes',
		 'matriculaBaja', 'matriculaPromociones', 'matriculaPromocionesTresAnios', 'matriculaPromocionesCuatroAnios', 'matriculaPromocionesCincoAnios', 'matriculaPromociones1ro', 'matriculaPromociones2do', 'matriculaPromociones3ro', 'matriculaPromociones4to', 'matriculaPromociones5to', 'matriculaPromociones6to', 'matriculaPromocionesAlfabetizacion', 'matriculaPromocionesCAP',
		 'matriculaRepitencias', 'matriculaRepitenciasTresAnios', 'matriculaRepitenciasCuatroAnios', 'matriculaRepitenciasCincoAnios', 'matriculaRepitencias1ro', 'matriculaRepitencias2do', 'matriculaRepitencias3ro', 'matriculaRepitencias4to', 'matriculaRepitencias5to', 'matriculaRepitencias6to', 'matriculaRepitencias7mo', 'matriculaRepitenciasAlfabetizacion', 'matriculaRepitenciasCAP',
		 'matriculaEgresos', 'matriculaPasesEntrantes', 'matriculaPasesSalientes', 'matriculaPasesEntrantes3anios', 'matriculaPasesEntrantes4anios', 'matriculaPasesEntrantes5anios', 'matriculaPasesEntrantes1ro', 'matriculaPasesEntrantes2do', 'matriculaPasesEntrantes3ro', 'matriculaPasesEntrantes4to', 'matriculaPasesEntrantes5to', 'matriculaPasesEntrantes6to', 'matriculaPasesEntrantes7mo', 'matriculaPasesEntrantesAlfabetizacion', 'matriculaPasesEntrantesCAP',
		 'matriculaPasesSalientes3anios', 'matriculaPasesSalientes4anios', 'matriculaPasesSalientes5anios', 'matriculaPasesSalientes1ro', 'matriculaPasesSalientes2do', 'matriculaPasesSalientes3ro', 'matriculaPasesSalientes4to', 'matriculaPasesSalientes5to', 'matriculaPasesSalientes6to', 'matriculaPasesSalientes7mo', 'matriculaPasesSalientesAlfabetizacion', 'matriculaPasesSalientesCAP',
		 'cursosTresAnios', 'cursosTresAniosMultiple', 'cursosCuatroAnios', 'cursosCuatroAniosMultiple', 'cursosCincoAnios', 'cursosPrimerosAnios', 'cursosSegundosAnios', 'cursosTercerosAnios', 'cursosCuartosAnios',
		 'cursosQuintosAnios', 'cursosSextosAnios', 'cursosSeptimosAnios', 'cursosAlfabetizacion', 'cursosCAP', 'inscripcionesPorHermano', 'inscripcionesComunes', 'inscripcionesPorSituacionSocial',
		 'inscripcionesPorPase', 'bajasSalidosConPase', 'bajasSalidosSinPase', 'bajasPerdidaRegularidad', 
		 'bajasFallecimiento', 'bajasSinEspecificar', 'titulacionesIdActivas', 'nivelCentro', 'filtro', 'userCentroId')); 
	} 
}
?>