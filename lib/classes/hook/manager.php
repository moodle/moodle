<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core\hook;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Hook manager implementing "Dispatcher" and "Event Provider" from PSR-14.
 *
 * Due to class/method naming restrictions and collision with
 * Moodle events the definitions from PSR-14 should be interpreted as:
 *
 *  1. Event --> Hook
 *  2. Listener --> Hook callback
 *  3. Emitter --> Hook emitter
 *  4. Dispatcher --> Hook dispatcher - implemented in manager::dispatch()
 *  5. Listener Provider --> Hook callback provider - implemented in manager::get_callbacks_for_hook()
 *
 * Note that technically any object can be a hook, but it is recommended
 * to put all hook classes into \component_name\hook namespaces and
 * each hook should implement \core\hook\described_hook interface.
 *
 * @package   core
 * @author    Petr Skoda
 * @copyright 2022 Open LMS
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class manager implements
    EventDispatcherInterface,
    ListenerProviderInterface {

    /** @var ?manager the one instance of listener provider and dispatcher */
    private static $instance = null;

    /** @var array list of callback definitions for each hook class. */
    private $allcallbacks = [];

    /** @var array list of all deprecated lib.php plugin callbacks. */
    private $alldeprecations = [];

    /** @var array list of redirected callbacks in PHPUnit tests */
    private $redirectedcallbacks = [];

    /**
     * Constructor can be used only from factory methods.
     */
    private function __construct() {
    }

    /**
     * Factory method, returns instance of manager that serves
     * as hook dispatcher and callback provider.
     *
     * @return self
     */
    public static function get_instance(): manager {
        if (!self::$instance) {
            self::$instance = new self();
            self::$instance->init_standard_callbacks();
        }
        return self::$instance;
    }

    /**
     * Factory method for testing of hook manager in PHPUnit tests.
     *
     * @param array $componentfiles list of hook callback files for each component.
     * @return self
     */
    public static function phpunit_get_instance(array $componentfiles): manager {
        if (!PHPUNIT_TEST) {
            throw new \coding_exception('Invalid call of manager::phpunit_get_instance() outside of tests');
        }
        $instance = new self();
        $instance->load_callbacks($componentfiles);
        return $instance;
    }

    /**
     * Override hook callbacks for testing purposes.
     *
     * @param string $hookname
     * @param callable $callback
     * @return void
     */
    public function phpunit_redirect_hook(string $hookname, callable $callback): void {
        if (!PHPUNIT_TEST) {
            throw new \coding_exception('Invalid call of manager::phpunit_redirect_hook() outside of tests');
        }
        $this->redirectedcallbacks[$hookname] = $callback;
    }

    /**
     * Cancel all redirections of hook callbacks.
     *
     * @return void
     */
    public function phpunit_stop_redirections(): void {
        if (!PHPUNIT_TEST) {
            throw new \coding_exception('Invalid call of manager::phpunit_stop_redirections() outside of tests');
        }
        $this->redirectedcallbacks = [];
    }

    /**
     * Returns list of callbacks for given hook name.
     *
     * NOTE: this is the "Listener Provider" described in PSR-14,
     * instead of instance parameter it uses real PHP class names.
     *
     * @param string $hookclassname PHP class name of hook
     * @return array list of callback definitions
     */
    public function get_callbacks_for_hook(string $hookclassname): array {
        return $this->allcallbacks[$hookclassname] ?? [];
    }

    /**
     * Returns list of all callbacks found in db/hooks.php files.
     *
     * @return iterable
     */
    public function get_all_callbacks(): iterable {
        return $this->allcallbacks;
    }

    /**
     * Get the list of listeners for the specified event.
     *
     * @param object $event The object being listened to (aka hook).
     * @return iterable<callable>
     *   An iterable (array, iterator, or generator) of callables.  Each
     *   callable MUST be type-compatible with $event.
     *   Please note that in Moodle the callable must be a string.
     */
    public function getListenersForEvent(object $event): iterable {
        // Callbacks are sorted by priority, highest first at load-time.
        $hookclassname = get_class($event);
        $callbacks = $this->get_callbacks_for_hook($hookclassname);

        if (count($callbacks) === 0) {
            // Nothing is interested in this hook.
            return new \EmptyIterator();
        }

        foreach ($callbacks as $definition) {
            if ($definition['disabled']) {
                continue;
            }
            $callback = $definition['callback'];

            if ($this->is_callback_valid($definition['component'], $callback)) {
                yield $callback;
            }
        }
    }

    /**
     * Verify that callback is valid.
     *
     * @param string $component
     * @param string $callback
     * @return bool
     */
    private function is_callback_valid(string $component, string $callback): bool {
        [$callbackclass, $callbackmethod] = explode('::', $callback, 2);
        if (!class_exists($callbackclass)) {
            debugging(
                "Hook callback definition contains invalid 'callback' class name in '$component'. " .
                    "Callback class '{$callbackclass}' not found.",
                DEBUG_DEVELOPER,
            );
            return false;
        }
        $rc = new \ReflectionClass($callbackclass);
        if (!$rc->hasMethod($callbackmethod)) {
            debugging(
                "Hook callback definition contains invalid 'callback' method name in '$component'. " .
                    "Callback method not found.",
                DEBUG_DEVELOPER,
            );
            return false;
        }

        $rcm = $rc->getMethod($callbackmethod);
        if (!$rcm->isStatic()) {
            debugging(
                "Hook callback definition contains invalid 'callback' method name in '$component'. " .
                    "Callback method not a static method.",
                DEBUG_DEVELOPER,
            );
            return false;
        }

        if (!is_callable($callback, false, $callablename)) {
            debugging(
                "Cannot execute callback '$callablename' from '$component'" .
                    "Callback method not callable.",
                DEBUG_DEVELOPER
            );
            return false;
        }

        return true;
    }

    /**
     * Returns the list of Hook class names that have registered callbacks.
     *
     * @return array
     */
    public function get_hooks_with_callbacks(): array {
        return array_keys($this->allcallbacks);
    }

    /**
     * Provide all relevant listeners with an event to process.
     *
     * @param object $event The object to process (aka hook).
     * @return object The Event that was passed, now modified by listeners.
     */
    public function dispatch(object $event): object {
        // We can dispatch only after the lib/setup.php includes,
        // that is right before the database connection is made,
        // the MUC caches need to be working already.
        if (!function_exists('setup_DB')) {
            debugging('Hooks cannot be dispatched yet', DEBUG_DEVELOPER);
            return $event;
        }

        if (PHPUNIT_TEST) {
            $hookclassname = get_class($event);
            if (isset($this->redirectedcallbacks[$hookclassname])) {
                call_user_func($this->redirectedcallbacks[$hookclassname], $event);
                return $event;
            }
        }

        $callbacks = $this->getListenersForEvent($event);

        if (empty($callbacks)) {
            // Nothing is interested in this hook.
            return $event;
        }

        foreach ($callbacks as $callback) {
            // Note: PSR-14 states:
            // If passed a Stoppable Event, a Dispatcher
            // MUST call isPropagationStopped() on the Event before each Listener has been called.
            // If that method returns true it MUST return the Event to the Emitter immediately and
            // MUST NOT call any further Listeners. This implies that if an Event is passed to the
            // Dispatcher that always returns true from isPropagationStopped(), zero listeners will be called.
            // Ergo, we check for a stopped event before calling each listener, not afterwards.
            if ($event instanceof StoppableEventInterface) {
                if ($event->isPropagationStopped()) {
                    return $event;
                }
            }

            call_user_func($callback, $event);
        }

        // Developers need to be careful to not create infinite loops in hook callbacks.
        return $event;
    }

    /**
     * Initialise list of all callbacks for each hook.
     *
     * @return void
     */
    private function init_standard_callbacks(): void {
        global $CFG;

        $this->allcallbacks = [];
        $this->alldeprecations = [];

        $cache = null;
        // @codeCoverageIgnoreStart
        if (!PHPUNIT_TEST && !CACHE_DISABLE_ALL) {
            $cache = \cache::make('core', 'hookcallbacks');
            $callbacks = $cache->get('callbacks');
            $deprecations = $cache->get('deprecations');
            $overrideshash = $cache->get('overrideshash');

            $usecache = is_array($callbacks);
            $usecache = $usecache && is_array($deprecations);
            $usecache = $usecache && $this->calculate_overrides_hash() === $overrideshash;
            if ($usecache) {
                $this->allcallbacks = $callbacks;
                $this->alldeprecations = $deprecations;
                return;
            }
        }
        // @codeCoverageIgnoreEnd

        // Get list of all files with callbacks, one per component.
        $components = ['core' => "{$CFG->dirroot}/lib/db/hooks.php"];
        $plugintypes = \core_component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $plugintypedir) {
            $plugins = \core_component::get_plugin_list($plugintype);
            foreach ($plugins as $pluginname => $plugindir) {
                if (!$plugindir) {
                    continue;
                }

                $components["{$plugintype}_{$pluginname}"] = "{$plugindir}/db/hooks.php";
            }
        }

        // Load the callbacks and apply overrides.
        $this->load_callbacks($components);

        if ($cache) {
            $cache->set('callbacks', $this->allcallbacks);
            $cache->set('deprecations', $this->alldeprecations);
            $cache->set('overrideshash', $this->calculate_overrides_hash());
        }
    }

    /**
     * Load callbacks from component db/hooks.php files.
     *
     * @param array $componentfiles list of all components with their callback files
     * @return void
     */
    private function load_callbacks(array $componentfiles): void {
        $this->allcallbacks = [];
        $this->alldeprecations = [];

        array_map(
            [$this, 'add_component_callbacks'],
            array_keys($componentfiles),
            $componentfiles,
        );
        $this->load_callback_overrides();
        $this->prioritise_callbacks();
        $this->fetch_deprecated_callbacks();
    }

    /**
     * In extremely special cases admins may decide to override callbacks via config.php setting.
     */
    private function load_callback_overrides(): void {
        global $CFG;

        if (!property_exists($CFG, 'hooks_callback_overrides')) {
            return;
        }

        if (!is_iterable($CFG->hooks_callback_overrides)) {
            debugging('hooks_callback_overrides must be an array', DEBUG_DEVELOPER);
            return;
        }

        foreach ($CFG->hooks_callback_overrides as $hookclassname => $overrides) {
            if (!is_iterable($overrides)) {
                debugging('hooks_callback_overrides must be an array of arrays', DEBUG_DEVELOPER);
                continue;
            }

            if (!array_key_exists($hookclassname, $this->allcallbacks)) {
                debugging('hooks_callback_overrides must be an array of arrays with existing hook classnames', DEBUG_DEVELOPER);
                continue;
            }

            foreach ($overrides as $callback => $override) {
                if (!is_array($override)) {
                    debugging('hooks_callback_overrides must be an array of arrays', DEBUG_DEVELOPER);
                    continue;
                }

                $found = false;
                foreach ($this->allcallbacks[$hookclassname] as $index => $definition) {
                    if ($definition['callback'] === $callback) {
                        if (isset($override['priority'])) {
                            $definition['defaultpriority'] = $definition['priority'];
                            $definition['priority'] = (int) $override['priority'];
                        }

                        if (!empty($override['disabled'])) {
                            $definition['disabled'] = true;
                        }

                        $this->allcallbacks[$hookclassname][$index] = $definition;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    debugging("Unable to find callback '{$callback}' for '{$hookclassname}'", DEBUG_DEVELOPER);
                }
            }
        }
    }

    /**
     * Calculate a hash of the overrides.
     * This is used to inform if the overrides have changed, which invalidates the cache.
     *
     * Overrides are only configured in config.php where there is no other mechanism to invalidate the cache.
     *
     * @return null|string
     */
    private function calculate_overrides_hash(): ?string {
        global $CFG;

        if (!property_exists($CFG, 'hooks_callback_overrides')) {
            return null;
        }

        if (!is_iterable($CFG->hooks_callback_overrides)) {
            return null;
        }

        return sha1(json_encode($CFG->hooks_callback_overrides));
    }

    /**
     * Prioritise the callbacks.
     */
    private function prioritise_callbacks(): void {
        // Prioritise callbacks.
        foreach ($this->allcallbacks as $hookclassname => $hookcallbacks) {
            \core_collator::asort_array_of_arrays_by_key($hookcallbacks, 'priority', \core_collator::SORT_NUMERIC);
            $hookcallbacks = array_reverse($hookcallbacks);
            $this->allcallbacks[$hookclassname] = $hookcallbacks;
        }
    }

    /**
     * Fetch the list of callbacks that this hook replaces.
     */
    private function fetch_deprecated_callbacks(): void {
        $candidates = self::discover_known_hooks();

        /** @var class-string<deprecated_callback_replacement> $hookclassname */
        foreach (array_keys($candidates) as $hookclassname) {
            if (!class_exists($hookclassname)) {
                continue;
            }
            if (!is_subclass_of($hookclassname, \core\hook\deprecated_callback_replacement::class)) {
                continue;
            }
            $deprecations = $hookclassname::get_deprecated_plugin_callbacks();
            if (!$deprecations) {
                continue;
            }
            foreach ($deprecations as $deprecation) {
                $this->alldeprecations[$deprecation][] = $hookclassname;
            }
        }
    }

    /**
     * Add hook callbacks from file.
     *
     * @param string $component component where hook callbacks are defined
     * @param string $hookfile file with list of all callbacks for component
     * @return void
     */
    private function add_component_callbacks(string $component, string $hookfile): void {
        if (!file_exists($hookfile)) {
            return;
        }

        $parsecallbacks = function($hookfile) {
            $callbacks = [];
            include($hookfile);
            return $callbacks;
        };

        $callbacks = $parsecallbacks($hookfile);

        if (!is_array($callbacks) || !$callbacks) {
            return;
        }

        foreach ($callbacks as $callbackdata) {
            if (empty($callbackdata['hook'])) {
                debugging("Hook callback definition requires 'hook' name in '$component'", DEBUG_DEVELOPER);
                continue;
            }

            $callbackmethod = $this->normalise_callback($component, $callbackdata);
            if ($callbackmethod === null) {
                continue;
            }

            $callback = [
                'callback' => $callbackmethod,
                'component' => $component,
                'disabled' => false,
                'priority' => 100,
            ];

            if (isset($callbackdata['priority'])) {
                $callback['priority'] = (int) $callbackdata['priority'];
            }

            $hook = ltrim($callbackdata['hook'], '\\'); // Normalise hook class name.
            $this->allcallbacks[$hook][] = $callback;
        }
    }

    /**
     * Normalise the callback class::method value.
     *
     * @param string $component
     * @param array $callback
     * @return null|string
     */
    private function normalise_callback(string $component, array $callback): ?string {
        if (empty($callback['callback'])) {
            debugging("Hook callback definition requires 'callback' callable in '$component'", DEBUG_DEVELOPER);
            return null;
        }
        $classmethod = $callback['callback'];
        if (!is_string($classmethod)) {
            debugging("Hook callback definition contains invalid 'callback' string in '$component'", DEBUG_DEVELOPER);
            return null;
        }
        if (!str_contains($classmethod, '::')) {
            debugging(
                "Hook callback definition contains invalid 'callback' static class method string in '$component'",
                DEBUG_DEVELOPER
            );
            return null;
        }

        // Normalise the callback class::method name, we use it later as an identifier.
        $classmethod = ltrim($classmethod, '\\');

        return $classmethod;
    }

    /**
     * Is the plugin callback from lib.php deprecated by any hook?
     *
     * @param string $plugincallback short callback name without the component prefix
     * @return bool
     */
    public function is_deprecated_plugin_callback(string $plugincallback): bool {
        return isset($this->alldeprecations[$plugincallback]);
    }

    /**
     * Is there a hook callback in component that deprecates given lib.php plugin callback?
     *
     * NOTE: if there is both hook and deprecated callback then we ignore the old callback
     * to allow compatibility of contrib plugins with multiple Moodle branches.
     *
     * @param string $component
     * @param string $plugincallback short callback name without the component prefix
     * @return bool
     */
    public function is_deprecating_hook_present(string $component, string $plugincallback): bool {
        if (!isset($this->alldeprecations[$plugincallback])) {
            return false;
        }

        foreach ($this->alldeprecations[$plugincallback] as $hookclassname) {
            if (!isset($this->allcallbacks[$hookclassname])) {
                continue;
            }
            foreach ($this->allcallbacks[$hookclassname] as $definition) {
                if ($definition['component'] === $component) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns list of hooks discovered through hook namespaces or discovery agents.
     *
     * The hooks overview page includes also all other classes that are
     * referenced in callback registrations in db/hooks.php files, those
     * are not included here.
     *
     * @return array hook class names
     */
    public static function discover_known_hooks(): array {
        // All classes in hook namespace of core and plugins, unless plugin has a discovery agent.
        $hooks = \core\hooks::discover_hooks();

        // Look for hooks classes in all plugins that implement discovery agent interface.
        foreach (\core_component::get_component_names() as $component) {
            $classname = "{$component}\\hooks";

            if (!class_exists($classname)) {
                continue;
            }

            if (!is_subclass_of($classname, discovery_agent::class)) {
                continue;
            }

            $hooks = array_merge($hooks, $classname::discover_hooks());
        }

        return $hooks;
    }
}
