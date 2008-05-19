Description of HTML Purifier v2.1.4 Lite library import into Moodle

Changes:
 * HMLTModule/Text.php - added  <nolink>, <tex>, <lang> and <algebra> tags
 * HMLTModule/XMLCommonAttributes.php - remove xml:lang - needed for multilang
 * AttrDef/Lang.php - relax lang check - needed for multilang
 * AttrDef/URI/Email/SimpleCheck.php - deleted to prevent errors on some systems, not used anyway

skodak

$Id$
