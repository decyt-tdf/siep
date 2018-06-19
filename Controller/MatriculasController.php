<?php
App::uses('AppController', 'Controller');

class MatriculasController extends AppController
{
    // Permite agregar el Helper de Siep a las vistas
    public $helpers = array('Siep');

    public function beforeFilter()
    {
        parent::beforeFilter();
        
        /* ACCESOS SEGÚN ROLES DE USUARIOS (INICIO).
        *Si el usuario tiene un rol de superadmin le damos acceso a todo. Si no es así (se trata de un usuario "admin o usuario") tendrá acceso sólo a las acciones que les correspondan.
        */

        // Importa el Helper de Siep al controlador es accesible mediante $this->Siep
        App::import('Helper', 'Siep');
        $this->Siep= new SiepHelper(new View());

        /*
        --------------------------------------------------------------
                   Ejemplo de verificacion de rol con el Helper
        --------------------------------------------------------------
        if($this->Siep->isAdmin() || $this->Siep->isSuperAdmin()) {
            $this->Auth->allow();
        }

        if($this->Siep->isUsuario()) {
            $this->Auth->allow('index', 'view', 'requestDatatable');
        }
       */

        switch ($this->Auth->user('role')) {
            case 'superadmin':
            case 'admin':
                $this->Auth->allow();
            break;
            case 'usuario':
            case 'viewer':
                $this->Auth->allow('index', 'view', 'requestDatatable');
            break;
        }
    }

    public function index() {
        //$this->User->recursive = 0;
        $this->loadModel('Curso');
        $this->paginate = array(
            'contain' => array('Centro'),
            'limit' => 10,
            'conditions' => array('Curso.division !=' => '', 'Curso.status =' => 1),
            'order' => array('Curso.centro_id' => 'asc' )
        );
        $this->redirectToNamed();
        $conditions = array();
        if (!empty($this->params['named']['ciclo_id'])) {

            // Condicion para filtrar el ciclo_id
            //$conditions['CursosInscripcions.ciclo_id ='] = $this->params['named']['ciclo_id'];
        }
        if(!empty($this->params['named']['centro_id']))
        {
            $conditions['Centro.id = '] = $this->params['named']['centro_id'];
        }
        if (!empty($this->params['named']['anio'])) {
            $conditions['Curso.anio ='] = $this->params['named']['anio'];
        }
        if (!empty($this->params['named']['division'])) {
            $conditions['Curso.division ='] = $this->params['named']['division'];
        }
        $userCentroId = $this->getUserCentroId();
        // Cargo todos los cilos de la base de datos
        $this->loadModel('Ciclo');
        $comboCiclo = $this->Ciclo->find('list', array('fields'=>array('id', 'nombre')));

        $cicloIdUltimo = $this->getLastCicloId();
        $cicloIdActual = $this->getActualCicloId();

        if($this->Siep->isAdmin()) {
            $conditions['Curso.centro_id'] = $userCentroId;
            $matriculas = $this->paginate('Curso',$conditions);
            $comboAnio = $this->Curso->find('list', array(
                'recursive'=> -1,
                'fields'=> array('Curso.anio','Curso.anio'),
                'conditions'=>array('centro_id'=>$userCentroId)
            ));
            $comboDivision = $this->Curso->find('list', array(
                'recursive'=> -1,
                'fields'=> array('Curso.division','Curso.division'),
                'conditions'=>array('centro_id'=>$userCentroId)
            ));

//            $comboDivision = $this->Curso->query("SELECT `Curso`.`division` FROM `siep`.`cursos` AS `Curso` WHERE `centro_id` = ".$userCentroId." GROUP BY division");

//            $comboDivision = $this->Curso->find('list', array(
//                'recursive'=> -1,
//                'fields'=> 'division',
//                'conditions'=>array('centro_id'=>$userCentroId)
//            ));
        }

        if($this->Siep->isUsuario()) {
            $nivelCentroArray = $this->Curso->Centro->findById($userCentroId, 'nivel_servicio');
            $nivelCentroString = $nivelCentroArray['Centro']['nivel_servicio'];
            if ($nivelCentroString === 'Común - Inicial - Primario') {
                $nivelCentroId = $this->Curso->Centro->find('list', array(
                    'fields' => array('id'),
                    'conditions' => array(
                        'nivel_servicio' => array('Común - Inicial', 'Común - Primario')
                    )
                ));
                $conditions['Curso.centro_id'] = $nivelCentroId;
                $matriculas = $this->paginate('Curso', $conditions);
            } else {
                $nivelCentroId = $this->Curso->Centro->find('list', array(
                    'fields' => array('id'),
                    'conditions' => array('nivel_servicio' => $nivelCentroString)
                ));
                $conditions['Curso.centro_id'] = $nivelCentroId;
                $matriculas = $this->paginate('Curso', $conditions);
            }

            $comboAnio = $this->Curso->find('list', array(
                'recursive'=> -1,
                'fields'=> array('Curso.anio','Curso.anio'),
                'conditions'=>array('centro_id'=>$nivelCentroId)
            ));
            $comboDivision = $this->Curso->find('list', array(
                'recursive'=> -1,
                'fields'=> array('Curso.division','Curso.division'),
                'conditions'=>array('centro_id'=>$nivelCentroId)
            ));
        }

        if(($this->Siep->isSuperAdmin()) || ($this->Siep->isViewer())) {
            $matriculas = $this->paginate('Curso',$conditions);

            $comboAnio = $this->Curso->find('list', array(
                'recursive'=> -1,
                'fields'=> array('Curso.anio','Curso.anio')
            ));
            $comboDivision = $this->Curso->find('list', array(
                'recursive'=> -1,
                'fields'=> array('Curso.division','Curso.division')
            ));
        }

        $this->set(compact('matriculas','comboAnio','comboDivision','comboCiclo','cicloIdUltimo','cicloIdActual'));
  	}

    /*
     * Este metodo se encarga de listar todas las inscripciones realizadas, y las agrupa segun el siguiente filtro
     *
     * Inscripcion en -> ciclo_id, centro_id, curso.anio, curso.division, curso.turno
     *
     * El filtro obtiene la cantidad de matriculas y sus plazas, lo que permite obtener las vacantes.
     *
     * Esta consulta a su vez actualiza los datos en la tabla Cursos segun el Curso.id
     *
     */
    public function recuento()
    {
        // Antes que nada devuelvo a cero todas las matriculas, y las vacantes son el total de plazas
        $this->loadModel('Cursos');
        $this->Cursos->updateAll(
            array(
                'Cursos.matricula' => 0,
                'Cursos.vacantes' => 'Cursos.plazas'
            )
        );

        // Mande la query de pecho, la verdad no me quise complicar con el ORM de cake.
        $this->loadModel('Inscripcions');
        $lista = $this->Inscripcions->query("
            select 
            
            ins.ciclo_id,
            ins.centro_id,            
            curso.id,
            curso.anio,
            curso.division,
            curso.tipo,
            curso.turno,
            curso.plazas,
            COUNT(ins.id) as matriculas,
            (
              curso.plazas - COUNT(ins.id)
            ) as vacantes
            
            
            FROM inscripcions ins
            
            inner join ciclos ci on ci.id = ins.ciclo_id
            inner join centros ce on ce.id = ins.centro_id
            inner join cursos_inscripcions cui on cui.inscripcion_id = ins.id
            inner join cursos curso on curso.id = cui.curso_id
            
            where
            
            ci.nombre = 2018 and
            ins.estado_inscripcion = 'CONFIRMADA' 
            
            group by 

            ins.ciclo_id,            
            ins.centro_id,            
            curso.id,
            curso.anio,
            curso.division,
            curso.turno,
            curso.plazas
        ");

        foreach($lista as $item)
        {
            $matriculas = $item[0]['matriculas'];
            $vacantes = $item[0]['vacantes'];

            $update = array(
                'vacantes' => $vacantes,
                'matricula' => $matriculas
            );

            $this->Cursos->id = $item['curso']['id'];
            $this->Cursos->save($update);
        }

        $this->autoRender = false;
        $this->redirect(array('action' => 'index'));

//        $this->response->type('json');

//        $json = json_encode($lista);
//        $this->response->body($lista);

    }

/*    public function requestDatatable()
    {
        $conditions = [];


        $query = $this->request->data;

        $columns = $query['columns'];
        $order = $query['order'][0];

        $orderColumn = $columns[$order['column']]['data'];
        $orderColumnDir = $order['dir'];

        $start = $query['start'];
        if($start<=0) { $start = 1; }
        $limite = $query['length'];
        $search = $query['search'];

        $this->paginate = array(
            'contain' => array('Centro'),
            'limit' => $limite,
            'order' => array($orderColumn => $orderColumnDir )
        );

        foreach ($columns as $index => $item)
        {
            $columna = $item['data'];
            $busqueda = $item['search']['value'];
            if(!empty($busqueda))
            {
                switch($columna) {
                    case 'Centro.sigla':
                        $conditions[$columna.' LIKE'] = '%'.$busqueda.'%';
                    break;
                    default:
                        $conditions[$columna] = $busqueda;
                    break;
                }
            }
        }

        $this->loadModel('Curso');
        $userCentroId = $this->getUserCentroId();
        $userRole = $this->Auth->user('role');
        // Modifique las rutas de ROLES al formato SWITCH que es mas llevadero que los IF
        switch ($userRole) {
            case 'admin':
                $conditions['Curso.centro_id'] = $userCentroId;
                $result = $this->paginate('Curso',$conditions);
            break;
            case 'usuario':
                $userCentroId = $this->getUserCentroId();
                $this->loadModel('Curso');
                $nivelCentroArray = $this->Curso->Centro->findById($userCentroId, 'nivel_servicio');
                $nivelCentroString = $nivelCentroArray['Centro']['nivel_servicio'];
                if ($nivelCentroString === 'Común - Inicial - Primario') {
                    $nivelCentroId = $this->Curso->Centro->find('list', array('fields'=>array('id'), 'conditions'=>array('nivel_servicio'=>array('Común - Inicial', 'Común - Primario'))));

                    $conditions['Curso.centro_id'] = $nivelCentroId;
                    $result = $this->paginate('Curso',$conditions);
                } else  {
                    $nivelCentroId = $this->Curso->Centro->find('list', array('fields'=>array('id'), 'conditions'=>array('nivel_servicio'=>$nivelCentro)));

                    $conditions['Curso.centro_id'] = $nivelCentroId;
                    $result = $this->paginate('Curso',$conditions);
                }
          break;
            case 'superadmin':
                $result = $this->paginate('Curso',$conditions);
            break;
        }
        $result = [
        	"data" => $result
        ];
        $this->autoRender = false;
        $this->response->type('json');
        $json = json_encode($result);
        $this->response->body($json);
    }*/
}
