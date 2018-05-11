<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

class User extends AppModel {

	var $name = 'User';
	public $displayField = 'username';

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Centro' => array(
			'className' => 'Centro',
			'foreignKey' => 'centro_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Empleado' => array(
			'className' => 'Empleado',
			'foreignKey' => 'empleado_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

//Validaciones
   public $validate = array(
	'username' => array(
		'required' => array(
			 'rule' => 'notBlank',
			 'required' => 'create',
			 'message' => 'Indicar una fecha y hora.'
         ),
		'between' => array(
			'rule' => array('between', 5, 15),
			'required' => true,
			'message' => 'Usernames must be between 5 to 15 characters'
		),
		'isUnique' => array(
			 'rule' => 'isUnique',
			 'message' => 'Este nombre de usuario está siendo usado.'
		),
		'alphaNumericDashUnderscore' => array(
			'rule'    => array('alphaNumericDashUnderscore'),
			'message' => 'Username can only be letters, numbers and underscores'
		),
	),
    'password' => array(
		'required' => array(
			'rule' => 'notBlank',
			'required' => 'create',
			'message' => 'La Contraseña es obligatoria'
		),
		'min_length' => array(
			'rule' => array('minLength', '4'),
			'message' => 'La contraseña debe tener al menos 6 caracteres'
		)
	),
	'password_confirm' => array(
		'required' => array(
			'rule' => 'notBlank',
			'required' => 'create',
			'message' => 'Por favor confirme su contraseña'
		),
		'equaltofield' => array(
			'rule' => array('equaltofield','password'),
			'message' => 'Ambas contraseñas deben coincidir'
		)
	),
	'email' => array(
        'required' => array(
		   'rule' => 'notBlank',
            'required' => 'create',
		    'message' => 'Indicar un Email.'
		),
		'email' => array(
            'rule' => 'email',
            'allowEmpty' => true,
            'message' => 'Indicar un formato válido.'
        ),
        'isUnique' => array(
			 'rule' => 'isUnique',
			 'message' => 'Este email está siendo usado.'
		)
    ),
	'role' => array(
		'valid' => array(
			'rule' => array('inList', array('superadmin' ,'admin', 'usuario', 'viewer')),
			'message' => 'Ingrese un rol válido',
			'allowEmpty' => false
		)
	),
	'puesto' => array(
		'valid' => array(
			'rule' => array('inList', array('Sistemas', 'Subsecretaría Provincial', 'Dirección Provincial de Modalidades', 'Dirección Provincial de Superior', 'Supervisión Inicial/Primaria', 'Supervisión Secundaria', 'Dirección Jardín', 'Dirección Escuela Primaria', 'Dirección Colegio Secundario', 'Dirección Instituto Superior', 'Unidad de Estadística Educativa')),
			'message' => 'Ingrese un puesto válido',
			'allowEmpty' => false
		)
		/*
		'required' => array(
			'rule' => array('puesto', true),
			'message' => 'Ingrese una opción de la lista'
	    )
	    */
	),
	'password_update' => array(
		'min_length' => array(
			'rule' => array('minLength', '6'),
			'message' => 'La contraseña debe contener al menos 6 caracteres'
		)
	),
	'password_confirm_update' => array(
		 'equaltofield' => array(
			'rule' => array('equaltofield','password_update'),
			'message' => 'Ambas contraseñas no coinciden'
		)
	)
	);

   /**
   * Before isUniqueUsername
   * @param array $options
   * @return boolean
   *
   */
   /*
    function isUniqueUsername($check) {
         $username = $this->find(
            'first',
            array(
                'fields' => array(
                    'User.id',
                    'User.username'
                ),
                'conditions' => array(
                    'User.username' => $check['username']
                )
            )
        );
        if(!empty($username)){
            if($this->data[$this->alias]['id'] == $username['User']['id']){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }*/

	public function alphaNumericDashUnderscore($check) {
        // $data array is passed using the form field name as the key
        // have to extract the value to make the function generic
        $value = array_values($check);
        $value = $value[0];

        return preg_match('/^[a-zA-Z0-9_ \-]*$/', $value);
    }

	public function equaltofield($check,$otherfield) {
        //get name of field
        $fname = '';
        foreach ($check as $key => $value){
            $fname = $key;
            break;
        }
        return $this->data[$this->name][$otherfield] === $this->data[$this->name][$fname];
    }

	/**
     * Before isUniqueEmail
     * @param array $options
     * @return boolean
     *
     */
	/*
    function isUniqueEmail($check) {
       $email = $this->find(
            'first',
            array(
                'fields' => array(
                    'User.id'
                ),
                'conditions' => array(
                    'User.email' => $check['email']
                )
            )
        );
        if(!empty($email)){
            if($this->data[$this->alias]['id'] == $email['User']['id']){
                return true;
            }else{
                return false;
            }
        }else{
            return true;
        }
    }*/

	/**
	 * Before Save
	 * @param array $options
	 * @return boolean
	 */
	 public function beforeSave($options = array()) {
		// hash our password
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->                   alias]['password']);
		}
		// if we get a new password, hash it
		if (isset($this->data[$this->alias]['password_update'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->                   alias]['password_update']);
		}
		// fallback to our parent
		return parent::beforeSave($options);
	}
}
?>
