<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Spanish (Mexico) language strings for local_coursematrix
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Matriz de Cursos';
$string['coursematrix'] = 'Matriz de Cursos';
$string['coursematrix:manage'] = 'Administrar Matriz de Cursos';
$string['coursematrix:viewdashboard'] = 'Ver Panel de Planes de Aprendizaje';
$string['coursematrix:assignplans'] = 'Asignar Planes de Aprendizaje a Usuarios';
$string['coursematrix:receivereminders'] = 'Recibir Recordatorios de Planes de Aprendizaje';
$string['department'] = 'Departamento';
$string['jobtitle'] = 'Puesto de Trabajo';
$string['courses'] = 'Cursos';
$string['addnewrule'] = 'Agregar Nueva Regla';
$string['editrule'] = 'Editar Regla';
$string['deleterule'] = 'Eliminar Regla';
$string['searchcourses'] = 'Buscar Cursos';
$string['selectcourses'] = 'Seleccionar Cursos';
$string['norules'] = 'No hay reglas definidas aún.';
$string['save'] = 'Guardar';
$string['cancel'] = 'Cancelar';
$string['actions'] = 'Acciones';
$string['matrixupdated'] = 'Matriz actualizada e inscripciones procesadas.';

// Planes de Aprendizaje.
$string['learningplans'] = 'Planes de Aprendizaje';
$string['learningplan'] = 'Plan de Aprendizaje';
$string['createplan'] = 'Crear Plan de Aprendizaje';
$string['editplan'] = 'Editar Plan de Aprendizaje';
$string['deleteplan'] = 'Eliminar Plan de Aprendizaje';
$string['planname'] = 'Nombre del Plan';
$string['plandescription'] = 'Descripción';
$string['plancourses'] = 'Cursos en el Plan';
$string['plancourses_help'] = 'Seleccione los cursos que componen este plan de aprendizaje. El orden en que los seleccione determina la secuencia por la que progresarán los usuarios.';
$string['duedays'] = 'Días para Completar';
$string['reminders'] = 'Programación de Recordatorios';
$string['addreminder'] = 'Agregar Recordatorio';
$string['removereminder'] = 'Eliminar';
$string['daysbefore'] = 'Días Antes del Vencimiento';
$string['assigntoplan'] = 'Asignar a Plan de Aprendizaje';
$string['assignusers'] = 'Asignar Usuarios';
$string['selectusers'] = 'Seleccionar Usuarios';
$string['selectplan'] = 'Seleccionar Plan de Aprendizaje';
$string['selectlearningplans'] = 'Seleccionar Planes de Aprendizaje';
$string['noplans'] = 'No hay planes de aprendizaje definidos aún.';
$string['plansaved'] = 'Plan de aprendizaje guardado exitosamente.';
$string['plandeleted'] = 'Plan de aprendizaje eliminado.';
$string['usersassigned'] = 'Usuarios asignados al plan de aprendizaje.';
$string['alreadyassigned'] = 'El usuario ya está asignado a este plan.';

// Estado.
$string['status'] = 'Estado';
$string['status_active'] = 'En Progreso';
$string['status_overdue'] = 'Vencido';
$string['status_completed'] = 'Completado';
$string['currentcourse'] = 'Curso Actual';
$string['startdate'] = 'Iniciado';
$string['duedate'] = 'Fecha de Vencimiento';

// Etiquetas de fecha de vencimiento.
$string['daysremaining'] = '{$a} días restantes';
$string['dayremaining'] = '1 día restante';
$string['overdue'] = 'VENCIDO';
$string['overduedays'] = '{$a} días de retraso';

// Panel de Control.
$string['dashboard'] = 'Panel de Control';
$string['totalplans'] = 'Total de Planes';
$string['totalusers'] = 'Total de Usuarios';
$string['activeusers'] = 'En Progreso';
$string['overdueusers'] = 'Vencidos';
$string['completedusers'] = 'Completados';
$string['planstatistics'] = 'Estadísticas de Planes';
$string['userlist'] = 'Lista de Usuarios';
$string['filterbyplan'] = 'Filtrar por Plan';
$string['filterbystatus'] = 'Filtrar por Estado';
$string['allplans'] = 'Todos los Planes';
$string['allstatuses'] = 'Todos los Estados';
$string['noplandata'] = 'No hay datos disponibles.';
$string['inprogress'] = 'En Progreso';
$string['aging'] = 'Por Vencer';
$string['viewusers'] = 'Ver Usuarios';

// Mensajes de recordatorio.
$string['task_sendreminders'] = 'Enviar recordatorios de planes de aprendizaje';
$string['messageprovider:planreminder'] = 'Notificaciones de recordatorio de planes de aprendizaje';
$string['remindersubject'] = 'Recordatorio: Completar "{$a->coursename}" - {$a->daysremaining} días restantes';
$string['reminderbody'] = 'Hola {$a->username},

Este es un recordatorio de que tienes {$a->daysremaining} día(s) restantes para completar el curso "{$a->coursename}" como parte de tu plan de aprendizaje "{$a->planname}".

Fecha de vencimiento: {$a->duedate}

Por favor inicia sesión en Moodle y completa este curso antes de la fecha de vencimiento.

Gracias.';
