<?PHP //$Id$
	//This php script contains all the stuff to backup/restore
	//quiz mods

	//-----------------------------------------------------------
	// This is the "graphical" structure of the hotpot mod:
	//-----------------------------------------------------------
	//
	//                        hotpot
	//                  (CL, pk->id, files)
	//                           |
	//            +--------------+--------------+
	//            |                             |
	//            |                             |
	//    hotpot_attempts                 hotpot_questions
	//      (UL, pk->id,                    (UL, pk->id,
	//       fk->hotpot)                  fk->hotpot, text)
	//            |                             |        |
	//            |                             |        |
	//            +--------------+--------------+        |
	//                           |                       |
	//                           |                       |
	//                   hotpot_responses                |
	//                     (UL, pk->id,                  |
	//                 fk->attempt, question,            |
	//                correct, wrong, ignored)           |
	//                           |                       |
	//                           |                       |
	//                           +-----------+-----------+
	//                                       |
	//                                hotpot_strings
	//                                 (UL, pk->id)
	//
	// Meaning: pk->primary key field of the table
	//          fk->foreign key to link with parent
	//          nt->nested field (recursive data)
	//          CL->course level info
	//          UL->user level info
	//          files->table may have files
	//
	//-----------------------------------------------------------
	// It is not necessary to backup "questions", "responses" 
	// and "strings", because they can be restored from the 
	// "details" field of the "attempts" records
	//-----------------------------------------------------------

	function hotpot_backup_mods($bf, $preferences) {

		$level = 3;
		$status = true;

		$table = 'hotpot';
		$field = 'course';
		$value = $preferences->backup_course;

		$modtype = 'hotpot';

		$records_tag = '';
		$records_tags = array();

		$record_tag = 'MOD';
		$record_tags = array('MODTYPE'=>$modtype);

		$excluded_tags = array();

		$more_backup = '';
		if ($preferences->mods[$modtype]->userinfo) {
			$more_backup .= $modtype.'_backup_attempts($bf, $record, $level, $status);';
		}

		return hotpot_backup_records(
			$bf, $status, $level, 
			$table, $field, $value, 
			$records_tag, $records_tags,
			$record_tag, $record_tags,
			$excluded_tags, $more_backup
		);
	}
	function hotpot_backup_attempts($bf, &$parent, $level, $status) {
		// $parent is a reference to a hotpot record

		$table = 'hotpot_attempts';
		$field = 'hotpot';
		$value = $parent->id;

		$records_tag = 'ATTEMPT_DATA';
		$records_tags = array();

		$record_tag = 'ATTEMPT';
		$record_tags = array();

		$more_backup = '';
		$excluded_tags = array();

		return hotpot_backup_records(
			$bf, $status, $level, 
			$table, $field, $value, 
			$records_tag, $records_tags,
			$record_tag, $record_tags,
			$excluded_tags, $more_backup
		);
	}

	function hotpot_backup_records(&$bf, $status, $level, $table, $field, $value, $records_tag, $records_tags, $record_tag, $record_tags, $excluded_tags, $more_backup) {

		// If any of the "fwrite" statements fail, 
		// no further "fwrite"s will be attempted
		// and the function returns "false".
		// Otherwise, the function returns "true".

		if ($status && ($records = get_records($table, $field, $value, 'id'))) {

			// start a group of records
			if ($records_tag) {
				$status = $status && fwrite($bf, start_tag($records_tag, $level, true));
				$level++;

				foreach ($records_tags as $tag) {
					$status = $status && fwrite($bf, full_tag($tag, $level, false, $value));
				}
			}

			foreach ($records as $record) {

				// start a single record
				if ($record_tag) {
					$status = $status && fwrite($bf, start_tag($record_tag, $level, true));
					$level++;

					foreach ($record_tags as $tag=>$value) {
						$status = $status && fwrite($bf, full_tag($tag, $level, false, $value));
					}
				}

				// backup fields in this record
				$tags = get_object_vars($record);
				foreach ($tags as $tag=>$value) {
					if (!is_numeric($tag) && !in_array($tag, $excluded_tags)) {
						$status = $status && fwrite($bf, full_tag($tag, $level, false, $value));
					}
				}

				// backup related records, if required
				if ($more_backup) {
					eval($more_backup);
				}

				// end a single record
				if ($record_tag) {
					$level--;
					$status = $status && fwrite($bf, end_tag($record_tag, $level, true));
				}
			}

			// end a group of records
			if ($records_tag) {
				$level--;
				$status = $status && fwrite($bf, end_tag($records_tag, $level, true));
			}
		}

		return $status;
	}

	////Return an array of info (name, value)
	function hotpot_check_backup_mods($course, $user_data=false, $backup_unique_code) {
	
		// the course data
		$info[0][0] = get_string('modulenameplural','hotpot');
		$info[0][1] = count_records('hotpot', 'course', $course);
		
		// the user_data, if requested
		if ($user_data) {
			global $CFG;
			$table = "{$CFG->prefix}hotpot h, {$CFG->prefix}hotpot_attempts a";
			$select = "h.course = $course AND h.id = a.hotpot"; 
			
			$info[1][0] = get_string('attempts', 'quiz');
			$info[1][1] = count_records_sql("SELECT COUNT(*) FROM $table WHERE $select");
		}
		
		return $info;
	}

?>
