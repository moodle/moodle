<?PHP

function hotpot_upgrade($oldversion) {
	global $CFG;

	$ok = true;

	// update from HotPot v1 to HotPot v2
	if ($oldversion < 2005031400) {
		$ok = $ok && hotpot_get_update_to_v2();
		$ok = $ok && hotpot_update_to_v2_from_v1();
	}

	// update to HotPot v2.1
	if ($oldversion < 2005090700) {
		$ok = $ok && hotpot_get_update_to_v2();
		$ok = $ok && hotpot_update_to_v2_1();
	}
	return $ok;
}
function hotpot_get_update_to_v2() {
	global $CFG;
	$filepath = "$CFG->dirroot/mod/hotpot/db/update_to_v2.php";
	if (file_exists($filepath) && is_readable($filepath)) {
		include_once $filepath;
		$ok = true;
	} else {
		$ok = false;
	}
	return $ok;
}
?>
