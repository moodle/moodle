<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 3.2//EN">
<html>
<head>
    <title>Example use of EvalMath</title>
</head>

<body>
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
        y(x) = <input type="text" name="function" value="<?=(isset($_POST['function']) ? htmlspecialchars($_POST['function']) : '')?>">
        <input type="submit">
    </form>
    <?
if (isset($_POST['function']) and $_POST['function']) {
	include('evalmath.class.php');
	$m = new EvalMath;
	$m->suppress_errors = true;
	if ($m->evaluate('y(x) = ' . $_POST['function'])) {
		print "\t<table border=\"1\">\n";
		print "\t\t<tr><th>x</th><th>y(x)</th>\n";
		for ($x = -2; $x <= 2; $x+=.2) {
			$x = round($x, 2);
			print "\t\t<tr><td>$x</td><td>" . $m->e("y($x)") . "</td></tr>\n";
		}
		print "\t</table>\n";
	} else {
		print "\t<p>Could not evaluate function: " . $m->last_error . "</p>\n";
	}
}
?>
</body>
</html>
