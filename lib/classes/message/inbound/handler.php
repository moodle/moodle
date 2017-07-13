<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Abstract class describing Inbound Message Handlers.
 *
 * @package    core_message
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\message\inbound;

/**
 * Abstract class describing Inbound Message Handlers.
 *
 * @copyright  2014 Andrew NIcols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read int $id The ID of the handler in the database
 * @property-read string $component The component of this handler
 * @property-read int $defaultexpiration Default expiration of new addresses for this handler
 * @property-read string $description The description of this handler
 * @property-read string $name The name of this handler
 * @property-read bool $validateaddress Whether the address validation is a requiredment
 * @property-read bool $enabled Whether this handler is currently enabled
 * @property-read string $classname The name of handler class
 */
abstract class handler {

    /**
     * @var int $id The id of the handler in the database.
     */
    private $id = null;

    /**
     * @var string $component The component to which this handler belongs.
     */
    private $component = '';

    /**
     * @var int $defaultexpiration The default expiration time to use when created a new key.
     */
    private $defaultexpiration = WEEKSECS;

    /**
     * @var bool $validateaddress Whether to validate the sender address when processing this handler.
     */
    private $validateaddress = true;

    /**
     * @var bool $enabled Whether this handler is currently enabled.
     */
    private $enabled = false;

    /**
     * @var $accessibleproperties A list of the properties which can be read.
     */
    private $accessibleproperties = array(
        'id' => true,
        'component' => true,
        'defaultexpiration' => true,
        'validateaddress' => true,
        'enabled' => true,
    );

    /**
     * Magic getter to fetch the specified key.
     *
     * @param string $key The name of the key to retrieve
     */
    public function __get($key) {
        // Some properties have logic behind them.
        $getter = 'get_' . $key;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        // Check for a commonly accessibly property.
        if (isset($this->accessibleproperties[$key])) {
            return $this->$key;
        }

        // Unknown property - bail.
        throw new \coding_exception('unknown_property ' . $key);
    }

    /**
     * Set the id name.
     *
     * @param int $id The id to set
     * @return int The newly set id
     */
    public function set_id($id) {
        return $this->id = $id;
    }

    /**
     * Set the component name.
     *
     * @param string $component The component to set
     * @return string The newly set component
     */
    public function set_component($component) {
        return $this->component = $component;
    }

    /**
     * Whether the current handler allows changes to the address validation
     * setting.
     *
     * By default this will return true, but for some handlers it may be
     * necessary to disallow such changes.
     *
     * @return boolean
     */
    public function can_change_validateaddress() {
        return true;
    }

    /**
     * Set whether validation of the address is required.
     *
     * @param bool $validateaddress The new state of validateaddress
     * @return bool
     */
    public function set_validateaddress($validateaddress) {
        return $this->validateaddress = $validateaddress;
    }

    /**
     * Whether the current handler allows changes to expiry of the generated email address.
     *
     * By default this will return true, but for some handlers it may be
     * necessary to disallow such changes.
     *
     * @return boolean
     */
    public function can_change_defaultexpiration() {
        return true;
    }

    /**
     * Whether this handler can be disabled (or enabled).
     *
     * By default this will return true, but for some handlers it may be
     * necessary to disallow such changes. For example, a core handler to
     * handle rejected mail validation should not be disabled.
     *
     * @return boolean
     */
    public function can_change_enabled() {
        return true;
    }

    /**
     * Set the enabled name.
     *
     * @param bool $enabled The new state of enabled
     * @return bool
     */
    public function set_enabled($enabled) {
        return $this->enabled = $enabled;
    }

    /**
     * Set the default validity for new keys.
     *
     * @param int $period The time in seconds before a key expires
     * @return int
     */
    public function set_defaultexpiration($period) {
        return $this->defaultexpiration = $period;
    }

    /**
     * Get the non-namespaced name of the current class.
     *
     * @return string The classname
     */
    private function get_classname() {
        $classname = get_class($this);
        if (strpos($classname, '\\') !== 0) {
            $classname = '\\' . $classname;
        }

        return $classname;
    }

    /**
     * Return a description for the current handler.
     *
     * @return string
     */
    protected abstract function get_description();

    /**
     * Return a name for the current handler.
     * This appears in the admin pages as a human-readable name.
     *
     * @return string
     */
    protected abstract function get_name();

    /**
     * Process the message against the current handler.
     *
     * @param \stdClass $record The Inbound Message Handler record
     * @param \stdClass $messagedata The message data
     */
    public abstract function process_message(\stdClass $record, \stdClass $messagedata);

    /**
     * Return the content of any success notification to be sent.
     * Both an HTML and Plain Text variant must be provided.
     *
     * If this handler does not need to send a success notification, then
     * it should return a falsey value.
     *
     * @param \stdClass $messagedata The message data.
     * @param \stdClass $handlerresult The record for the newly created post.
     * @return \stdClass with keys `html` and `plain`.
     */
    public function get_success_message(\stdClass $messagedata, $handlerresult) {
        return false;
    }

    /**
     * Remove quoted message string from the text (NOT HTML) message.
     *
     * @param \stdClass $messagedata The Inbound Message record
     *
     * @return array message and message format to use.
     */
    protected static function remove_quoted_text($messagedata) {
        if (!empty($messagedata->plain)) {
            $text = $messagedata->plain;
        } else {
            $text = html_to_text($messagedata->html);
        }
        $messageformat = FORMAT_PLAIN;

        $splitted = preg_split("/\n|\r/", $text);
        if (empty($splitted)) {
            return array($text, $messageformat);
        }

        $i = 0;
        $flag = false;
        foreach ($splitted as $i => $element) {
            if (stripos($element, ">") === 0) {
                // Quoted text found.
                $flag = true;
                // Remove 2 non empty line before this.
                for ($j = $i - 1; ($j >= 0); $j--) {
                    $element = $splitted[$j];
                    if (!empty($element)) {
                        unset($splitted[$j]);
                        break;
                    }
                }
                break;
            }
        }
        if ($flag) {
            // Quoted text was found.
            // Retrieve everything from the start until the line before the quoted text.
            $splitted = array_slice($splitted, 0, $i-1);

            // Strip out empty lines towards the end, since a lot of clients add a huge chunk of empty lines.
            $reverse = array_reverse($splitted);
            foreach ($reverse as $i => $line) {
                if (empty($line)) {
                    unset($reverse[$i]);
                } else {
                    // Non empty line found.
                    break;
                }
            }

            $replaced = implode(PHP_EOL, array_reverse($reverse));
            $message = trim($replaced);
        } else {
            // No quoted text, fallback to original text.
            if (!empty($messagedata->html)) {
                $message = $messagedata->html;
                $messageformat = FORMAT_HTML;
            } else {
                $message = $messagedata->plain;
            }
        }
        return array($message, $messageformat);
    }
}
