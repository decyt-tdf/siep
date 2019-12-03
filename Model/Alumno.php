<?php
App::uses('AppModel', 'Model');

class Alumno extends AppModel {

	var $name = 'Alumno';
    //var $displayField = 'apellido';
	//public $virtualFields = array('nombre_completo_alumno'=> 'CONCAT(Alumno.apellidos, " ", Alumno.nombres)');
	public $actsAs = array('Containable');
    
    var $belongsTo = array(
		'Persona' => array(
			'className' => 'Persona',
			'foreignKey' => 'persona_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
	    /*
	    'Integracion' => array(
	      'className' => 'Integracion',
	      'foreignKey' => 'alumno_id',
	      'dependent' => false,
	      'conditions' => '',
	      'fields' => '',
	      'order' => '',
	      'limit' => '',
	      'offset' => '',
	      'exclusive' => '',
	      'finderQuery' => '',
	      'counterQuery' => ''
	    ),
	    */
	    /*
	    'Servicio' => array(
	      'className' => 'Servicio',
	      'foreignKey' => 'alumno_id',
	      'dependent' => false,
	      'conditions' => '',
	      'fields' => '',
	      'order' => '',
	      'limit' => '',
	      'offset' => '',
	      'exclusive' => '',
	      'finderQuery' => '',
	      'counterQuery' => ''
	    ),
	    */
	    'Inscripcion' => array(
	      'className' => 'Inscripcion',
	      'foreignKey' => 'alumno_id',
	      'dependent' => false,
	      'conditions' => '',
	      'fields' => '',
	      'order' => '',
	      'limit' => '',
	      'offset' => '',
	      'exclusive' => '',
	      'finderQuery' => '',
	      'counterQuery' => ''
	    ),
	    /*
	    'Inasistencia' => array(
	      'className' => 'Inasistencia',
	      'foreignKey' => 'alumno_id',
	      'dependent' => false,
	      'conditions' => '',
	      'fields' => '',
	      'order' => '',
	      'limit' => '',
	      'offset' => '',
	      'exclusive' => '',
	      'finderQuery' => '',
	      'counterQuery' => ''
	    ),
	    */
	    /*
	    'Nota' => array(
	      'className' => 'Nota',
	      'foreignKey' => 'alumno_id',
	      'dependent' => false,
	      'conditions' => '',
	      'fields' => '',
	      'order' => '',
	      'limit' => '',
	      'offset' => '',
	      'exclusive' => '',
	      'finderQuery' => '',
	      'counterQuery' => ''
	    ),
	    */
	    /*
	    'Pase' => array(
	      'className' => 'Pase',
	      'foreignKey' => 'alumno_id',
	      'dependent' => false,
	      'conditions' => '',
	      'fields' => '',
	      'order' => '',
	      'limit' => '',
	      'offset' => '',
	      'exclusive' => '',
	      'finderQuery' => '',
	      'counterQuery' => ''
	    )
	    */
	);

   /**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	var $hasAndBelongsToMany = array(
		'Familiar' => array(
			'className' => 'Familiar',
			'joinTable' => 'alumnos_familiars',
			'foreignKey' => 'alumno_id',
			'associationForeignKey' => 'familiar_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		),
		/*
		'Mesaexamen' => array(
			'className' => 'Mesaexamen',
			'joinTable' => 'alumnos_mesaexamens',
			'foreignKey' => 'mesaexamen_id',
			'associationForeignKey' => 'alumno_id',
			'unique' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
		*/
	);

//Validaciones

		var $validate = array(
			'persona_id' => array(
				'required' => array(
				'rule' => 'notBlank',
				'required' => true,
				'message' => 'Indicar los nombres.'
				),
				'numeric' => array(
	 	        'rule' => 'naturalNumber',
	 	        ),
			),
			'centro_id' => array(
				'required' => array(
					'rule' => 'notBlank',
					'required' => 'create',
					'message' => 'Indicar un centro.'
				),
				'numeric' => array(
					'rule' => 'naturalNumber',
					),
				),
				'legajo_fisico_nro' => array(
					'alphaBet' => array(
					'allowEmpty' => true,
					'rule' => '/^[ áÁéÉíÍóÓúÚ a-zA-ZñÑ 0-9 -]{3,}$/i',
					'message' => 'Sólo letras, números y el caracter especial -.'
					)
				)
	    );

	//Funciones privadas.

	function addAsterisco($id){
	   $this->id = $id;
	   $this->saveField('pendientes', 1);
	}
}
?>
