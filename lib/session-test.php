<?php
/// This is a tiny standalone diagnostic script to test that sessions 
/// are working correctly on a given server.  
///
/// Just run it from a browser.   The first time you run it will 
/// set a new variable, and after that it will try to find it again.
/// The random number is just to prevent browser caching.

session_start();

if (!isset($_SESSION["test"])) {   // First time you call it.
    echo "<p>No session found - starting a session now.";
    $_SESSION["test"] = "welcome back!";

} else {                           // Subsequent times you call it
    echo "<p>Session found - ".$_SESSION["test"];
    echo "</p><p>Sessions are working correctly</p>";
}

echo "<p><a href=\"session-test.php?random=".rand(1,10000)."\">Reload this page</a></p>";

?>
