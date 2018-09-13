SELECT DISTINCT ases_students.username, 
		ases_students.firstname,  
		ases_students.lastname,
		ases_students.num_doc,
                ases_students.email,
                ases_students.cohorts_student,
                ases_students.direccion_res,
                academic_program.cod_univalle AS cod_univalle,
		academic_program.nombre AS academic_program_name,
                
		ases_status.ases_status_student,
		icetex_status.icetex_status_student,
		ases_students.program_status,
		faculty.nombre AS faculty_name,

		accum_average.promedio_acumulado,
		history_bajo.numero_bajo,
        
		assignments_query.monitor,
		assignments_query.trainer,
		assignments_query.professional
		
FROM (SELECT moodle_user.username, 
	     moodle_user.firstname,  
	     moodle_user.lastname,
	     ases_user.num_doc,
	     ases_user.id AS student_id,
         moodle_user.email,
         ases_user.celular,
         ases_user.direccion_res,
	     STRING_AGG(cohort.idnumber, ', ') AS cohorts_student,
	     program_statuses.nombre AS program_status,
	     user_extended.id_academic_program	     
     FROM {cohort} AS cohort 
     INNER JOIN {talentospilos_inst_cohorte} AS instance_cohort ON cohort.id = instance_cohort.id_cohorte
     INNER JOIN {cohort_members} AS cohort_member ON cohort_member.cohortid = cohort.id
     INNER JOIN {user} AS moodle_user ON moodle_user.id = cohort_member.userid
     INNER JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_moodle_user = moodle_user.id
     INNER JOIN {talentospilos_usuario} AS ases_user ON ases_user.id = user_extended.id_ases_user
     INNER JOIN {talentospilos_estad_programa} AS program_statuses ON program_statuses.id = user_extended.program_status
     WHERE instance_cohort.id_instancia = 450299 AND user_extended.tracking_status = 1
     GROUP BY moodle_user.username, 
              moodle_user.firstname, 
              moodle_user.lastname, 
              student_id,
              moodle_user.email,
              ases_user.celular,
              ases_user.direccion_res,
              ases_user.num_doc, 
              program_statuses.nombre, 
              user_extended.id_academic_program) AS ases_students

LEFT JOIN (SELECT current_ases_status.id_ases_student AS id_ases_student, 
                  historic_ases_statuses.nombre AS ases_status_student
	   FROM
	      (SELECT student_ases_status.id_estudiante AS id_ases_student,
	              MAX(student_ases_status.fecha) AS fecha
	      FROM {talentospilos_est_estadoases} AS student_ases_status
	      WHERE id_instancia = 450299
	      GROUP BY student_ases_status.id_estudiante) AS current_ases_status
	    INNER JOIN
		(SELECT student_ases_status.id_estudiante, 
		        student_ases_status.fecha, ases_statuses.nombre
		FROM {talentospilos_est_estadoases} AS student_ases_status
		     INNER JOIN {talentospilos_estados_ases} AS ases_statuses ON ases_statuses.id = student_ases_status.id_estado_ases) AS historic_ases_statuses
		ON (historic_ases_statuses.id_estudiante = current_ases_status.id_ases_student AND historic_ases_statuses.fecha = current_ases_status.fecha)
	) AS ases_status ON ases_status.id_ases_student = ases_students.student_id

LEFT JOIN (SELECT current_icetex_status.id_ases_student AS id_ases_student, 
                 historic_icetex_statuses.nombre AS icetex_status_student
          FROM
		(SELECT student_icetex_status.id_estudiante AS id_ases_student,
		       MAX(student_icetex_status.fecha) AS fecha
		FROM {talentospilos_est_est_icetex} AS student_icetex_status
		GROUP BY student_icetex_status.id_estudiante) AS current_icetex_status
	  INNER JOIN
		(SELECT student_icetex_status.id_estudiante, student_icetex_status.fecha, icetex_statuses.nombre
		FROM {talentospilos_est_est_icetex} AS student_icetex_status
		     INNER JOIN {talentospilos_estados_icetex} AS icetex_statuses ON icetex_statuses.id = student_icetex_status.id_estado_icetex) AS historic_icetex_statuses
	   ON (historic_icetex_statuses.id_estudiante = current_icetex_status.id_ases_student AND historic_icetex_statuses.fecha = current_icetex_status.fecha)) AS icetex_status ON icetex_status.id_ases_student = ases_students.student_id

LEFT JOIN (SELECT history_academic.promedio_acumulado, history_academic.id_estudiante
	   FROM {talentospilos_history_academ} AS history_academic
		INNER JOIN (SELECT id_estudiante, MAX(id_semestre) AS id_semestre
	                    FROM {talentospilos_history_academ}
	                    GROUP BY id_estudiante) AS students_semesters
                ON (history_academic.id_semestre = students_semesters.id_semestre AND history_academic.id_estudiante = students_semesters.id_estudiante 
)
                INNER JOIN {talentospilos_user_extended} AS user_extended ON (user_extended.id_ases_user = history_academic.id_estudiante AND history_academic.id_programa = user_extended.id_academic_program)
           
        WHERE tracking_status = 1
  
) AS accum_average ON accum_average.id_estudiante = ases_students.student_id

LEFT JOIN (SELECT DISTINCT MAX(numero_bajo) AS numero_bajo, academic_history.id_estudiante
	   FROM {talentospilos_history_academ} AS academic_history
	        INNER JOIN {talentospilos_history_bajos} AS history_bajos ON history_bajos.id_history = academic_history.id
	   GROUP BY academic_history.id_estudiante
	   ) AS history_bajo ON history_bajo.id_estudiante = ases_students.student_id

INNER JOIN {talentospilos_programa} AS academic_program ON academic_program.id = ases_students.id_academic_program
INNER JOIN {talentospilos_facultad} AS faculty ON faculty.id = academic_program.id_facultad

LEFT JOIN 
 (SELECT monitor_student.id_ases_user AS id_estudiante,
         monitor_student.username, 
	 psico_staff.monitor AS monitor, 
	 psico_staff.trainer AS trainer, 
	 psico_staff.professional AS professional
  FROM
    (SELECT user_extended.id_ases_user,
            monitor_student.id_monitor, 
            moodle_user.username
     FROM {talentospilos_monitor_estud} AS monitor_student
          LEFT JOIN {talentospilos_user_extended} AS user_extended ON user_extended.id_ases_user = monitor_student.id_estudiante
          INNER JOIN {user} AS moodle_user ON moodle_user.id = user_extended.id_moodle_user
     WHERE monitor_student.id_semestre =  7 
           AND monitor_student.id_instancia = 450299) AS monitor_student

     LEFT JOIN

    (SELECT query_monitor.id_monitor,
            query_monitor.monitor_name AS monitor,
            query_trainer.trainer_name AS trainer, 
            query_professional.professional_name AS professional
     FROM
      (SELECT user_role.id_usuario AS id_monitor,
              CONCAT(moodle_user.firstname,
              CONCAT(' ', moodle_user.lastname)) AS monitor_name,
              user_role.id_jefe AS id_boss_monitor
       FROM {talentospilos_user_rol} AS user_role 
       INNER JOIN {user} AS moodle_user ON user_role.id_usuario = moodle_user.id
       WHERE id_rol = (SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = 'monitor_ps') AND id_semestre =  7 AND id_instancia = 450299) AS query_monitor

     INNER JOIN
    
     (SELECT user_role.id_usuario AS id_trainer, CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS trainer_name, user_role.id_jefe AS id_boss_trainer
      FROM {talentospilos_user_rol} AS user_role 
      INNER JOIN {user} AS moodle_user ON user_role.id_usuario = moodle_user.id
      WHERE id_rol = (SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = 'practicante_ps') AND id_semestre =  7 AND id_instancia = 450299) AS query_trainer
    
     ON query_monitor.id_boss_monitor = query_trainer.id_trainer
    
    INNER JOIN
    
    (SELECT user_role.id_usuario AS id_professional, CONCAT(moodle_user.firstname, CONCAT(' ', moodle_user.lastname)) AS professional_name
    FROM {talentospilos_user_rol} AS user_role 
    INNER JOIN {user} AS moodle_user ON user_role.id_usuario = moodle_user.id
    WHERE id_rol = (SELECT id FROM {talentospilos_rol} AS role WHERE nombre_rol = 'profesional_ps') AND id_semestre =  7 AND id_instancia = 450299) AS query_professional
    
    ON query_professional.id_professional = query_trainer.id_boss_trainer) AS psico_staff
    
    ON monitor_student.id_monitor = psico_staff.id_monitor) AS assignments_query
ON assignments_query.id_estudiante = ases_students.student_id
