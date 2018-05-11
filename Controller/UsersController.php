
<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {
 
    public $helpers = array('Text', 'Js', 'Time');
    public $components = array('Paginator', 'RequestHandler', 'Session');
    /*
    public $paginate = array(
        'limit' => 25,
        'conditions' => array('status' => '1'),
        'order' => array('User.username' => 'asc' ) 
    );
    */
   
    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('login','add');

        //Si el usuario tiene un rol de superadmin entonces le dejamos paso a todo.
        //Si no es así se trata de un usuario común y le permitimos solo la acción
        //logout y la correspondiente a usuario (página solo para ellos)
	    if($this->Auth->user('role') === 'superadmin') {
	        $this->Auth->allow();
	    } elseif (($this->Auth->user('role') === 'admin') || ($this->Auth->user('role') === 'usuario') || ($this->Auth->user('role') === 'viewer')) { 
	        $this->Auth->allow('logout', 'usuario');
	    } 
    }     
     
	//Acción para redirigir a los usuarios con rol usuario común
    public function usuario() {
    	$this->paginate = array(
			'limit' => 5,
			'order' => array('User.username' => 'asc' )
		);
    	$users = $this->paginate('User');
		$this->set(compact('users'));
    	$this->render('/Users/usuario');
    }
	
	public function login() {
		//if already logged-in, redirect
		if($this->Session->check('Auth.User')){
			$this->redirect(array('action' => 'index'));		
		}
		// if we get the post information, try to authenticate
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->Session->setFlash('Bienvenido, '. $this->Auth->user('username'), 'default', array('class' => 'alert alert-success'));
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Nombre de usuario o contraseña incorrectos.'));
			}
		} 
	}

    public function logout() {
    	$this->Auth->logout();
		$this->redirect('login');
    }

    public function index() {
        //$this->User->recursive = 0;
		$this->paginate = array(
            'limit' => 10,
            'order' => array('User.username' => 'asc' )
        );
		$this->redirectToNamed();
		$conditions = array();
		
		if(!empty($this->params['named']['username']))
		{
			$conditions['User.username ='] = $this->params['named']['username'];
		}
		
        $users = $this->paginate('User', $conditions);
        $this->set(compact('users'));
    }

	public function view($id = null) {
		if (!$id) {
			$this->Session->setFlash('Usuario no válido', 'default', array('class' => 'alert alert-danger'));
			$this->redirect(array('action' => 'index'));
		}
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
        /*
        $this->pdfConfig = array(
            'download' => true,
            'filename' => 'user_' . $id .'.pdf'
        );
        */
		$this->set('user', $this->User->read(null, $id));
		$this->set('user', $this->User->find('first', $options));
	}

	public function add() {
		  //abort if cancel button was pressed  
          if(isset($this->params['data']['cancel'])){
                $this->Session->setFlash('Los cambios no fueron guardados. Agregación cancelada.'                                          , 'default', array('class' => 'alert alert-warning'));
                $this->redirect( array( 'action' => 'index' ));
		  }
		  if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
                $this->Session->setFlash('El Usuario ha sido grabado', 'default', array('class' => 'alert alert-success'));
				$inserted_id = $this->User->id;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
				$this->Session->setFlash('El Usuario no fue grabado. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			}
		}
		$centros = $this->User->Centro->find('list');
		$empleados = $this->User->Empleado->find('list', array('fields'=>array('id', 'nombre_completo_empleado')));
		$this->set(compact('centros', 'empleados'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash('Usuario no valido', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
		  //abort if cancel button was pressed  
            if(isset($this->params['data']['cancel'])){
                $this->Session->setFlash('Los cambios no fueron guardados. Edición cancelada.', 'default', array('class' => 'alert alert-warning'));
                $this->redirect( array( 'action' => 'index' ));
		  }
			if ($this->User->save($this->data)) {
				$this->Session->setFlash('El usuario ha sido grabado', 'default', array('class' => 'alert alert-success'));
				$inserted_id = $this->User->id;
				$this->redirect(array('action' => 'view', $inserted_id));
			} else {
				$this->Session->setFlash('El usuario no ha sido grabado. Intentelo nuevamente.', 'default', array('class' => 'alert alert-danger'));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
		$centros = $this->User->Centro->find('list');
		$empleados = $this->User->Empleado->find('list', array('fields'=>array('id', 'nombre_completo_empleado')));
		$this->set(compact('centros', 'empleados'));
	}

	 public function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash('Id no valido para el usuario', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action'=>'index'));
		}
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('ID inválido');
			$this->redirect(array('action'=>'index'));
        }
        if ($this->User->saveField('status', 0)) {
 			$this->Session->setFlash('El usuario ha sido borrado', 'default', array('class' => 'alert alert-success'));
            $this->redirect(array('action' => 'index'));
        }
		$this->Session->setFlash('El usuario no fue borrado', 'default', array('class' => 'alert alert-danger'));
        $this->redirect(array('action' => 'index'));
    }
	
	public function activate($id = null) {
		if (!$id) {
			$this->Session->setFlash('Id no valido para el usuario', 'default', array('class' => 'alert alert-warning'));
			$this->redirect(array('action'=>'index'));
		}		
        $this->User->id = $id;
        if (!$this->User->exists()) {
            $this->Session->setFlash('ID inválido');
			$this->redirect(array('action'=>'index'));
        }
        if ($this->User->saveField('status', 1)) {
			$this->Session->setFlash('El usuario ha sido reactivado', 'default', array('class' => 'alert alert-success'));
            $this->redirect(array('action' => 'index'));
        }
		$this->Session->setFlash('El usuario no pudo ser reactivado', 'default', array('class' => 'alert alert-danger'));
        $this->redirect(array('action' => 'index'));
    }
	
}
?>
