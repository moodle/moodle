<?PHP
function hotpot_upgrade($oldversion) {
	global $CFG;
	$ok = true;

	// set path to update functions
	$update_to_v2 = "$CFG->dirroot/mod/hotpot/db/update_to_v2.php";

	// update from HotPot v1 to HotPot v2
	if ($oldversion < 2005031400) {
		require_once $update_to_v2;
		$ok = $ok && hotpot_update_to_v2_from_v1();
	}
	// update to HotPot v2.1
	if ($oldversion < 2005090700) {
		require_once $update_to_v2;
		$ok = $ok && hotpot_update_to_v2_1();
	}
	if ($oldversion > 2005031419 && $oldversion < 2005090702) {
		// update to from HotPot v2.1.0 or v2.1.1
		require_once $update_to_v2;
		$ok = $ok && hotpot_update_to_v2_1_2();
	}
	if ($oldversion < 2005090706) {
		require_once $update_to_v2;
		$ok = $ok && hotpot_update_to_v2_1_6();
	}
	if ($oldversion < 2005090708) {
		require_once $update_to_v2;
		$ok = $ok && hotpot_update_to_v2_1_8();
	}
	if ($oldversion < 2006042103) {
		require_once $update_to_v2;
		$ok = $ok && hotpot_update_to_v2_1_16();
	}
	if ($oldversion < 2006042602) {
		require_once $update_to_v2;
		$ok = $ok && hotpot_update_to_v2_1_17();
	}
	if ($oldversion < 2006042803) {
		require_once $update_to_v2;
		$ok = $ok && hotpot_update_to_v2_1_18();
	}


        if ($oldversion < 2006050201) {

            modify_database('', 'ALTER TABLE prefix_hotpot
                ALTER COLUMN studentfeedbackurl SET DEFAULT \'\',
                ALTER COLUMN studentfeedbackurl SET NOT NULL,
                ALTER COLUMN clickreporting SET DEFAULT 0,
                ALTER COLUMN studentfeedback SET DEFAULT 0');

            modify_database('', 'ALTER TABLE prefix_hotpot_strings
                ALTER COLUMN string SET DEFAULT \'\'');

            modify_database('', 'ALTER TABLE prefix_hotpot_responses
                ALTER COLUMN hints TYPE int2,
                ALTER COLUMN hints SET DEFAULT 0,
                ALTER COLUMN ignored SET DEFAULT \'\',
                ALTER COLUMN ignored SET NOT NULL,
                ALTER COLUMN score TYPE int2,
                ALTER COLUMN score SET DEFAULT 0,
                ALTER COLUMN correct SET DEFAULT \'\',
                ALTER COLUMN correct SET NOT NULL,
                ALTER COLUMN weighting TYPE int2,
                ALTER COLUMN weighting SET DEFAULT 0,
                ALTER COLUMN wrong SET DEFAULT \'\',
                ALTER COLUMN wrong SET NOT NULL,
                ALTER COLUMN checks TYPE int2,
                ALTER COLUMN checks SET DEFAULT 0,
                ALTER COLUMN clues TYPE int2,
                ALTER COLUMN clues SET DEFAULT 0');

            modify_database('', 'ALTER TABLE prefix_hotpot_questions
                ALTER COLUMN "type" SET DEFAULT 0');

            modify_database('', 'ALTER TABLE prefix_hotpot_attempts
                ALTER COLUMN penalties TYPE smallint,
                ALTER COLUMN penalties SET DEFAULT 0,
                ALTER COLUMN score TYPE smallint,
                ALTER COLUMN score SET DEFAULT 0,
                ALTER COLUMN status SET DEFAULT 1');

        }


        return $ok;
}
?>
