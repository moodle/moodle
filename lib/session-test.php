<?PHP
/// This is a tiny standalone diagnostic script to test that sessions 
/// are working correctly on a given server.  
///
/// Just run it from a browser.   The first time you run it will 
/// set a new variable, and after that it will try to find it again.
/// The random number is just to prevent browser caching.

session_start();

if (!isset($_SESSION["test"])) {   // First time you call it.
    echo "<P>No session found - starting a session now.";
    $_SESSION["test"] = "welcome back!";

} else {                           // Subsequent times you call it
    echo "<P>Session found - ".$_SESSION["test"];
    echo "<P>Sessions are working correctly</P>";
}

echo "<P><A HREF=\"session-test.php?random=".rand(1,10000)."\">Reload this page</A></P>";

?>
