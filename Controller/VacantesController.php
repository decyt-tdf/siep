<?php
App::uses('AppController', 'Controller');

class VacantesController extends AppController
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
                $this->Auth->allow('index', 'view');
            break;

			default:
                $this->Session->setFlash('No tiene permisos.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect($this->referer());
                break;
        }
    }


    public function index() {
        $showBtnExcel = false;

        $this->loadModel('Centro');

        $this->Centro->recursive = -1;
        $comboSectorDb = $this->Centro->find('all', array('fields'=>'DISTINCT sector'));
        // Sanatizo el combo sector, a una simple lista
        $comboSector = [];
        while (list($c, $v) = each($comboSectorDb)) {
            $sector = $v['Centro']['sector'];
            $comboSector[$sector] = $sector;
        }

        $this->loadModel('Ciudad');
        $comboCiudad = $this->Ciudad->find('list', array('fields'=>array('nombre')));

        $this->loadModel('Ciclo');
        $comboCiclo = $this->Ciclo->find('list', array('fields'=>array('id', 'nombre')));
        $cicloIdUltimo = $this->getLastCicloId();
        $cicloIdActual = $this->getActualCicloId();

        // Datos de usuario logueado
        $userCentro = $this->Auth->user('Centro');

        // Parametros de API por defecto
        $apiParams = [];
        $apiParams['por_pagina'] = 10;
        $apiParams['ciclo'] = 2019;
        //$apiParams['estado_inscripcion'] = 'CONFIRMADA';
        $apiParams['division'] = 'con';
        //$apiParams['order'] = 'anio';
        $apiParams['order_dir'] = 'asc';
        
        // Filtros de formulario y paginacion
        if(isset($this->request->query['ciclo']) && !empty($this->request->query['ciclo'])){
            $apiParams['ciclo'] = $this->request->query['ciclo'];
        }
        if(isset($this->request->query['anio']) && !empty($this->request->query['anio'])){
            $apiParams['anio'] = $this->request->query['anio'];
        }
        if(isset($this->request->query['centro_id']) && !empty($this->request->query['centro_id'])){
            $apiParams['centro_id'] = $this->request->query['centro_id'];
        }
        if(isset($this->request->query['ciudad_id']) && !empty($this->request->query['ciudad_id'])){
            $apiParams['ciudad_id'] = $this->request->query['ciudad_id'];
        }
        if(isset($this->request->query['nivel_servicio']) && !empty($this->request->query['nivel_servicio'])){
            $apiParams['nivel_servicio'] = $this->request->query['nivel_servicio'];
        }
        if(isset($this->request->query['sector'])&& !empty($this->request->query['sector'])){
            $apiParams['sector'] = $this->request->query['sector'];
        }
        if(isset($this->request->query['turno'])&& !empty($this->request->query['turno'])){
            $apiParams['turno'] = $this->request->query['turno'];
        }
        if(isset($this->request->query['vacantes'])&& !empty($this->request->query['vacantes'])){
            $apiParams['vacantes'] = $this->request->query['vacantes'];
        }
        if(isset($this->request->query['page'])&& !empty($this->request->query['page'])){
            $apiParams['page'] = $this->request->query['page'];
        }

        // Filtros de roles
        if($this->Siep->isAdmin())
        {
            $apiParams['centro_id'] = $userCentro['id'];
        }

        if($this->Siep->isUsuario())
        {
            // Supervision Primaria ve Jardines y Escuelas
            if($this->Siep->isSupervisionInicialPrimaria())
            {
                $apiParams['nivel_servicio'] = [
                    'Común - Inicial',
                    'Común - Primario',
                    'Maternal - Inicial',
                    'Especial - Primario',
                    'Adultos - Primario',
                    'Común - Inicial - Primario'
                ];
            } elseif ($this->Siep->isSupervisionSecundaria())
            {
                // Supervision Secundaria, solo ve colegios secundarios
                $apiParams['nivel_servicio'] = [
                    'Común - Secundario'
                ];
            } else {
                // El resto de los usuarios, ven a los inscriptos de sus establecimientos, en su nivel de servicio
                $userNivelServicio = $userCentro['nivel_servicio'];
                $apiParams['centro_id'] = $userCentro['id'];
                $apiParams['nivel_servicio'] = $userNivelServicio;
            }
        }

        // Consumo de API
        $matriculas_por_seccion = $this->Siep->consumeApi("api/matriculas/cuantitativa/por_seccion",$apiParams);
        if(isset($matriculas_por_seccion['error']))
        {
            // Manejar error de API
        }

        //Obtención de los nombres de las titulaciones para mostrar en las secciones.
        $this->loadModel('Titulacion');
        $this->Titulacion->recursive = 0;
        $this->Titulacion->Behaviors->load('Containable');
        $titulacionesNombres = $this->Titulacion->find('list', array(
            'fields'=>array('nombre_abreviado'),
            'contain'=>false,
            'conditions'=>array('status'=>1)));

        if(isset($matriculas_por_seccion['total']) &&  $matriculas_por_seccion>0) {
            $showBtnExcel = true;
            $queryExportarExcel = [];
            $queryExportarExcel['export'] = 'excel';
            $queryExportarExcel['por_pagina'] = '1000000'; // Hardcode (hay un problema de paginacion en el API)
            $queryExportarExcel = array_merge($apiParams,$queryExportarExcel);
        }

        // Consumo de API
        $ubicaciones = $this->Siep->consumeApi("api/v1/ciudades");
        if(isset($ubicaciones['error']))
        {
            // Manejar error de API
        }

        $centroSolicitado = '';
        if(isset($apiParams['centro_id']))
        {
            $centroSolicitado = $apiParams['centro_id'];
        }

        $this->set(compact('centroSolicitado','apiParams','ubicaciones','matriculas_por_seccion','cicloIdUltimo','cicloIdActual','comboCiclo','comboCiudad','comboSector', 'titulacionesNombres','queryExportarExcel','showBtnExcel'));
    }

    public function recuento()
    {
        // Evita buscar el archivo VIEW
        $this->autoRender = false;

        if(isset($this->params['named']['ciclo']))
        {
            $ciclo = $this->params['named']['ciclo'];
            $response = $this->Siep->consumeApi("api/matriculas/recuento/vacantes/$ciclo");
        } else {
            $response = ['error'=>'Debe definir el parametro CICLO'];
        }

        // Muestra el resultado de un Array como JSON
        $this->response->type('json');
        $json = json_encode($response);
        $this->response->body($json);

        // Redireccionar a otra ruta
        // $this->redirect(array('action' => 'index'));
    }
    /*
    public function recuento()
    {
        // Antes que nada devuelvo a cero todas las matriculas, y las vacantes son el total de plazas
        $this->loadModel('Cursos');

        // Esta lista obtiene las divisiones agrupadas, se filtran los cursos sin division, y con turno que no sea "Otro"
        $lista = $this->Cursos->query("
            select 
                id,
                centro_id,
                division,
                anio,
                turno,
                plazas,
                matricula,
                vacantes
            from cursos
            
            where
            
            division = '' and
            turno <> 'otro' 
            
            order by
            
            centro_id        
        ");

        foreach($lista as $item)
        {
            // Realiza el calculo de cuantas plazas, matriculas y vacantes hay en las divisiones != ''
            $cantidades = $this->cuantificarRecuento(
                $item['cursos']['centro_id'],
                $item['cursos']['anio'],
                $item['cursos']['turno']
            );

            // Si existen divisiones != '' para ese centro, año y turno
            if(count($cantidades)>0)
            {
                $el = $cantidades[0][0];

                $plazas = $el['plazas'];
                $matricula = $el['matricula'];
                $vacantes = $el['vacantes'];

                $update = array(
                    'plazas' => $plazas,
                    'matricula' => $matricula,
                    'vacantes' => $vacantes
                );

                // Actualiza los datos del curso agrupado con los datos cuantitativos de los cursos con division
                $this->Cursos->id = $item['cursos']['id'];
                $this->Cursos->save($update);
            }
        }

        $this->autoRender = false;
        $this->redirect(array('action' => 'index'));

//        $this->response->type('json');

//        $json = json_encode($lista);
//        $this->response->body($lista);
    }

    private function cuantificarRecuento($centro_id,$anio,$turno)
    {
        $query = "
          SELECT 
            turno,
            SUM(plazas) as plazas,
            SUM(matricula) as matricula,
            SUM(vacantes) as vacantes
            
          FROM `cursos` 
          WHERE
            centro_id = $centro_id and
            division <> '' and
            anio = '$anio' and
            turno = '$turno'
            
            group by
            turno";

        $this->loadModel('Cursos');
        $lista = $this->Cursos->query($query);

        return $lista;
    }
*/
}
