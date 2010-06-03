Description of HTML Purifier v4.1.0 library import into Moodle

Changes:
 * HMLTModule/Text.php - added  <nolink>, <tex>, <lang> and <algebra> tags
 * HMLTModule/XMLCommonAttributes.php - remove xml:lang - needed for multilang
 * AttrDef/Lang.php - relax lang check - needed for multilang
 * Lexer.php - Subverted line break normalisation (requires setting: Output.Newline to \n)

skodak
