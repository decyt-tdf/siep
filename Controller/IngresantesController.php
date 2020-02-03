<?php
App::uses('AppController', 'Controller');

class IngresantesController extends AppController
{
    // Permite agregar el Helper de Siep a las vistas
    public $helpers = array('Siep');

    public function beforeFilter()
    {
        parent::beforeFilter();

        // Importa el Helper de Siep al controlador es accesible mediante $this->Siep
        App::import('Helper', 'Siep');
        $this->Siep= new SiepHelper(new View());

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
        $this->Ciudad->recursive = 0;
        $this->Ciudad->Behaviors->load('Containable');
        $comboCiudad = $this->Ciudad->find('list', array('fields'=>array('nombre')));

        $this->loadModel('Ciclo');
        $this->Ciclo->recursive = 0;
        $this->Ciclo->Behaviors->load('Containable');
        $comboCiclo = $this->Ciclo->find('list', array('fields'=>array('id', 'nombre')));
        $cicloIdUltimo = $this->getLastCicloId();
        $cicloNombreUltimo = $comboCiclo[$cicloIdUltimo];

        $cicloIdActual = $this->getActualCicloId();

        // Datos de usuario logueado
        $userCentro = $this->Auth->user('Centro');

        // Parametros de API por defecto
        $apiParams = [];
        $apiParams['por_pagina'] = 10;
        $apiParams['ciclo'] = 2019;
        $apiParams['estado_inscripcion'] = ['CONFIRMADA', 'NO CONFIRMADA',];
        $apiParams['division'] = 'sin';
        $apiParams['order'] = 'anio';
        $apiParams['order_dir'] = 'asc';
        // Solo se muestran salas de 4 y 1ro
        $apiParams['anio'] = ['Sala de 4 años','1ro'];

        // Filtros de formulario y paginacion
        if(isset($this->request->query['ciclo'])){
            $apiParams['ciclo'] = $this->request->query['ciclo'];
        }
//        if(isset($this->request->query['anio'])){
//            $apiParams['anio'] = $this->request->query['anio'];
//        }
        if(isset($this->request->query['centro_id'])){
            $apiParams['centro_id'] = $this->request->query['centro_id'];
        }
        if(isset($this->request->query['page'])){
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
                // Solo muestra sala de 4 años y 1ro
                $apiParams['nivel_servicio'] = ['Común - Inicial', 'Común - Primario'];
            } elseif ($this->Siep->isSupervisionSecundaria())
            {
                // Supervision Secundaria, solo ve colegios secundarios
                // Solo 1ro año
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

        $this->set(compact('matriculas_por_seccion', 'cicloIdUltimo', 'cicloNombreUltimo', 'cicloIdActual','comboCiclo','comboCiudad','comboSector','apiParams'));
    }
}
