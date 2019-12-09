<?php
App::uses('AppController', 'Controller');

class InscripcionsController extends AppController {

	var $name = 'Inscripcions';
    var $paginate = array('Inscripcion' => array(
        'contain' => array('Centro', 'Ciclo', 'Alumno'),
        'limit' => 4,
        'order' => 'Inscripcion.fecha_alta DESC'));
    // Permite agregar el Helper de Siep a las vistas
    public $helpers = array('Siep');

    function beforeFilter(){
	    parent::beforeFilter();
		/* ACCESOS SEGÚN ROLES DE USUARIOS (INICIO).
        *Si el usuario tiene un rol de superadmin le damos acceso a todo. Si no es así (se trata de un usuario "admin o usuario") tendrá acceso sólo a las acciones que les correspondan.
        */
        switch($this->Auth->user('role'))
        {
            case 'superadmin':
            case 'usuario':
                $this->Auth->allow();
                /*
                if ($this->Auth->user('puesto') === 'Sistemas') {
                    $this->Auth->allow();               
                } else {
                    //En caso de ser ATEI
                    $this->Auth->allow('index', 'add', 'view', 'edit');    
                }
                */
                break;
            case 'admin':
                $this->Auth->allow('index', 'add', 'view', 'edit');
                break;

			default:
                $this->Session->setFlash('No tiene permisos.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect($this->referer());
                break;
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

        // Importa el Helper de Siep al controlador es accesible mediante $this->Siep
        App::import('Helper', 'Siep');
        $this->Siep= new SiepHelper(new View());
    }

	public function index() {
        $this->Inscripcion->recursive = 0;
		$this->paginate['Inscripcion']['contain'] = 'Alumno.Persona';
		$this->paginate['Inscripcion']['limit'] = 4;
		$this->paginate['Inscripcion']['order'] = array('Inscripcion.fecha_alta' => 'DESC');
		/* PAGINACIÓN SEGÚN ROLES DE USUARIOS (INICIO).
		*Sí el usuario es "admin" muestra los cursos del establecimiento. Sino sí es "usuario" externo muestra los cursos del nivel.
		*/
        $userRole = $this->Auth->user('role');
        $userCentroId = $this->getUserCentroId();
        $this->loadModel('Centro');
        $this->Centro->recursive = 0;
        $this->Centro->Behaviors->load('Containable');
        $nivelCentroArray = $this->Centro->findById($userCentroId, 'nivel_servicio');
        $nivelCentro = $nivelCentroArray['Centro']['nivel_servicio'];
        $nivelCentroId = $this->Centro->find('list', array(
            'fields'=>array('id'),
            'contain'=>false,
            'conditions'=>array(
                'nivel_servicio'=>$nivelCentro)));
		if ($this->Auth->user('role') === 'admin') {
        $this->paginate['Inscripcion']['conditions'] = array('Inscripcion.centro_id' => $userCentroId, 'Inscripcion.estado_inscripcion' =>array('CONFIRMADA', 'NO CONFIRMADA', 'BAJA', 'EGRESO', 'ANULADA', 'SIN TERMINALIDAD'));    
        } else if (($userRole === 'usuario') && ($nivelCentro === 'Común - Inicial - Primario')) {
			$nivelCentroId = $this->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>array('Común - Inicial', 'Común - Primario'))));
			$this->paginate['Inscripcion']['conditions'] = array('Inscripcion.centro_id' => $nivelCentroId, 'Inscripcion.estado_inscripcion' =>array('CONFIRMADA', 'NO CONFIRMADA', 'BAJA', 'EGRESO', 'ANULADA', 'SIN TERMINALIDAD'));
		} else if ($userRole === 'usuario') {
			$nivelCentroId = $this->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>$nivelCentro)));
			$this->paginate['Inscripcion']['conditions'] = array('Inscripcion.centro_id' => $nivelCentroId, 'Inscripcion.estado_inscripcion' =>array('CONFIRMADA', 'NO CONFIRMADA', 'BAJA', 'EGRESO', 'ANULADA', 'SIN TERMINALIDAD'));
		}
		/* FIN */
    	/* PAGINACIÓN SEGÚN CRITERIOS DE BÚSQUEDAS (INICIO).
		*Pagina según búsquedas simultáneas ya sea por CICLO y/o CENTRO y/o LEGAJO y/o ESTADO.
		*/
    	$this->redirectToNamed();
		$conditions = array();
		if (!empty($this->params['named']['ciclo_id'])) {
			$conditions['Inscripcion.ciclo_id ='] = $this->params['named']['ciclo_id'];
		}
		if (!empty($this->params['named']['centro_id'])) {
			$conditions['Inscripcion.centro_id ='] = $this->params['named']['centro_id'];
		}
		if (!empty($this->params['named']['legajo_nro'])) {
			$conditions['Inscripcion.legajo_nro ='] = $this->params['named']['legajo_nro'];
		}
		if(!empty($this->params['named']['tipo_inscripcion'])) {
            $conditions['Inscripcion.tipo_inscripcion ='] = $this->params['named']['tipo_inscripcion'];
        }
        if(!empty($this->params['named']['estado_documentacion'])) {
			$conditions['Inscripcion.estado_documentacion ='] = $this->params['named']['estado_documentacion'];
		}
        if(!empty($this->params['named']['estado_inscripcion'])) {
            $conditions['Inscripcion.estado_inscripcion ='] = $this->params['named']['estado_inscripcion'];
        }
        if(!empty($this->params['named']['tipo_baja'])) {
            $conditions['Inscripcion.tipo_baja ='] = $this->params['named']['tipo_baja'];
        }
        if(!empty($this->params['named']['cud_estado'])) {
            $conditions['Inscripcion.cud_estado ='] = $this->params['named']['cud_estado'];
        }
		$inscripcions = $this->paginate('Inscripcion',$conditions);
		/* FIN */
		/* SETS DE DATOS PARA COMBOBOX (INICIO). */
		/* Carga de Ciclos */
        $this->Inscripcion->Ciclo->recursive = 0;
        $ciclos = $this->Inscripcion->Ciclo->find('list', array(
            'fields'=>array('id', 'nombre'),
            'contain'=>false
            ));
        /* Carga combobox de Centros
        *  Sí es superadmin carga todos los centros.
        *  Sino sí es un usario de Inicial/Primaria, carga los centros de ambos niveles.
        *  Sino sí es un usuario del resto de los niveles, carga los centros del nivel correspondientes.     
        */
		if ($userRole == 'superadmin') {
			$centros = $this->Inscripcion->Centro->find('list', array('fields'=>array('id', 'sigla'), 'contain'=>false));
		} else if (($userRole === 'usuario') && ($nivelCentro === 'Común - Inicial - Primario')) {
			$nivelCentroId = $this->Inscripcion->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>array('Común - Inicial', 'Común - Primario')))); 		
			$centros = $this->Inscripcion->Centro->find('list', array('fields'=>array('sigla'), 'contain'=>false, 'conditions'=>array('id'=>$nivelCentroId)));
        } else if ($userRole === 'usuario') {
            $nivelCentroId = $this->Inscripcion->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>$nivelCentro)));       
            $centros = $this->Inscripcion->Centro->find('list', array('fields'=>array('sigla'), 'contain'=>false, 'conditions'=>array('id'=>$nivelCentroId)));
        } else if ($userRole == 'admin') {
			$centros = $this->Inscripcion->Centro->find('list', array('fields'=>array('id', 'sigla'), 'contain'=>false, 'conditions'=>array('id'=>$nivelCentroId)));
		}
		$this->set(compact('inscripcions', 'centros', 'ciclos'));
	}

    public function view($id = null) {
        $this->Inscripcion->recursive = 0;
        if (!$id) {
            $this->Session->setFlash('Inscripcion no valida.', 'default', array('class' => 'alert alert-warning'));
            $this->redirect(array('action' => 'index'));
        }
        
        $inscripcion = null;
        $cursos = null;

        $apiParams = [];
        $apiParams['with'] = 'inscripcion.hermano.persona,inscripcion.pase';
        // La V2 obtiene los cursos en forma de array
        // Debido a que existe la posibilidad de que el alumno se encuentre en varias secciones
        $apiInscripcion = $this->Siep->consumeApi("api/v2/inscripcion/id/$id",$apiParams);

        // Si no existe error al consumir el api
        if(!isset($apiInscripcion['error']))
        {
            $cursos = $apiInscripcion['cursos'];
            $inscripcion = $apiInscripcion['inscripcion'];

            $this->set(compact('inscripcion','cursos'));
        } else {
            // Error al consumir el API
            $this->Session->setFlash($apiInscripcion['error'], 'default', array('class' => 'alert alert-danger'));
            $this->redirect(array('action' => 'index'));
        }

        //Obtención del nivel del centro del usuario.
        $userCentroId = $this->getUserCentroId();
        $userCentroNivel = $this->getUserCentroNivel($userCentroId);

        // Obtención del ciclo actual.
        $hoyArray = getdate();
        $nombreCicloActual = $hoyArray['year'];
        $this->loadModel('Ciclo');
        $this->Ciclo->recursive = 0;
        $this->Ciclo->Behaviors->load('Containable');
        $cicloIdActualArray = $this->Ciclo->findByNombre($nombreCicloActual, 'id');
        $cicloIdActual = $cicloIdActualArray['Ciclo']['id']; 
        $cicloIdPosterior = $cicloIdActual + 2;
        //Envío de dato a la vista.

        $this->set(compact('userCentroNivel', 'userCentroId', 'cicloIdActual', 'cicloIdPosterior'));
    }

	public function add() {
        $this->Inscripcion->recursive = 0;
        /* BOTÓN CANCELAR (INICIO) */
        if (isset($this->params['data']['cancel'])) {
            $this->Session->setFlash('Los cambios no fueron guardados. Agregación cancelada.', 'default', array('class' => 'alert alert-warning'));
            $this->redirect( array( 'action' => 'index' ));
		}
        /* FIN */
        /* INICIO: PERMISO PARA AGREGAR SEGÚN EL NIVEL DEL CENTRO DEL USUARIO */
        //Se obtiene el rol del usuario
        $userRole = $this->Auth->user('role');
        //Se obtiene el centro del usuario
        $userCentroId = $this->getUserCentroId();
        $userData = $this->Auth->user();
        if($userRole == 'admin') {
            switch($userData['Centro']['nivel_servicio']) {
                /*
                case 'Común - Secundario':
                    if ($userData['Centro']['sector'] == 'ESTATAL') {
                        $this->Session->setFlash('No tiene permisos para agregar inscripciones.', 'default', array('class' => 'alert alert-warning'));
                        $this->redirect( array( 'action' => 'index' ));
                    }
                    break;
                */
                case 'Maternal - Inicial':
                case 'Especial - Primario':
                case 'Común - Inicial':
                case 'Común - Primario':
                case 'Común - Secundario':
                case 'Común - Superior':
                case 'Adultos - Primario':
                case 'Adultos - Secundario':
                case 'Especial - Integración':
                case 'Común - Servicios complementarios':
                //  PERMITIDOS AGREGARR
                    break;
                default:
                    $this->Session->setFlash('No tiene permisos para agregar inscripciones.', 'default', array('class' => 'alert alert-warning'));
                    $this->redirect( array( 'action' => 'index' ));
                    break;
            }
        }
        $this->Inscripcion->contain(array('Centro', 'Ciclo'));
        /* FIN */
        //Al realizar SUBMIT
        if (!empty($this->data)) {
            $this->Inscripcion->create();
            //Se genera el id del usuario
            $this->request->data['Inscripcion']['usuario_id'] = $this->Auth->user('id');
            //La fecha de alta se toma del servidor php al momento de ejecutar el controlador
            $this->request->data['Inscripcion']['fecha_alta'] = date('Y-m-d');
            /* DEFINICIÓN DEL CENTRO DE ORIGEN SEGÚN EL ROL DEL USUARIO (INICIO) */
            switch($userRole) {
                case 'superadmin':
                case 'usuario':
                    // Usa el centro especificado en el formulario
                    $userCentroId = $this->request->data['Inscripcion']['centro_id'];
                break;
                case 'admin':
                    // Usa el centro al que pertenece el usuario.
                    $this->request->data['Inscripcion']['centro_id'] = $userCentroId;
                break;
            }
            /* FIN */
            // Luego de seleccionar el ciclo, se deja en los datos que se intentarán guardar.
            $cicloId = $this->request->data['Inscripcion']['ciclo_id'];
            /* No continua la inscripcion si: 
            ** - Es un usuario del nivel "Común-Secundario, Común Incial o Común Primario",
            ** - Del sector "ESTATAL",
            ** - El ciclo seleccionado es 2020.*/
            if (($cicloId == 7) && ($userData['Centro']['sector'] == 'ESTATAL')) {
                switch ($userData['Centro']['nivel_servicio']) {
                    case 'Común - Inicial':
                        if (!($userData['Centro']['id'] == 129 || $userData['Centro']['id'] == 150 || $userData['Centro']['id'] == 507 || $userData['Centro']['id'] == 512 || $userData['Centro']['id'] == 532)) {    
                            $this->Session->setFlash('Momentáneamente no se permiten las inscripciones 2020.', 'default', array('class' => 'alert alert-danger'));
                            $this->redirect($this->referer());
                        }
                        break;
                    case 'Común - Primario':
                        if (!($userData['Centro']['id'] == 502 || $userData['Centro']['id'] == 505 || $userData['Centro']['id'] == 508 || $userData['Centro']['id'] == 509 || $userData['Centro']['id'] == 511)) {    
                            $this->Session->setFlash('Momentáneamente no se permiten las inscripciones 2020.', 'default', array('class' => 'alert alert-danger'));
                            $this->redirect($this->referer());
                        }
                        break;
                    case 'Común - Secundario':
                        if (!($userData['Centro']['id'] == 101 || $userData['Centro']['id'] == 506 || $userData['Centro']['id'] == 510 || $userData['Centro']['id'] == 526)) {
                            $this->Session->setFlash('Momentáneamente no se permiten las inscripciones 2020.', 'default', array('class' => 'alert alert-danger'));
                            $this->redirect($this->referer());
                        }
                        break;
                }
            }
            $this->Inscripcion->Ciclo->recursive = 0;
            $ciclos = $this->Inscripcion->Ciclo->findById($cicloId, 'nombre');
            $ciclo = substr($ciclos['Ciclo']['nombre'], -2);
            // Obtención de la división del curso.
            $this->loadModel('Curso');
            $this->Curso->recursive = 0;
            $this->Curso->Behaviors->load('Containable');
            $cursoIdArray = $this->request->data['Curso'];
            $cursoIdString = $cursoIdArray['Curso'];
            $divisionArray = $this->Curso->findById($cursoIdString, 'division');
            $divisionString = $divisionArray['Curso']['division'];
            // No continua la inscripcion si no se definió el centro y el curso.
            if (count($divisionArray)<=0) {
                $this->Session->setFlash('No definio la sección.', 'default', array('class' => 'alert alert-danger'));
                $this->redirect($this->referer());
            }
            /* INICIO: VERIFICACION DE DEFINICIÓN DE LA PERSONA */
            // Obtención del id de la persona.
            $personaId = $this->request->data['Persona']['persona_id'];
            // Si no está definido el id de persona, vuelve al formulario anterior.
            if (empty($personaId)) {
                $this->Session->setFlash('No se definio la persona.', 'default', array('class' => 'alert alert-danger'));
                $this->redirect($this->referer());
            }
            /* FIN */
            /* INICIO: GENERACIÓN DEL CÓDIGO DE INSCRIPCIÓN ÚNICO.*/
            //Obtención del DNI de la persona.
            $this->loadModel('Persona');
            $this->Persona->recursive = 0;
            $this->Persona->Behaviors->load('Containable');
            $persona = $this->Persona->findById($personaId,'id, documento_nro');
            $personaDni = $persona['Persona']['documento_nro'];
            // Obtención del tipo de inscripción actual. 
            $tipoInscripcionActual = $this->request->data['Inscripcion']['tipo_inscripcion'];
            // Obtención del nivel de servicio del centro.
            $this->loadModel('Centro');
            $this->Centro->recursive = 0;
            $this->Centro->Behaviors->load('Containable');
            // Generación del código específico.
            switch ($tipoInscripcionActual) {
                case 'Pase': // Obtención del número de pase para el ciclo actual.
                    //Sí el tipo de inscripción actual es PASE, genera un código específico.
                    $paseNro = 0;
                    //Busca el número de pase que corresponde al ciclo actual.
                    do { 
                        $paseNro += 1;
                        $codigoPrueba = $this->__getCodigoPase($ciclo, $personaDni, $paseNro);
                        $cuentaInscripcionPase = $this->Inscripcion->find('count',array(
                                    'contain' => false,
                                    'conditions' => array('Inscripcion.legajo_nro' => $codigoPrueba)
                                    ));
                    } while ($cuentaInscripcionPase != 0);
                    // Genera código actual y anterior de inscripción específico para PASE.
                    $codigoActualPase = $codigoPrueba;
                    $codigoAnteriorPase = $this->__getCodigoPase($ciclo, $personaDni, $paseNro-1);
                    // Generación del código de inscripción estándar. 
                    $codigoActual = $this->__getCodigo($ciclo, $personaDni);
                    // Comprobación de unicidad del código de inscripción estándar.
                    $existePersonaInscripta = $this->Inscripcion->find('first',array(
                        'contain' => false,
                        'conditions' => array(
                            'Inscripcion.legajo_nro' => $codigoActual)));
                    break;
                case 'Común':
                case 'Hermano de alumno regular':
                case 'Integración':
                case 'Situación social':
                case 'Estudiante de Intercambio':
                    $centroNivelServicioArray = $this->Centro->findById($userCentroId,'nivel_servicio');
                    $centroNivelServicio = $centroNivelServicioArray['Centro']['nivel_servicio'];
                    // Si el centro no es Maternal ni Especial ni del IPI, genera código estándar.
                    //Sino, genera un codigo específico para Maternal o Especial.
                    if ($centroNivelServicio != 'Maternal - Inicial' && $centroNivelServicio != 'Especial - Primario' && $centroNivelServicio != 'Común - Servicios complementarios') {
                        // Generación del código de inscripción estándar. 
                        $codigoActual = $this->__getCodigo($ciclo, $personaDni);
                    } else {
                        // Sí el centro es Maternal.
                        // Sino si el centro es Especial.
                        // Sino si el centro es el IPI.
                        if ($centroNivelServicio === 'Maternal - Inicial') {
                            // Generación del código de inscripción para Maternal. 
                            $codigoActual = $this->__getCodigoMaternal($ciclo, $personaDni);
                        } else if ($centroNivelServicio === 'Especial - Primario') {
                            // Generación del código de inscripción para Maternal. 
                            $codigoActual = $this->__getCodigoEspecial($ciclo, $personaDni);
                        } else if ($centroNivelServicio === 'Común - Servicios complementarios') {
                            // Generación del código de inscripción para el IPI. 
                            $codigoActual = $this->__getCodigoIPI($ciclo, $personaDni);
                        }  
                    }
                    // Comprobación de unicidad del código de inscripción estándar.
                    $existePersonaInscripta = $this->Inscripcion->find('first',array(
                        'contain' => false,
                        'conditions' => array(
                            'Inscripcion.legajo_nro' => $codigoActual)));                    
                    break;
                default:
                    # code...
                    break;
            }
            /* FIN */
            // Validación para el registro del código de inscripción específicos generado.
            switch ($tipoInscripcionActual) {
                case 'Pase':
                    //Si existe una inscripción del actual ciclo continúa el proceso de PASE. Sino indica mensaje y detiene el proceso.                        
                    if ($existePersonaInscripta) {
                        //Sí se trata del primer pase.
                        if ($paseNro-1 == 0) {
                            //Genera código de inscripción estandar actual. 
                            $codigoActual = $this->__getCodigo($ciclo, $personaDni);
                            //Obtención del estado actual de la inscripción estándar.
                            $inscripcionEstadoActualArray = $this->Inscripcion->findByLegajoNro($codigoActual, 'estado_inscripcion');
                            $inscripcionEstadoActual = $inscripcionEstadoActualArray['Inscripcion']['estado_inscripcion'];
                        } else {
                            //Obtención del estado actual de la inscripción por PASE.
                            $inscripcionEstadoActualArray = $this->Inscripcion->findByLegajoNro($codigoAnteriorPase, 'estado_inscripcion');
                            $inscripcionEstadoActual = $inscripcionEstadoActualArray['Inscripcion']['estado_inscripcion'];
                        }
                        //Si el estado de inscripción actual es BAJA, continúa con la nueva inscripción por pase. Sino indica mensaje y detiene el proceso.
                        if ($inscripcionEstadoActual == 'BAJA') {
                           $this->request->data['Inscripcion']['legajo_nro'] = $codigoActualPase;
                        } else {
                           $this->Session->setFlash(sprintf("El alumno debe estar dado de baja para realizar el pase."), 'default', array('class' => 'alert alert-danger'));
                           $this->redirect($this->referer());
                        }                       
                    } else {
                        $this->Session->setFlash(sprintf("El alumno debe registrar inscripción en este ciclo para realizar el pase."), 'default', array('class' => 'alert alert-danger'));
                        $this->redirect($this->referer());
                    }
                    break;
                case 'Común':
                case 'Hermano de alumno regular':
                case 'Integración':
                case 'Situación social':
                case 'Estudiante de Intercambio':
                    if (isset($existePersonaInscripta['Inscripcion']['legajo_nro'])) {
                        $this->Session->setFlash(sprintf("El alumno ya está inscripto para este ciclo en %s", $existePersonaInscripta['Centro']['nombre']), 'default', array('class' => 'alert alert-danger'));
                        $this->redirect($this->referer());
                    } else {
                        $this->request->data['Inscripcion']['legajo_nro'] = $codigoActual;
                    }
                    break;
                default:
                    $this->Session->setFlash(sprintf("Error al indicar el tipo de inscripción."), 'default', array('class' => 'alert alert-danger'));
                    break;
            }
            /* FIN */
            /* INICIO: VERIFICACIONES PARA CREACIÓN DEL ALUMNO (cualquiera sea el tipo de inscripción) */
            //Verifica si la persona se registró como alumno en el centro a inscribir.              
            // Obtención de ID del alumno del centro a inscribir.
            $inscripcionAlumnoId = $this->Inscripcion->find('first',array(
                'fields' => 'alumno_id',
                'contain' => false,
                'conditions' => array(
                    'Inscripcion.legajo_nro LIKE' => '%'.$personaDni.'%',
                    'Inscripcion.centro_id' => $userCentroId)
                )
            );            
            // Si el alumno no fue creado o no registra inscripción en el centro a inscribir,
            // crea el alumno y le asigna el id del centro a inscribir. 
            if (empty($inscripcionAlumnoId)) {
                // Crear alumno
                $this->Alumno->create();
                $insert = array(
                        'Alumno' => array(
                            'created' => '2017-09-08 12:01',
                            'persona_id' => $personaId,
                            'centro_id' => $userCentroId));
                $alumno = $this->Alumno->save($insert);
                if (!$alumno['Alumno']['id']) {
                    print_r("Error al registrar a la persona como alumno");
                    die;
                }
                $this->request->data['Inscripcion']['alumno_id'] = $alumno['Alumno']['id'];
            } else {
                // Asigna alumno_id ya creado.
                $this->request->data['Inscripcion']['alumno_id'] = $inscripcionAlumnoId['Inscripcion']['alumno_id'];
            }
            /* FIN */
            /* INICIO:  Definición del estado de la documentación según el nivel del centro.*/
            $userCentroNivel = $this->getUserCentroNivel($userCentroId);
            switch($userCentroNivel) {
                case 'Común - Inicial':
                case 'Común - Primario':
                case 'Maternal - Inicial':
                case 'Adultos - Primario':
                        if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['partida_nacimiento_alumno'] ==1) && ($this->request->data['Inscripcion']['certificado_vacunas'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='No corresponde' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                               $estadoDocumentacion = "COMPLETA";
                        } else {
                                $estadoDocumentacion = "PENDIENTE";
                        }
                    break;
                case 'Común - Secundario':
                        if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['partida_nacimiento_alumno'] ==1) && ($this->request->data['Inscripcion']['certificado_vacunas'] ==1) && ($this->request->data['Inscripcion']['certificado_septimo'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='No corresponde' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                                $estadoDocumentacion = "COMPLETA";
                        } else {
                                $estadoDocumentacion = "PENDIENTE";   
                        }                        
                    break;
                case 'Especial - Primario':
                        if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['partida_nacimiento_alumno'] ==1) && ($this->request->data['Inscripcion']['certificado_vacunas'] ==1) && ($this->request->data['Inscripcion']['certificado_septimo'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                                $estadoDocumentacion = "COMPLETA";
                        } else {
                                $estadoDocumentacion = "PENDIENTE";   
                        }                        
                    break;
                case 'Adultos - Primario':
                        if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='No corresponde' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                               $estadoDocumentacion = "COMPLETA";
                        } else {
                                $estadoDocumentacion = "PENDIENTE";
                        }
                    break;
                case 'Adultos - Secundario':
                        if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['certificado_septimo'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='No corresponde' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                                $estadoDocumentacion = "COMPLETA";
                        } else {
                                $estadoDocumentacion = "PENDIENTE";   
                        }                        
                    break;
                case 'Común - Servicios complementarios':
                    if($this->request->data['Inscripcion']['fotocopia_dni'] ==1) {
                            $estadoDocumentacion = "COMPLETA";
                    } else {
                            $estadoDocumentacion = "PENDIENTE";   
                    }                        
                break;

                default:
                       $estadoDocumentacion = "PENDIENTE";
            }
            //Se genera el estado y se deja en los datos que se intentaran guardar
            $this->request->data['Inscripcion']['estado_documentacion'] = $estadoDocumentacion;
            /* FIN */
            /* INICIO: Adecúa mensajes para los combobox dependientes según el tipo de inscripción. */
            switch($this->request->data['Inscripcion']['tipo_inscripcion']) {
                case 'Hermano de alumno regular':
                    $hermano  = $this->Alumno->findById($this->request->data['Inscripcion']['hermano_id']);
                    if (count($hermano) == 0) {
                        $this->Session->setFlash('No se localizo al hermano como alumno.', 'default', array('class' => 'alert alert-danger'));
                        $this->redirect($this->referer());
                    }
                    break;
                case 'Pase':
                    $centroOrigen = $this->Centro->findById($this->request->data['Inscripcion']['centro_origen_id']);
                    // Aca puede ir la logica de que nivel de servicio es necesario para guardar la inscripcion por pase
                    if (count($centroOrigen) == 0) {
                        $this->Session->setFlash('No se localizo el centro origen para el pase', 'default', array('class' => 'alert alert-danger'));
                        $this->redirect($this->referer());
                    }
                    break;
                }
            /* FIN */
            if ($this->Inscripcion->save($this->data)) {
                /* INICIO: ATUALIZACIÓN DE LA MATRÍCULAS Y VACANTES.
                *  Al registrarse una Inscripción sí es para el ciclo actual o para un agrupamiento 
                *  para el próximo ciclo, actualiza valores de matrícula y vacantes del curso correspondiente.
                */
                // Obtiene el ciclo id...
                $this->loadModel('Ciclo');
                $this->Ciclo->recursive = 0;
                $this->Ciclo->Behaviors->load('Containable');
                $cicloIdActual = $this->getActualCicloId();
                $cicloIdActualArray = $this->Ciclo->findById($cicloIdActual, 'id');
                $cicloIdActualString = $cicloIdActualArray['Ciclo']['id'];
                $cursoIdInt = $cursoIdString[0];
                $this->loadModel('CursosInscripcion');
                $this->CursosInscripcion->recursive = 0;
                $this->CursosInscripcion->Behaviors->load('Containable');
                $matriculaActual = $this->CursosInscripcion->query("
                    SELECT COUNT(*) AS `matriculas` 
                    FROM `siep`.`cursos_inscripcions` AS CursosInscripcion
                    LEFT JOIN `siep`.`inscripcions` AS Inscripcion on Inscripcion.id = CursosInscripcion.inscripcion_id       
                    WHERE 
                        CursosInscripcion.curso_id = $cursoIdInt AND 
                        Inscripcion.ciclo_id = $cicloIdActualString");
                $matriculaActual = $matriculaActual[0][0]['matriculas'];
                $this->Curso->id=$cursoIdString;
                $this->Curso->saveField("matricula", $matriculaActual);
                $plazasArray = $this->Curso->findById($cursoIdString, 'plazas');
                $plazasString = $plazasArray['Curso']['plazas'];
                $vacantesActual = $plazasString - $matriculaActual;
                $this->Curso->saveField("vacantes", $vacantesActual);
                /* FIN */
                $inserted_id = $this->Inscripcion->id;
                    /*
                     * __ LINEAS PARA DEBUG __
                    echo '<pre>';
                    print_r($inserted_id);
                    print_r($this->request->data);
                    echo '</pre>';
                    die;
                    */
                $this->Session->setFlash('La inscripcion ha sido grabada.', 'default', array('class' => 'alert alert-success'));
                $this->redirect(array('action' => 'view', $inserted_id));
            } else {
                    $this->Session->setFlash('La inscripcion no fue grabada. Intente nuevamente.', 'default', array('class' => 'alert alert-danger'));
            }
        }
    }

	public function edit($id = null) {
        $this->Inscripcion->recursive = 0;
        if (!$id && empty($this->data)) {
			$this->Session->setFlash('Inscripcion no valida.', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action' => 'index'));
		}
        // Obtención de estados de inscripción anterior y actual.
        $estadoInscripcionAnteriorArray = $this->Inscripcion->findById($id, 'estado_inscripcion');
        $estadoInscripcionAnterior = $estadoInscripcionAnteriorArray['Inscripcion']['estado_inscripcion'];
        // Obtención del registro relación curso-inscripción correspondiente a la inscripción.
        $this->loadModel('CursosInscripcion');
        $this->CursosInscripcion->recursive = 0;
        $this->CursosInscripcion->Behaviors->load('Containable');
        $cursoInscripcion  = $this->CursosInscripcion->find('first',[
            'contains' => false,
            'conditions' => ['Inscripcion.id'=> $id]
        ]);
        // Obtención del alumno correspondiente a la inscripción.
        $this->loadModel('Alumno');
        $this->Alumno->recursive = 0;
        $this->Alumno->Behaviors->load('Containable');
        $alumno = $this->Alumno->find('first',[
            'contains' => false,
            'conditions' => ['Alumno.id'=> $cursoInscripcion['Inscripcion']['alumno_id']]
        ]);
        // En este punto tengo al alumno y a la persona relacionadas al id de inscripcion.
        $alumnoId = $alumno['Alumno']['id'];
        $personaId  = $alumno['Persona']['id'];
        // Obtención del ciclo actual.
        $hoyArray = getdate();
        $this->loadModel('Ciclo');
        $this->Ciclo->recursive = 0;
        $this->Ciclo->Behaviors->load('Containable');
        $cicloActual = $this->Ciclo->find('first', array(
            'contain' => false,
            'conditions' => array('nombre' => $hoyArray['year'])
        ));
        $cicloActual = array_pop($cicloActual);
        // Submit de formulario
    	if (!empty($this->data)) {
            //abort if cancel button was pressed
            if(isset($this->params['data']['cancel'])){
                $this->Session->setFlash('Los cambios no fueron guardados. Edición cancelada.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect( array( 'action' => 'index' ));
		    }
            /* INICIO:  Definición del estado de la documentación según el nivel del centro.*/
            $CentroId = $this->Inscripcion->findById($id, 'centro_id');
            $CentroIdString = $CentroId['Inscripcion']['centro_id'];
            $CentroNivel = $this->getUserCentroNivel($CentroIdString);
            switch($CentroNivel) {
                case 'Común - Inicial':
                case 'Común - Primario':
                case 'Maternal - Inicial':
                case 'Adultos - Primario':
                    if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['partida_nacimiento_alumno'] ==1) && ($this->request->data['Inscripcion']['certificado_vacunas'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='No corresponde' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                               $estadoDocumentacion = "COMPLETA";
                        } else {
                                $estadoDocumentacion = "PENDIENTE";
                        }
                    break;
                case 'Común - Secundario':
                        if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['partida_nacimiento_alumno'] ==1) && ($this->request->data['Inscripcion']['certificado_vacunas'] ==1) && ($this->request->data['Inscripcion']['certificado_septimo'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='No corresponde' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                                $estadoDocumentacion = "COMPLETA";
                        } else {
                                $estadoDocumentacion = "PENDIENTE";   
                        }                        
                    break;
                case 'Especial - Primario':
                    if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['partida_nacimiento_alumno'] ==1) && ($this->request->data['Inscripcion']['certificado_vacunas'] ==1) && ($this->request->data['Inscripcion']['certificado_septimo'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                            $estadoDocumentacion = "COMPLETA";
                    } else {
                            $estadoDocumentacion = "PENDIENTE";   
                    }                        
                break;
                case 'Adultos - Primario':
                    if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='No corresponde' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                               $estadoDocumentacion = "COMPLETA";
                        } else {
                                $estadoDocumentacion = "PENDIENTE";
                        }
                    break;
                case 'Adultos - Secundario':
                    if(($this->request->data['Inscripcion']['fotocopia_dni'] ==1) && ($this->request->data['Inscripcion']['certificado_septimo'] ==1) && ($this->request->data['Inscripcion']['cud_estado'] =='Actualizado' || $this->request->data['Inscripcion']['cud_estado'] =='No corresponde' || $this->request->data['Inscripcion']['cud_estado'] =='NULL')) {
                            $estadoDocumentacion = "COMPLETA";
                    } else {
                            $estadoDocumentacion = "PENDIENTE";   
                    }                        
                break;
                case 'Común - Servicios complementarios':
                    if($this->request->data['Inscripcion']['fotocopia_dni'] ==1) {
                            $estadoDocumentacion = "COMPLETA";
                    } else {
                            $estadoDocumentacion = "PENDIENTE";   
                    }                        
                break;
                
                default:
                       $estadoDocumentacion = "PENDIENTE";
            }
            //Se genera el estado y se deja en los datos que se intentaran guardar
            $this->request->data['Inscripcion']['estado_documentacion'] = $estadoDocumentacion;
            /*FIN*/
            /* INICIO: Se define el id del centro en función del rol.*/
            //Se obtiene el rol del usuario
            $userRole = $this->Auth->user('role');
            switch($userRole) {
                case 'superadmin':
                case 'usuario':
                    // Usa el centro especificado en el formulario
                    $userCentroId = $this->request->data['Inscripcion']['centro_id'];
                break;
                case 'admin':
                    // Usa el centro definido para el usuario
                    $userCentroId = $this->getUserCentroId();
                    $this->request->data['Inscripcion']['centro_id'] = $userCentroId ;
                break;
            }
            /* FIN */
            // Obtiene la división del curso "Seleccionado"
            $cursoIdArray = $this->request->data['Curso'];
            $cursoIdString = $cursoIdArray['Curso'];
            $this->loadModel('Curso');
            $this->Curso->recursive = 0;
            $this->Curso->Behaviors->load('Containable');
            $nuevoCurso = $this->Curso->findById($cursoIdString);
            // Es necesario tener una seccion definida para la edicion
            if (count($nuevoCurso) <= 0 || !is_numeric($nuevoCurso['Curso']['id'])) {
                $this->Session->setFlash('No definio la sección.', 'default', array('class' => 'alert alert-danger'));
                $this->redirect($this->referer());
            }
            $cursoIdAnterior = $cursoInscripcion['Curso']['id'];
            $cursoIdNew = $nuevoCurso['Curso']['id'];
            // Sí los id de los cursos son diferentes, se trata de un cambio de sección.
            if ($cursoIdNew != $cursoIdAnterior) {
                // Actualiza los valores de matrícula y vacantes tanto de la sección origen como de la sección destino.
                // Comienza por el curso anterior...
                $matricula = $this->Inscripcion->CursosInscripcion->find('count', array(
                    'fields'=>array(
                        'CursosInscripcion.*',
                        'Inscripcion.*'
                    ),
                    //'contain'=> false,
                    'conditions'=>array(
                        'CursosInscripcion.curso_id'=>$cursoIdAnterior,
                        'Inscripcion.ciclo_id'=>$cicloActual['id'],
                )));
                $matriculaActual = $matricula - 1;
                $this->Curso->id = $cursoIdAnterior;
                $this->Curso->saveField("matricula", $matriculaActual);
                $plazasArray = $this->Curso->findById($cursoIdAnterior, 'plazas');
                $plazasString = $plazasArray['Curso']['plazas'];
                $vacantesActual = $plazasString - $matriculaActual;
                $this->Curso->saveField("vacantes", $vacantesActual);
                // Continúa por el curso actual...
                $matricula = $this->Inscripcion->CursosInscripcion->find('count', array(
                    'fields'=>array(
                        'CursosInscripcion.*',
                        'Inscripcion.*'
                    ),
                    //'contain'=> false,
                    'conditions'=>array(
                        'CursosInscripcion.curso_id'=>$cursoIdNew,
                        'Inscripcion.ciclo_id'=>$cicloActual['id'],
                )));
                $matriculaActual = $matricula + 1;
                $this->Curso->id=$cursoIdNew;
                $this->Curso->saveField("matricula", $matriculaActual);
                $plazasArray = $this->Curso->findById($cursoIdNew, 'plazas');
                $plazasString = $plazasArray['Curso']['plazas'];
                $vacantesActual = $plazasString - $matriculaActual;
                $this->Curso->saveField("vacantes", $vacantesActual);
            }
            /* FIN: PASE INTERNO (ENTRE CURSOS DE UNA MISMA INSTITUCIÓN) */
            
            /* INICIO: COMPROBACIÓN DE DATOS DE BAJA INGRESADOS */
            // Si el estado de inscripción es BAJA debe ingresar al menos FECHA DE BAJA.
            if (($this->request->data['Inscripcion']['estado_inscripcion'] == 'BAJA') && ($this->request->data['Inscripcion']['fecha_baja'] == '')) {
                $this->Session->setFlash('Ingresó BAJA en el campo "Estado de la inscripción" del PASO 1. En ese caso debe ingresar la "Fecha de Baja" en el PASO 2.', 'default', array('class' => 'alert alert-danger'));
                $this->redirect($this->referer());
            }
            // Si se ingresaron datos de baja, el estado de inscripción debe ser BAJA.
            if (($this->request->data['Inscripcion']['fecha_baja'] != '') && ($this->request->data['Inscripcion']['estado_inscripcion'] != 'BAJA')) {
                $this->Session->setFlash('Ingresó FECHA DE BAJA en el PASO 2. En ese caso debe indicar BAJA en el campo "Estado de la inscripción" del PASO 1.', 'default', array('class' => 'alert alert-danger'));
                $this->redirect($this->referer());
            }
            /* FIN: COMPROBACIÓN DE DATOS DE BAJA INGRESADOS */           
            /* INICIO: BAJA DE UN ALUMNO (DE UN CURSO DE UNA INSTITUCIÓN)
            *  Sí cambia el estado de inscripción a BAJA.
            *  Actualiza valores de matrícula y vacantes del curso origen.
            */
            $estadoInscripcionActual = $this->request->data['Inscripcion']['estado_inscripcion'];
            // Sí el estado de inscripción paso a BAJA.
            if (($estadoInscripcionAnterior != $estadoInscripcionActual) || ($estadoInscripcionActual == 'BAJA')) {
                // Actualiza los valores de matrícula y vacantes de la sección origen.
                $matricula = $this->Inscripcion->CursosInscripcion->find('count', array(
                    'fields'=>array(
                        'CursosInscripcion.*',
                        'Inscripcion.*'
                    ),
                    //'contain'=> false,
                    'conditions'=>array(
                        'CursosInscripcion.curso_id'=>$cursoIdAnterior,
                        'Inscripcion.ciclo_id'=>$cicloActual['id'],
                )));
                $matriculaActual = $matricula - 1;
                $this->Curso->id = $cursoIdAnterior;
                $this->Curso->saveField("matricula", $matriculaActual);
                $plazasArray = $this->Curso->findById($cursoIdAnterior, 'plazas');
                $plazasString = $plazasArray['Curso']['plazas'];
                $vacantesActual = $plazasString - $matriculaActual;
                $this->Curso->saveField("vacantes", $vacantesActual);
            }
            /* FIN: BAJA DE UN ALUMNO (DE UN CURSO DE UNA INSTITUCIÓN) */
            // Quito estos campos de la modificacion, este dato no se modifica
            unset($this->request->data['Inscripcion']['alumno_id']);
            unset($this->request->data['Inscripcion']['ciclo_id']);
            $this->request->data['Inscripcion']['legajo_nro'] = $cursoInscripcion['Inscripcion']['legajo_nro'];
            $this->request->data['Inscripcion']['id'] = $id;
            $this->request->data['Inscripcion']['usuario_id'] = $this->Auth->user('id');
            $this->Inscripcion->set($this->request->data);
            // ACA INTENTA HACER UN INSERT., CUANDO DEBERIA HACER UN UPDATE
            if ($this->Inscripcion->save()) {
                $this->Session->setFlash('La inscripcion ha sido grabada.', 'default', array('class' => 'alert alert-success'));
				$inserted_id = $this->Inscripcion->id;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
                //debug( $this->Inscripcion->invalidFields() );
                //die;
				$this->Session->setFlash('La inscripcion no fue grabada. Intente nuevamente.', 'default', array('class' => 'alert alert-danger'));
            }
    	}
        //End submit de formulario
        //Genera variables para forzar tildes en la vista.
        $tildeDocumento = $this->Inscripcion->findById($id, 'fotocopia_dni');
        $tildeDocumentoString = $tildeDocumento['Inscripcion']['fotocopia_dni'];
        $tildePartida = $this->Inscripcion->findById($id, 'partida_nacimiento_alumno');
        $tildePartidaString = $tildePartida['Inscripcion']['partida_nacimiento_alumno'];
        $tildeVacunas = $this->Inscripcion->findById($id, 'certificado_vacunas');
        $tildeVacunasString = $tildeVacunas['Inscripcion']['certificado_vacunas'];
        $tildeSeptimo = $this->Inscripcion->findById($id, 'certificado_septimo');
        $tildeSeptimoString = $tildeSeptimo['Inscripcion']['certificado_septimo'];
        //Genera variable para forzar lectura del ciclo de la inscripción.
        $cicloInscripcionId = $this->Inscripcion->findById($id, 'ciclo_id');
        $cicloInscripcionIdString = $cicloInscripcionId['Inscripcion']['ciclo_id'];
        $cicloInscripcionNombre = $this->Ciclo->findById($cicloInscripcionIdString, 'nombre');
        $cicloInscripcionNombreString = $cicloInscripcionNombre['Ciclo']['nombre'];
        /*Verifica sí se trata de una inscripción con denominación SIN VACANTE.*/
        //Obtiene el string de la inscripción.
        $inscripcionLegajoNroArray = $this->Inscripcion->findById($id, 'legajo_nro');
        $inscripcionLegajoNroString = $inscripcionLegajoNroArray['Inscripcion']['legajo_nro'];
        //Obtiene la denominación 'SINVACANTE'
        $sinVacante = substr($inscripcionLegajoNroString, -12, 10);
        //Fuerza guardar Datos de Baja.
        $fechaBajaArray = $this->Inscripcion->findById($id, 'fecha_baja');    
        $fechaBaja = $fechaBajaArray['Inscripcion']['fecha_baja'];
        $bajaTipoArray = $this->Inscripcion->findById($id, 'tipo_baja');    
        $bajaTipo = $bajaTipoArray['Inscripcion']['tipo_baja'];
        $bajaTipoArray = $this->Inscripcion->findById($id, 'tipo_baja');    
        $bajaTipo = $bajaTipoArray['Inscripcion']['tipo_baja'];
        $bajaMotivoArray = $this->Inscripcion->findById($id, 'motivo_baja');    
        $bajaMotivo = $bajaMotivoArray['Inscripcion']['motivo_baja'];
        //Fuerza guardar Datos de Egreso.
        $fechaEgresoArray = $this->Inscripcion->findById($id, 'fecha_egreso');    
        $fechaEgreso = $fechaEgresoArray['Inscripcion']['fecha_egreso'];
        $fechaEmisionTituloArray = $this->Inscripcion->findById($id, 'fecha_emision_titulo');    
        $fechaEmisionTitulo = $fechaEmisionTituloArray['Inscripcion']['fecha_emision_titulo'];
        $notaFinalArray = $this->Inscripcion->findById($id, 'fecha_nota');    
        $notaFinal = $notaFinalArray['Inscripcion']['fecha_nota'];
        $actaNroArray = $this->Inscripcion->findById($id, 'acta_nro');    
        $actaNro = $actaNroArray['Inscripcion']['acta_nro'];
        $libroNroArray = $this->Inscripcion->findById($id, 'libro_nro');    
        $libroNro = $libroNroArray['Inscripcion']['libro_nro'];
        $folioNroArray = $this->Inscripcion->findById($id, 'folio_nro');    
        $folioNro = $folioNroArray['Inscripcion']['folio_nro'];
        $tituloNroArray = $this->Inscripcion->findById($id, 'titulo_nro');    
        $tituloNro = $tituloNroArray['Inscripcion']['titulo_nro'];
        //Fuerza guardar observaciones.
        $obsArray = $this->Inscripcion->findById($id, 'observaciones');    
        $obs = $obsArray['Inscripcion']['observaciones'];
        //Fuerza guardar el estado del CUD (sólo para modalidad ESPECIAL).
        $cudEstadoArray = $this->Inscripcion->findById($id, 'cud_estado');    
        $cudEstado = $cudEstadoArray['Inscripcion']['cud_estado'];
        //Envia datos a la vista.
        $this->set(compact('cursoInscripcion','alumno', 'personaId', 'estadoInscripcionAnteriorArray', 'tildeDocumentoString', 'tildePartidaString', 'tildeVacunasString', 'tildeSeptimoString', 'cicloInscripcionIdString', 'cicloInscripcionNombreString', 'sinVacante', 'fechaBaja', 'bajaTipo', 'bajaMotivo', 'fechaEgreso', 'fechaEmisionTitulo', 'notaFinal', 'actaNro', 'libroNro', 'folioNro', 'tituloNro', 'obs', 'cudEstado'));
    }

    // Implementa la ANULACIÓN y no el BORRADO de las INSCRIPCIONES.
    function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Id no valido para la inscripción', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action'=>'index'));
		} else {

        }
        $this->Inscripcion->id = $id;
        if (!$this->Inscripcion->exists()) {
            $this->Session->setFlash('ID inválido');
            //$this->redirect(array('action'=>'index'));
            $this->redirect($this->referer());
        }
        if ($this->Inscripcion->saveField('estado_inscripcion', 'ANULADA')) {
            //Obtención del legajo de la inscripción a anular.
            $inscripcionLegajoArray = $this->Inscripcion->findById($id, 'legajo_nro');
            $inscripcionLegajo = $inscripcionLegajoArray['Inscripcion']['legajo_nro'];
            //Agregación del prefijo "-ANULADA" al legajo.
            $codigoActual = $this->__getCodigoAnulada($inscripcionLegajo);
            //Actualización del legajo.
            $this->Inscripcion->saveField('legajo_nro', $codigoActual);
            $this->Session->setFlash('La inscripción ha sido ANULADA', 'default', array('class' => 'alert alert-success'));
            //$this->redirect(array('action' => 'index'));
            $this->redirect($this->referer());
        }
		$this->Session->setFlash('La inscripción no fue ANULADA', 'default', array('class' => 'alert alert-danger'));
        //$this->redirect(array('action' => 'index'));
        $this->redirect($this->referer());
	}

	//Métodos privados
	private function __lists(){
	    $this->loadModel('User');
        $this->User->recursive = 0;
        $this->User->Behaviors->load('Containable');
        $userRol = $this->Auth->user('role');
        // Carga en el combobox el Ciclo actual y uno posterior sí lo hubiera.        
        $this->loadModel('Ciclo');
        $this->Ciclo->recursive = 0;
        $this->Ciclo->Behaviors->load('Containable');
        $cicloIdActual = $this->getActualCicloId();
        $cicloIdActualArray = $this->Ciclo->findById($cicloIdActual, 'id');
        $cicloIdActualString = $cicloIdActualArray['Ciclo']['id'];
        $cicloIdUltimo = $this->getLastCicloId();
        $cicloIdUltimoArray = $this->Ciclo->findById($cicloIdUltimo, 'id');
        $cicloIdUltimoString = $cicloIdUltimoArray['Ciclo']['id'];
        /* Sí no es "superadmin" ve en combobox de ciclos, el actual y el posterior. 
        *  Sino ve todos los ciclos.
        */
        if ($userRol != 'superadmin') {
			$ciclos = $this->getTwoLastCicloNombres($cicloIdActualString, $cicloIdUltimoString);
		} else {
            $ciclos = $this->Ciclo->find('list', array('fields'=>array('id','nombre'), 'contain'=>false));
        }        
        $this->Inscripcion->Centro->recursive = 0;
        $centros = $this->Inscripcion->Centro->find('list');
		/* Sí es "superadmin" ve combobox con todos los cursos, 
        *  Sino sí es usuario de Inicial y Primaria, ve los propios de ambos niveles,
        *  Sino sí es usuario de otro nivel ve los correspondiente.
        */
		$userCentroId = $this->getUserCentroId();
        $userCentroNivel = $this->getUserCentroNivel($userCentroId);
        $nivelCentro = $this->Inscripcion->Centro->find('list', array('fields'=>array('nivel_servicio'), 'contain'=>false, 'conditions'=>array('id'=>$userCentroId)));
        $this->Inscripcion->Curso->recursive = 0;
        if ($userRol == 'superadmin') {
			$cursos = $this->Inscripcion->Curso->find('list', array('fields'=>array('id','nombre_completo_curso'), 'contain'=>false));
		} else if (($userRol === 'usuario') && ($nivelCentro === 'Común - Inicial - Primario')) {
            $nivelCentroId = $this->Inscripcion->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>array('Común - Inicial', 'Común - Primario'))));
            $cursos = $this->Inscripcion->Curso->find('list', array('fields'=>array('id','nombre_completo_curso'), 'contain'=>false, 'conditions'=>array('centro_id'=>$nivelCentroId, 'status'=> '1')));
        } else if ($userRol === 'usuario') {
            $nivelCentroId = $this->Inscripcion->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>$nivelCentro)));
            $cursos = $this->Inscripcion->Curso->find('list', array('fields'=>array('nombre_completo_curso'), 'contain'=>false, 'conditions'=>array('centro_id'=>$nivelCentroId, 'status' => '1')));
        } else if ($userRol == 'admin') {
			$userCentroId = $this->getUserCentroId();
			$cursos = $this->Inscripcion->Curso->find('list', array('fields'=>array('id','nombre_completo_curso'), 'contain'=>false, 'conditions'=>array('centro_id'=>$userCentroId, 'status' => '1')));
		}
		/* Sí es "superadmin" o "usuario" ve combobox con todos los alumnos.
    	*  Sino ve los propios del centro. (INICIO) */
		$userCentroId = $this->getUserCentroId();
		$userRole = $this->Auth->user('role');
		$this->loadModel('Alumno');
        $this->Alumno->recursive = 0;
        $this->Alumno->Behaviors->load('Containable');
        if ($this->Auth->user('role') === 'admin') {
	       	$personaId = $this->Alumno->find('list', array('fields'=>array('persona_id'), 'contain'=>false, 'conditions'=>array('centro_id'=>$userCentroId)));
		} else if ($userRole === 'usuario') {
            $this->loadModel('Centro');
            $this->Centro->recursive = 0;
            $this->Alumno->Behaviors->load('Containable');
            $nivelCentro = $this->Centro->find('list', array('fields'=>array('nivel_servicio'), 'contain'=>false, 'conditions'=>array('id'=>$userCentroId)));
            $nivelCentroId = $this->Centro->find('list', array('fields'=>array('id'), 'contain'=>false, 'conditions'=>array('nivel_servicio'=>$nivelCentro)));
            $personaId = $this->Inscripcion->find('list', array('fields'=>array('alumno_id'), 'contain'=>false, 'conditions'=>array('centro_id'=>$nivelCentroId)));
        } else {
			//Sí es superadmin
			$personaId = $this->Alumno->find('list', array('fields'=>array('persona_id'), 'contain'=>false));
		}
		/* FIN */
        $this->set(compact('ciclos', 'centros', 'cursos', 'materias', 'empleados', 'cicloIdActual','cicloIdUltimo', 'userCentroNivel'));
	}

    /* INICIO: FUNCIONES PARA GENERACIÓN DE CÓDIGOS DE INSCRIPCIÓN ESPECÍFICOS */

    private function __getCodigo($ciclo, $personaDocString){
        $legajo = $personaDocString."-".$ciclo;
        return $legajo;
    }

    private function __getCodigoPase($ciclo, $personaDocString, $paseNro){
        $legajo = $personaDocString."-".$ciclo."-"."PASE"."_".$paseNro;
        return $legajo;
    }

    private function __getCodigoMaternal($ciclo, $personaDocString){
        $legajo = $personaDocString."-".$ciclo."-"."MATERNAL";
        return $legajo;
    }

    private function __getCodigoEspecial($ciclo, $personaDocString){
        $legajo = $personaDocString."-".$ciclo."-"."ESPECIAL";
        return $legajo;
    }

    private function __getCodigoAnulada($inscripcionLegajo){
        $legajo = $inscripcionLegajo."-"."ANULADA";
        return $legajo;
    }

    private function __getCodigoIPI($ciclo, $personaDocString){
        $legajo = $personaDocString."-".$ciclo."-"."IPI";
        return $legajo;
    }
    
    /* FIN */
}
?>