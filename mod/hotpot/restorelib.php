<?PHP //$Id$
//This php script contains all the stuff to restore hotpot mods

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

	require_once ("$CFG->dirroot/mod/hotpot/lib.php");

	//This function executes all the restore procedure about this mod
	function hotpot_restore_mods($mod, $restore) {

		// this function is called by "restore_create_modules" (in "backup/restorelib.php") 
		// which is called by "backup/restore_execute.html" (included by "backup/restore.php")

		// $mod is an object
		// 	id           : id field in 'modtype' table
		// 	modtype      : 'hotpot'

		// $restore is an object
		// 	backup_unique_code : xxxxxxxxxx
		// 	file         : '/full/path/to/backupfile.zip'
		// 	mods         : an array of $modinfo's (see below)
		// 	restoreto    : 0=existing course (replace), 1=existing course (append), 2=new course
		// 	users        : 0=all, 1=course, 2=none
		// 	logs         : 0=no, 1=yes
		// 	user_files   : 0=no, 1=yes
		// 	course_files : 0=no, 1=yes
		// 	course_id    : id of course into which data is to be restored
		// 	deleting     : true if 'restoreto'==0, otherwise false
		// 	original_wwwroot : 'http://your.server.com/moodle'

		// $modinfo is an array
		//	'modname'    : array( 'restore'=> 0=no 1=yes, 'userinfo' => 0=no 1=yes)

		$status = true;

		//Get data record for this instance of the mod
		$data = backup_getid($restore->backup_unique_code, $mod->modtype, $mod->id);
		if ($data) {

			// $data is an object
			//	backup_code => xxxxxxxxxx,
			//	table_name  => 'hotpot',
			//	old_id      => xxx,
			//	new_id      => NULL,
			//	info        => array of info for this instance of the mod
		
			// short cut to xmlized info
			$info = &$data->info['MOD']['#'];

			// build the new record
			$hotpot = NULL;
			$hotpot->course = $restore->course_id;

			// don't include these fields in the hotpot record
			$excluded_TAGS = array('MODTYPE', 'ID', 'COURSE', 'ATTEMPT_DATA');

			// fill in the fields
			$TAGS = array_keys($info);
			foreach ($TAGS as $TAG) {

				if (!in_array($TAG, $excluded_TAGS)) {
					$tag = strtolower($TAG);
					$hotpot->$tag = backup_todb($info[$TAG][0]['#']);
				}
			}

			// insert the record
			$hotpot->id = insert_record ('hotpot', $hotpot);
			if (is_numeric($hotpot->id)) {

				// Do some output
				echo '<ul><li>'.get_string('modulename', 'hotpot').' &quot;'.$hotpot->name.'&quot;<br>';
				backup_flush(300);

				// save the new id (required for log retore later on)
				backup_putid($restore->backup_unique_code, $mod->modtype, $mod->id, $hotpot->id);

				// backup user info, if required
				if ($restore->mods[$mod->modtype]->userinfo) {

					// are we overwriting a course?
					if ($restore->deleting) {

						// remove previous attempts, questions and responses for this quiz
						$select = "hotpot='$hotpot->id'";
						if ($attempts = get_records_select('hotpot_attempts', $select)) {
							$ids = implode(',', array_keys($attempts));
							delete_records_select('hotpot_responses', "attempt IN ($ids)");
						}
						delete_records_select('hotpot_questions', $select);
						delete_records_select('hotpot_attempts', $select);
					}
			
					// don't transfer these fields to the attempt records
					$excluded_TAGS = array('hotpot');

					$i = 0;
					while ($status && isset($info['ATTEMPT_DATA'][$i]['#'])) {

						$ii = 0;
						while ($status && isset($info['ATTEMPT_DATA'][$i]['#']['ATTEMPT'][$ii]['#'])) {

							// shortcut to user info record
							$info_record = &$info['ATTEMPT_DATA'][$i]['#']['ATTEMPT'][$ii]['#'];

							$attempt = NULL;
							$attempt->hotpot = $hotpot->id;
		
							$TAGS = array_keys($info_record);
							foreach ($TAGS as $TAG) {
		
								if (!in_array($TAG, $excluded_TAGS)) {
		
									$value = backup_todb($info_record[$TAG][0]['#']);

									if ($TAG=='USERID') {
										$user = backup_getid($restore->backup_unique_code, 'user', $value);
										if ($user) {
											$value = $user->new_id;
										} else {
											$status = false; // this shouldn't happen
										}
									}
		
									$tag = strtolower($TAG);
									$attempt->$tag = $value;
								}
							} // end foreach $TAGS

							// store old attempt id
							$attempt->old_id = $attempt->id;
							unset($attempt->id);

							// add the attempt record
							$attempt->id = insert_record ('hotpot_attempts', $attempt);
							if (is_numeric($attempt->id)) {

								// save the new id (required for log retore later on)
								backup_putid($restore->backup_unique_code, 'hotpot_attempts', $attempt->old_id, $attempt->id);

								// remove slashes added by backup_todb(), otherwise xmlize() will complain
								$attempt->details = stripslashes($attempt->details);

								// add questions and responses in attempt $attempt->details
								hotpot_add_attempt_details($attempt);

							} else { // failed to insert $attempt record
								$status = false;
							}

							// do some output, if required
							if ($status) {
								if ($ii%10==0) {
									echo '.';
									if ($ii%200==0) {
										echo '<br>';
										backup_flush(300);
									}
								}
							}
		
							$ii++;
						} // end while $info_record

						$i++;
					} // end while $info_records
				}

				// Finalize ul		
				echo "</li></ul>";

			} else {
				// could not add hotpot record
				$status = false;
			}

		} else {
			// could not get $data for this hotpot quiz
			$status = false;
		}

		return $status;
	}

	//This function returns a log record with all the necessay transformations
	//done. It's used by restore_log_module() to restore modules log.
	function hotpot_restore_logs($restore, $log) {

		// assume the worst
		$status = false;

		switch ($log->action) {

			case "add":
			case "update":
			case "view":
				if ($log->cmid) {
					//Get the new_id of the module (to recode the info field)
					$mod = backup_getid($restore->backup_unique_code, $log->module, $log->info);
					if ($mod) {
						$log->url = "view.php?id=".$log->cmid;
						$log->info = $mod->new_id;
						$status = true;
					}
				}
			break;

			case "view all":
				$log->url = "index.php?id=".$log->course;
				$status = true;
			break;

			case "report":
				if ($log->cmid) {
					//Get the new_id of the module (to recode the info field)
					$mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
					if ($mod) {
						$log->url = "report.php?id=".$log->cmid;
						$log->info = $mod->new_id;
						$status = true;
					}
				}
			break;

			case "attempt":
			case "submit":
			case "review": 
				if ($log->cmid) {
					//Get the new_id of the module (to recode the info field)
					$mod = backup_getid($restore->backup_unique_code,$log->module,$log->info);
					if ($mod) {
						//Extract the attempt id from the url field
						$attemptid = substr(strrchr($log->url,"="),1);
						//Get the new_id of the attempt (to recode the url field)
						$attempt = backup_getid($restore->backup_unique_code,"hotpot_attempts",$attemptid);
						if ($attempt) { 
							$log->url = "review.php?id=".$log->cmid."&attempt=".$attempt->new_id;
							$log->info = $mod->new_id;
							$status = true;
						}
					}
				}
			break;

			default:
				// Oops, unknown $log->action
				print "<p>action (".$log->module."-".$log->action.") unknown. Not restored</p>";
			break;

		} // end switch
		
		return $status ? $log : false;
	}
?>
