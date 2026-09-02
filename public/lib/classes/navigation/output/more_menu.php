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

namespace core\navigation\output;

use core\navigation\navigation_node;
use renderable;
use renderer_base;
use templatable;
use custom_menu;

/**
 * more menu navigation renderable
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Adrian Greeve
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class more_menu implements renderable, templatable {

    protected $content;
    protected $navbarstyle;
    protected $haschildren;
    protected $istablist;

    /**
     * Constructor for this class.
     *
     * @param object $content Navigation objects.
     * @param string $navbarstyle class name.
     * @param bool $haschildren The content has children.
     * @param bool $istablist When true, the more menu should be rendered and behave with a tablist ARIA role.
     *                        If false, it's rendered with a menubar ARIA role. Defaults to false.
     */
    public function __construct(object $content, string $navbarstyle, bool $haschildren = true, bool $istablist = false) {
        $this->content = $content;
        $this->navbarstyle = $navbarstyle;
        $this->haschildren = $haschildren;
        $this->istablist = $istablist;
    }

    /**
     * Return data for rendering a template.
     *
     * @param renderer_base $output The output
     * @return array Data for rendering a template
     */
    public function export_for_template(renderer_base $output): array {
        $data = [
            'navbarstyle' => $this->navbarstyle,
            'istablist' => $this->istablist,
        ];

        // The secondary navigation view sets a context-aware menu title (Course menu, Activity
        // menu, Category menu, ...). It names the navigation landmark around this menu, which the
        // Nav component renders once mounted and secondarymoremenu.mustache's NonJS fallback
        // renders before that. The page already exposes a "Site navigation" landmark for the
        // navbar, and two navigation landmarks cannot be told apart when navigating by landmark
        // unless each is named. Menus built from a plain object (the primary navigation) have no
        // title and are already inside the navbar's landmark, so they get no landmark at all.
        $navlabel = !empty($this->content->headertitle) ? $this->content->headertitle : null;
        if ($navlabel !== null) {
            $data['navlabel'] = $navlabel;
        }

        if ($this->haschildren) {
            // The node collection doesn't have anything to render so exit now.
            if (!isset($this->content->children) || count($this->content->children) == 0) {
                return [];
            }
            $data['reactprops'] = $this->export_react_props($this->content->children, $navlabel);

            // Also export the legacy node-collection structure. This is rendered as static
            // server-side fallback markup inside the React mount point (see
            // secondarymoremenu.mustache): React replaces it once it mounts, but without
            // JavaScript (NonJS Behat, no-JS browsers) this is the only markup that ever
            // reaches the page, so the menu must remain fully functional on its own.
            foreach ($this->content->children as &$item) {
                if ($item->showchildreninsubmenu && isset($this->content->children) &&
                        count($this->content->children) > 0) {
                    $item->moremenuid = uniqid();
                    $item->haschildren = true;
                }
            }
            $data['nodecollection'] = $this->content;
        } else {
            $data['nodearray'] = (array) $this->content;
            // If there is no node array to render then return an empty array.
            if (empty($data['nodearray'])) {
                return [];
            }
        }
        $data['moremenuid'] = uniqid();

        return $data;
    }

    /**
     * Build the JSON props consumed by the core/nav/Nav component.
     *
     * @param iterable $nodes The top-level navigation_node children to flatten.
     * @param string|null $navlabel Accessible name for the navigation landmark the component
     *                              renders around the menu. Null renders no landmark.
     * @return string JSON-encoded {items, morelabel, istablist, navlabel} props.
     */
    protected function export_react_props(iterable $nodes, ?string $navlabel = null): string {
        $props = [
            'items' => array_map(
                fn(navigation_node $node): array => $this->export_node_for_react($node),
                iterator_to_array($nodes, false),
            ),
            'morelabel' => get_string('moremenu', 'core'),
            'istablist' => $this->istablist,
        ];
        if ($navlabel !== null) {
            $props['navlabel'] = $navlabel;
        }

        return json_encode($props, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Recursively flatten a navigation node into a JSON-safe array for the Nav component.
     *
     * @param navigation_node $node The node to flatten.
     * @return array JSON-safe representation of the node and its children.
     */
    protected function export_node_for_react(navigation_node $node): array {
        return [
            'key' => (string) $node->key,
            'text' => (string) $node->text,
            'href' => $this->get_node_href($node),
            'active' => (bool) $node->isactive,
            'forceintomoremenu' => (bool) $node->forceintomoremenu,
            'showchildreninsubmenu' => (bool) $node->showchildreninsubmenu,
            'id' => $node->is_action_link() ? $node->action->attributes['id'] : null,
            'attributes' => $node->actionattributes(),
            'actions' => $node->action_link_actions()['actions'] ?? [],
            'children' => $node->has_children()
                ? array_map(
                    fn(navigation_node $child): array => $this->export_node_for_react($child),
                    iterator_to_array($node->children, false),
                )
                : [],
        ];
    }

    /**
     * Resolve the href a node should navigate to when rendered by the Nav component.
     *
     * When rendered as a tablist (see $istablist), nodes are switched between via an in-page anchor
     * stored in $node->tab (e.g. "#linkusers" on admin/search.php) rather than a real navigable action
     * URL, matching the legacy moremenu_children.mustache template's behaviour. Otherwise the node's
     * real action URL is used.
     *
     * @param navigation_node $node The node to resolve the href for.
     * @return string|null The href, or null if the node has neither an action nor a tab anchor.
     */
    protected function get_node_href(navigation_node $node): ?string {
        $actionurl = $node->action();
        $actionhref = $actionurl ? $actionurl->out(false) : null;

        if ($this->istablist) {
            return $node->tab ?: $actionhref;
        }

        return $actionhref ?: ($node->tab ?: null);
    }

}
