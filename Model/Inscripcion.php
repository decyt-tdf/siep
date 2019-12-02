<?php
class Inscripcion extends AppModel {
	var $name = 'Inscripcion';
	public $displayField = 'legajo_nro';
	public $actsAs = array('Containable');

	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Alumno' => array(
			'className' => 'Alumno',
			'foreignKey' => 'alumno_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Ciclo' => array(
			'className' => 'Ciclo',
			'foreignKey' => 'ciclo_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Centro' => array(
			'className' => 'Centro',
			'foreignKey' => 'centro_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	var $hasOne = array(
		'Pase' => array(
			'className' => 'Pase',
			'foreignKey' => 'inscripcion_id',
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
		'Curso' => array(
			'className' => 'Curso',
			'joinTable' => 'cursos_inscripcions',
			'foreignKey' => 'inscripcion_id',
			'associationForeignKey' => 'curso_id',
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
		'Materia' => array(
			'className' => 'Materia',
			'joinTable' => 'inscripcions_materias',
			'foreignKey' => 'inscripcion_id',
			'associationForeignKey' => 'materia_id',
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
	   'tipo_inscripcion' => array(
            'valid' => array(
				'rule' => array('inList', array('Común','Hermano de alumno regular','Pase','Integración','Situación social','Hijo de personal de la institución','Estudiante de Intercambio')),
				'allowEmpty' => true,
				'message' => 'Indicar una opción'
			)
        ),
	   'alumno_id' => array(
			   'required' => array(
			   'rule' => 'notBlank',
			   'required' => 'create',
			   'message' => 'Indicar un alumno.'
			 ),
			 'numeric' => array(
				'rule' => 'naturalNumber',
				'message' => 'Indicar un alumno válido.'
			)
	   ),
	   'centro_id' => array(
			   'required' => array(
			   'rule' => 'notBlank',
			   'required' => 'create',
			   'message' => 'Indicar un Centro.'
			 ),
			 'numeric' => array(
				'rule' => 'naturalNumber',
				'message' => 'Indicar un centro válido.'
			)
	   ),
	   'legajo_nro' => array(
			   'required' => array(
			   'rule' => 'notBlank',
			   'required' => 'create',
			   'message' => 'Indicar un nº de legajo.'
			   ),
			   'isUnique' => array(
			   'rule' => 'isUnique',
			   'message' => 'Este nº de legajo de alumno esta siendo usado.'
			 )
	   ),
        'tipo_alta' => array(
            'valid' => array(
				'rule' => array('inList', array('Regular', 'Equivalencia')),
				'allowEmpty' => true,
				'message' => 'Indicar una opción'
			),
			'alphaBet' => array(
				'rule' => '/^[ áÁéÉíÍóÓúÚa-zA-ZñÑ]{7,}$/i',
			)
        ),
		/*
		'fecha_alta' => array(
			'required' => array(
				'rule' => 'notBlank',
				'required' => 'create',
				'message' => 'Indicar una fecha de alta.'
			),
			'date' => array(
				'rule' => 'date',
				'message' => 'Indicar fecha valida.'
			)
		),
		'cursa' => array(
			   'valid' => array(
					'rule' => array('inList', array('Cursa algun espacio curricular', 'Sólo se inscribe a rendir final', 'Cursa espacio curricular y rinde final')),
					'allowEmpty' => true,
					'message' => 'Indicar una opción'
			),
			 'alphaBet' => array(
		 'rule' => '/^[ áÁéÉíÍóÓúÚa-zA-ZñÑ]{5,}$/i',
			)
	   	),
	   	'fines' => array(
			   'valid' => array(
					'rule' => array('inList', array('No', 'Sí línea deudores de materias.', 'Sí línea trayectos educativos.')),
					'allowEmpty' => true,
					'message' => 'Indicar una opción'
			),
			'alphaBet' => array(
		 'rule' => '/^[ áÁéÉíÍóÓúÚa-zA-ZñÑ]{2,}$/i',
			)
	   	),
	   	*/
	   	'fecha_baja' => array(
			   'date' => array(
			   'rule' => 'date',
			   'allowEmpty' => true,
			   'message' => 'Indicar una fecha válida.'
			   )
	   	),
	   	'tipo_baja' => array(
			   'minLength' => array(
			   'rule' => array('minLength', 3),
			   'allowEmpty' => true,
			   'message' => 'Indicar una opción.'
									 ),
									 'alphaBet' => array(
								 'rule' => '/^[ áÁéÉíÍóÓúÚa-zA-ZñÑ]{3,}$/i',
								 )
	   	),
	   	'motivo_baja' => array(
			   'minLength' => array(
			   'rule' => array('minLength', 3),
			   'allowEmpty' => true,
			   'message' => 'Indicar una opción.'
									 ),
									 'alphaBet' => array(
								'rule' => '/^[ áÁéÉíÍóÓúÚa-zA-ZñÑ]{3,}$/i',
								)
	   	),
	   	'fecha_egreso' => array(
			   'date' => array(
			   'rule' => 'date',
			   'allowEmpty' => true,
			   'message' => 'Indicar una fecha válida.'
			   )
	   ),
	   	/*
	   	'acta_nro' => array(
			 'minLength' => array(
				'rule' => array('minLength',4),
				'allowEmpty' => true,
				'message' => 'Indicar código postal.'
			),
			'numeric' => array(
				'rule' => 'naturalNumber',
				'message' => 'Indicar número sin puntos ni comas ni espacios.'
			)
		),
		'libro_nro' => array(
				'numeric' => array(
				'rule' => 'naturalNumber',
				'allowEmpty' => true,
				'message' => 'Indicar número de libro sin puntos ni comas ni espacios.'
			)
		),
		'folio_nro' => array(

				'numeric' => array(
				'rule' => 'naturalNumber',
				'allowEmpty' => true,
				'message' => 'Indicar número sin puntos ni comas ni espacios.'
			)
		),
		'fecha_emision_titulo' => array(
				'date' => array(
				'rule' => 'date',
				'allowEmpty' => true,
				'message' => 'Indicar una fecha válida.'
				)
		),
		'recursante' => array(
				'boolean' => array(
				'rule' => array('boolean'),
				'allowEmpty' => true,
				'message' => 'Indicar una opción'
				)
		),
		'condicion_aprobacion' => array(
				'minLength' => array(
				'rule' => array('minLength', 3),
				'allowEmpty' => false,
				'message' => 'Indicar una opción.'
				)
		),
		'fecha_nota' => array(
				'date' => array(
				'rule' => 'date',
				'allowEmpty' => true,
				'message' => 'Indicar una fecha válida.'
				)
		),
		*/           
		'fotocopia_dni' => array(
				'boolean' => array(
				'rule' => array('boolean'),
				'allowEmpty' => true,
				'message' => 'Indicar una opción'
				)
		),
		'partida_nacimiento_alumno' => array(
				'boolean' => array(
				'rule' => array('boolean'),
				'allowEmpty' => true,
				'message' => 'Indicar una opción'
				)
		),
		'certificado_vacunas' => array(
				'boolean' => array(
				'rule' => array('boolean'),
				'allowEmpty' => true,
				'message' => 'Indicar una opción'
				)
		),
		'certificado_septimo' => array(
				'boolean' => array(
				'rule' => array('boolean'),
				'allowEmpty' => true,
				'message' => 'Indicar una opción'
				)
		),
		'estado_inscripcion' => array(
			'valid' => array(
			'rule' => array('inList', array('CONFIRMADA','NO CONFIRMADA','BAJA','EGRESO','ANULADA', 'SIN TERMINALIDAD')),
			'message' => 'Indicar una opción',
			'allowEmpty' => false
				)
		),
		'estado_documentacion' => array(
			'valid' => array(
			'rule' => array('inList', array('PENDIENTE','COMPLETA')),
			'message' => 'Indicar una opción',
			'allowEmpty' => false
				)
		),
		'cud_estado' => array(
			'valid' => array(
				'rule' => array('inList', array('Actualizado', 'Desactualizado', 'No tiene', 'No corresponde')),
				'allowEmpty' => true,
				'message' => 'Indicar una opción'
			),
			'alphaBet' => array(
				'rule' => '/^[ áÁéÉíÍóÓúÚa-zA-ZñÑ]{7,}$/i',
			)
		),
	);
}
?>
