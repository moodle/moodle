Schriftarten
------------

Dieses Verzeichnis enthält Schrifarten,
die verwendet werden, wenn Bilder mit Text erstellt werden.
Die einzige, die z.Zt. verwendet wird, ist default.ttf.

Wenn eine Sprache nicht über die Schriftart verfügt, 
wird stattdessen /lang/en/fonts/default.ttf verwendet.  

Multibyte-Strings müssen decodiert werden, 
weil die Truetype Routinen ISO-Fonts oder Unicode Zeichenketten erwarten.  
Wenn eine Datei lang_decode.php existiert, die eine Funktion
lang_decode() enthält, wird sie bei jeder Zeichenkette verwendet.