<?php
class EvalKitUtils
{
    function isUserStudent($user_id)
    {
		if (!empty($_SESSION['ekiamstudent']) && $_SESSION['ekiamstudent']=='TRUE')
		{
			return true;
		}

		$config = get_config('blocks/evaluation_kit_sso');
		$studentRolesCsv = 'student'; //$config->EvalKitstudentroles;
		if (empty($studentRolesCsv))
		{
			$studentRolesCsv = 'student';
		}
		$studentRoles = explode(',',$studentRolesCsv);

		$courses = enrol_get_my_courses(NULL, 'visible DESC, fullname ASC');
		foreach ($courses as $course) 
		{
			$course_id = $course->id;
			$rolename  = 'student';
			$myRoles = get_user_roles_in_course($user_id, $course_id);
			$myRoles = explode(',',$myRoles);

			foreach ($studentRoles as $sr) 
			{
				foreach ($myRoles as $mr) 
				{
					if (strlen(stristr($mr, $sr)) > 0) 
					{
						$_SESSION['ekiamstudent'] = 'TRUE';
						return true;
					}
				}
			}
		}
		$SESSION['ekiamstudent'] = 'FALSE';
		return false;
    }
}