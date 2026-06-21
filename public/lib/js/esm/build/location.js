/**
 * Browser location utilities.
 *
 * Provides a mockable abstraction over `window.location` for navigation actions.
 *
 * @module     core/location
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */function n(i){window.location.assign(i)}export{n as redirect};
