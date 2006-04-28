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
	return $ok;
}
?>
