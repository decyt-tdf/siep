<?php
class Curso extends AppModel {
	var $name = 'Curso';
    //var $displayField = 'division';
	public $virtualFields = array('nombre_completo_curso'=> 'CONCAT(Curso.anio, " ", Curso.division, " ", Curso.turno)');
    public $actsAs = array('Containable');

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Centro' => array(
			'className' => 'Centro',
			'foreignKey' => 'centro_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Titulacion' => array(
			'className' => 'Titulacion',
			'foreignKey' => 'titulacion_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Materia' => array(
			'className' => 'Materia',
			'foreignKey' => 'curso_id',
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
	);

    var $hasAndBelongsToMany = array(
		'Inscripcion' => array(
			'className' => 'Inscripcion',
			'joinTable' => 'cursos_inscripcions',
			'foreignKey' => 'curso_id',
			'associationForeignKey' => 'inscripcion_id',
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
		'Inasistencia' => array(
			'className' => 'Inasistencia',
			'joinTable' => 'cursos_inasistencias',
			'foreignKey' => 'curso_id',
			'associationForeignKey' => 'inasistencia_id',
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
	);

    //Validaciones

        var $validate = array(
				   'tipo' => array(
                        'valid' => array(
							'rule' => array('inList', array('Independiente','Independiente de recuperación','Independiente semipresencial','Independiente presencial y semipresencial','Múltiple','Múltiple de recuperación','Múltiple semipresencial','Múltiple presencial y semipresencial','No Corresponde','Independiente presencial y semipresencial (violeta)','Mixta / Bimodal','Múltiple presencial y semipresencial (violeta)','Multinivel','Multiplan')),
							'message' => 'Ingrese un tipo válido',
							'allowEmpty' => false
						)	
                    ),
				   'anio' => array(
                        'valid' => array(
							'rule' => array('inList', array('Sala de menos de 1 año','Sala de 1 año','Sala de 2 años','Sala de 3 años','Sala de 4 años','Sala de 5 años','1ro','2do','3ro','4to','5to','6to','7mo')),
							'message' => 'Ingrese un año válido',
							'allowEmpty' => false
						)	
                    ),
                   'division' => array(
                        'required' => array(
						   	'rule' => 'notBlank',
						   	'required' => 'create',
                           	'message' => 'Ingrese una división válida',
						)	
                    ),
                   'turno' => array(
                        'valid' => array(
							'rule' => array('inList', array('Mañana','Tarde','Mañana Extendida','Tarde Extendida','Doble','Vespertino','Noche','Otro','Bachiller','Tecnico')),
							'message' => 'Ingrese un turno válido',
							'allowEmpty' => false
						)	
                    ),
                   'plazas' => array(
                   		'required' => array(
						   	'rule' => 'notBlank',
						   	'required' => 'create',
                           	'message' => 'Indicar la cantidad de plazas.'
                       	),
						'numeric' => array(
							'rule' => 'naturalNumber',
							'message' => 'Indicar número sin puntos ni comas ni espacios.'
						)
                   ),
                   'matricula' => array(
			            'numeric' => array(
			                'rule' => 'naturalNumber',
			                'allowEmpty' => true,
			                'message' => 'Indicar número sin puntos ni comas ni espacios.'
		                )
		            ),
                   	'vacantes' => array(
		                'required' => array(
						   	'rule' => 'notBlank',
						   	'required' => 'create',
                           	'message' => 'Indicar la cantidad de vacantes.'
                       	),
		                'numeric' => array(
			                'rule' => 'naturalNumber',
			                'message' => 'Indicar número sin puntos ni comas ni espacios.'
		                )
		            ),
				   'aula_nro' => array(
                        'numeric' => array(
							'rule' => 'naturalNumber',
							'allowEmpty' => true,
							'message' => 'Indicar número sin puntos ni comas ni espacios.'
						)
                   ),
                   'titulacion_id' => array(
                        'required' => array(
						   'rule' => 'notBlank',
						   'required' => 'create',
                           'message' => 'Indicar una titulación.'
					  	),
						'numeric' => array(
							'rule' => 'naturalNumber',
						 	'message' => 'Indicar número sin puntos ni comas ni espacios.'
						)
	                )
     		);
}
?>
