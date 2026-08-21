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

use core\url;
use renderable;
use renderer_base;
use templatable;
use custom_menu;
use filter_manager;

/**
 * Primary navigation renderable
 *
 * This file combines primary nav, custom menu, lang menu and
 * usermenu into a standardized format for the frontend
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class primary implements renderable, templatable {
    /** @var \moodle_page $page the moodle page that the navigation belongs to */
    private $page = null;

    /**
     * primary constructor.
     * @param \moodle_page $page
     */
    public function __construct($page) {
        $this->page = $page;
    }

    /**
     * Combine the various menus into a standardized output.
     *
     * @param renderer_base|null $output
     * @return array
     */
    public function export_for_template(?renderer_base $output = null): array {
        if (!$output) {
            $output = $this->page->get_renderer('core');
        }

        $primarynav = $this->merge_primary_and_custom($this->get_primary_nav(), $this->get_custom_menu($output));
        $menudata = (object) $primarynav;
        $moremenu = new \core\navigation\output\more_menu($menudata, 'navbar-nav', false);
        $moremenudata = $moremenu->export_for_template($output);
        if (!empty($moremenudata)) {
            $moremenudata['reactprops'] = $this->export_react_props($primarynav, $moremenudata);
        }
        $mobileprimarynav = $this->merge_primary_and_custom($this->get_primary_nav(), $this->get_custom_menu($output), true);

        $languagemenu = new \core\output\language_menu($this->page);

        return [
            'mobileprimarynav' => $mobileprimarynav,
            'moremenu' => $moremenudata,
            'lang' => !isloggedin() || isguestuser() ? $languagemenu->export_for_template($output) : [],
            'user' => $this->get_user_menu($output),
        ];
    }

    /**
     * Build the JSON props consumed by the core/nav/PrimaryNav component.
     *
     * @param array $nodes Standardised primary/custom nav items.
     * @param array $moremenudata The more_menu export these props render alongside.
     * @return string JSON-encoded props.
     */
    protected function export_react_props(array $nodes, array $moremenudata): string {
        return json_encode([
            'items' => $this->export_nodes_for_react($nodes, false),
            'morelabel' => get_string('moremenu', 'core'),
            // Taken from the more_menu export rather than repeated as literals, so the React
            // markup and the NonJS fallback markup can never disagree about either.
            'navbarstyle' => $moremenudata['navbarstyle'],
            'istablist' => $moremenudata['istablist'],
            'measuredclass' => 'primarynav-measured',
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * Flatten a list of standardised menu items into JSON-safe structures for React.
     *
     * Dividers are only meaningful inside a dropdown: legacy moremenu_children.mustache rendered a
     * child divider, but a top level one fell through to its ordinary <li> branch and became an
     * empty nav item. They are dropped here rather than reproduced.
     *
     * @param iterable $nodes The nodes to flatten.
     * @param bool $keepdividers Whether divider records should be preserved.
     * @return array
     */
    protected function export_nodes_for_react(iterable $nodes, bool $keepdividers): array {
        $items = [];
        $index = 0;
        foreach ($nodes as $node) {
            $item = (array) $node;
            if (!empty($item['divider']) && !$keepdividers) {
                continue;
            }
            $items[] = $this->export_node_for_react($item, $index);
            $index++;
        }

        return $items;
    }

    /**
     * Flatten a standardised menu item into a JSON-safe structure for React.
     *
     * @param array $node The node to flatten.
     * @param int $index The node's position among its siblings.
     * @return array
     */
    protected function export_node_for_react(array $node, int $index): array {
        $item = [
            'key' => $this->resolve_node_key($node, $index),
            'text' => '',
            'title' => null,
            'href' => null,
            'active' => false,
            'forceintomoremenu' => false,
            'showchildreninsubmenu' => false,
            // Primary nav nodes are never action links, so there is nothing to fill these with.
            // They are still emitted so the payload matches NavNode's shape exactly.
            'id' => null,
            'attributes' => [],
            'actions' => [],
            'divider' => !empty($node['divider']),
            'children' => [],
        ];

        if ($item['divider']) {
            return $item;
        }

        $text = $this->decode_node_text($node['text'] ?? '');
        $title = $this->decode_node_text($node['title'] ?? '');

        $item['text'] = $text;
        // Only export a title that differs from the text, as the legacy pair of
        // custom_menu_item::export_for_template() and moremenu_children.mustache did, so a title
        // never just repeats the visible label.
        $item['title'] = ($title !== '' && $title !== $text) ? $title : null;
        $item['href'] = $this->resolve_node_href($node);
        $item['active'] = !empty($node['isactive']);
        $item['forceintomoremenu'] = !empty($node['forceintomoremenu']);
        $item['showchildreninsubmenu'] = !empty($node['showchildreninsubmenu']) || !empty($node['haschildren']);
        $item['children'] = $this->export_nodes_for_react($node['children'] ?? [], true);

        return $item;
    }

    /**
     * Decode an already escaped value for handing to React.
     *
     * Legacy moremenu_children.mustache printed these raw ({{{text}}}, href="{{{url}}}"), so the
     * HTML parser decoded them into the DOM and custom_menu_item::export_for_template() escapes
     * accordingly. React decodes neither its text nodes nor an href set with setAttribute(), so
     * "Terms & Conditions" would show as "Terms &amp; Conditions" and "?id=2&mode=all" would be
     * requested with a second parameter of "amp;mode".
     *
     * @param mixed $value The escaped label, title or url.
     * @return string
     */
    protected function decode_node_text($value): string {
        return html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Resolve a stable key for React list rendering.
     *
     * Keys only have to be unique among siblings, so the node's position is a safe last resort:
     * unlike a hash of the label it survives the label changing, and so never remounts the item.
     *
     * @param array $node The menu node.
     * @param int $index The node's position among its siblings.
     * @return string
     */
    protected function resolve_node_key(array $node, int $index): string {
        // The get_primary_nav() function sets both key and sort; custom_menu_item::export_for_template()
        // sets only sort. Test with isset() rather than !empty() because a sort of 0 is still a key.
        foreach (['key', 'sort'] as $candidate) {
            if (isset($node[$candidate]) && $node[$candidate] !== '') {
                return (string) $node[$candidate];
            }
        }

        return 'node-' . $index;
    }

    /**
     * Resolve the href used by the React component.
     *
     * @param array $node The menu node.
     * @return string|null
     */
    protected function resolve_node_href(array $node): ?string {
        // The get_primary_nav() function exports navigation_node::action(), i.e. a url object or null, while
        // custom_menu_item::export_for_template() exports an already stringified url.
        $url = $node['url'] ?? null;

        if ($url instanceof url) {
            return $url->out(false);
        }

        if (!empty($url)) {
            // That string comes from url::out(), i.e. HTML escaped ready for the legacy template's
            // raw href="{{{url}}}", where the HTML parser decoded it again. React does not.
            return $this->decode_node_text($url);
        }

        return null;
    }

    /**
     * Get the primary nav object and standardize the output
     *
     * @param \navigation_node|null $parent used for nested nodes, by default the primarynav node
     * @return array
     */
    protected function get_primary_nav($parent = null): array {
        if ($parent === null) {
            $parent = $this->page->primarynav;
        }
        $nodes = [];
        foreach ($parent->children as $node) {
            $children = $this->get_primary_nav($node);
            $activechildren = array_filter($children, function($child) {
                return !empty($child['isactive']);
            });
            if ($node->preceedwithhr && count($nodes) && empty($nodes[count($nodes) - 1]['divider'])) {
                $nodes[] = ['divider' => true];
            }
            $nodes[] = [
                'title' => $node->get_title(),
                'url' => $node->action(),
                'text' => $node->text,
                'icon' => $node->icon,
                'isactive' => $node->isactive || !empty($activechildren),
                'key' => $node->key,
                'sort' => $node->key,
                'children' => $children,
                'haschildren' => !empty($children) ? 1 : 0,
            ];
        }

        return $nodes;
    }

    /**
     * Custom menu items reside on the same level as the original nodes.
     * Fetch and convert the nodes to a standardised array.
     *
     * @param renderer_base $output
     * @return array
     */
    protected function get_custom_menu(renderer_base $output): array {
        global $CFG;

        // Early return if a custom menu does not exists.
        if (empty($CFG->custommenuitems)) {
            return [];
        }

        $custommenuitems = $CFG->custommenuitems;

        // If filtering of the primary custom menu is enabled, apply only the string filters.
        if (!empty($CFG->navfilter && !empty($CFG->stringfilters))) {
            // Apply filters that are enabled for Content and Headings.
            $filtermanager = \filter_manager::instance();
            $custommenuitems = $filtermanager->filter_string($custommenuitems, \context_system::instance());
        }

        $currentlang = current_language();
        $custommenunodes = custom_menu::convert_text_to_menu_nodes($custommenuitems, $currentlang);
        $nodes = [];
        foreach ($custommenunodes as $node) {
            $nodes[] = $node->export_for_template($output);
        }

        return $nodes;
    }

    /**
     * When defining custom menu items, the active flag is not obvserved correctly. Therefore, the merge of the primary
     * and custom navigation must be handled a bit smarter. Change the "isactive" flag of the nodes (this may set by
     * default in the primary nav nodes but is entirely missing in the custom nav nodes).
     * Set the $expandedmenu argument to true when the menu for the mobile template is build.
     *
     * @param array $primary
     * @param array $custom
     * @param bool $expandedmenu
     * @return array
     */
    protected function merge_primary_and_custom(array $primary, array $custom, bool $expandedmenu = false): array {
        if (empty($custom)) {
            return $primary; // No custom nav, nothing to merge.
        }
        // Remember the amount of primary nodes and whether we changed the active flag in the custom menu nodes.
        $primarylen = count($primary);
        $changed = false;
        foreach (array_keys($custom) as $i) {
            if (!$changed) {
                if ($this->flag_active_nodes($custom[$i], $expandedmenu)) {
                    $changed = true;
                }
            }
            $primary[] = $custom[$i];
        }
        // In case some custom node is active, mark all primary nav elements as inactive.
        if ($changed) {
            for ($i = 0; $i < $primarylen; $i++) {
                $primary[$i]['isactive'] = false;
            }
        }
        return $primary;
    }

    /**
     * Recursive checks if any of the children is active. If that's the case this node (the parent) is active as
     * well. If the node has no children, check if the node itself is active. Use pass by reference for the node
     * object because we actively change/set the "isactive" flag inside the method and this needs to be kept at the
     * callers side.
     * Set $expandedmenu to true, if the mobile menu is done, in this case the active flag gets the node that is
     * actually active, while the parent hierarchy of the active node gets the flag isopen.
     *
     * @param object $node
     * @param bool $expandedmenu
     * @return bool
     */
    protected function flag_active_nodes(object $node, bool $expandedmenu = false): bool {
        global $FULLME;
        $active = false;
        foreach (array_keys($node->children ?? []) as $c) {
            if ($this->flag_active_nodes($node->children[$c], $expandedmenu)) {
                $active = true;
            }
        }
        // One of the children is active, so this node (the parent) is active as well.
        if ($active) {
            if ($expandedmenu) {
                $node->isopen = true;
            } else {
                $node->isactive = true;
            }
            return true;
        }

        // By default, the menu item node to check is not active.
        $node->isactive = false;

        // Check if the node url matches the called url. The node url may omit the trailing index.php, therefore check
        // this as well.
        if (empty($node->url)) {
            // Current menu node has no url set, so it can't be active.
            return false;
        }
        $nodeurl = parse_url($node->url);
        $current = parse_url($FULLME ?? '');

        $pathmatches = false;

        // Check for same host names before comparing the path.
        $currenthost = array_key_exists('host', $current) ? strtolower($current['host']) : '';
        $nodehost = array_key_exists('host', $nodeurl) ? strtolower($nodeurl['host']) : '';
        if ($currenthost !== $nodehost) {
            return false;
        }
        // Exact match of the path of node and current url.
        $nodepath = $nodeurl['path'] ?? '/';
        $currentpath = $current['path'] ?? '/';
        if ($nodepath === $currentpath) {
            $pathmatches = true;
        }
        // The current url may be trailed by a index.php, otherwise it's the same as the node path.
        if (!$pathmatches && $nodepath . 'index.php' === $currentpath) {
            $pathmatches = true;
        }
        // No path did match, so the node can't be active.
        if (!$pathmatches) {
            return false;
        }
        // We are here because the path matches, so now look at the query string.
        $nodequery = $nodeurl['query'] ?? '';
        $currentquery = $current['query'] ?? '';
        // If the node has no query string defined, then the patch match is sufficient.
        if (empty($nodeurl['query'])) {
            $node->isactive = true;
            return true;
        }
        // If the node contains a query string then also the current url must match this query.
        if ($nodequery === $currentquery) {
            $node->isactive = true;
        }
        return $node->isactive;
    }

    /**
     * Get/Generate the user menu.
     *
     * This is leveraging the data from \core\user::get_user_navigation_info() and the logic in $OUTPUT->user_menu()
     *
     * @param renderer_base $output
     * @return array The template context array, including:
     *               - userfirstname string The first name of the logged-in user (or real user when impersonating).
     *               - userfullname  string The full name of the logged-in user (or real user when impersonating).
     *               - avatardata    array  Avatar image(s) for display in the menu trigger.
     *               - metadata      array  Additional contextual metadata (role, login failures, etc.).
     *               - items         array  Navigation links shown in the dropdown.
     *               - submenus      array  Nested submenus (e.g. language selector).
     */
    public function get_user_menu(renderer_base $output): array {
        global $CFG, $USER, $PAGE;
        require_once($CFG->dirroot . '/user/lib.php');

        $usermenudata = [];
        $submenusdata = [];
        $info = \core\user::get_user_navigation_info($USER, $PAGE);
        if (isset($info->unauthenticateduser)) {
            $info->unauthenticateduser['content'] = get_string($info->unauthenticateduser['content']);
            $info->unauthenticateduser['url'] = get_login_url();
            return (array) $info;
        }
        // Gather all the avatar data to be displayed in the user menu.
        $usermenudata['avatardata'][] = [
            'content' => $info->metadata['useravatar'],
            'classes' => 'current'
        ];
        $usermenudata['userfullname'] = $info->metadata['realuserfullname'] ?? $info->metadata['userfullname'];
        $usermenudata['userfirstname'] = $info->metadata['realuserfirstname'] ?? $info->metadata['userfirstname'];

        // Logged in as someone else.
        if ($info->metadata['asotheruser']) {
            $usermenudata['avatardata'][] = [
                'content' => $info->metadata['realuseravatar'],
                'classes' => 'realuser'
            ];
            $usermenudata['metadata'][] = [
                'content' => get_string('loggedinas', 'moodle', $info->metadata['userfullname']),
                'classes' => 'viewingas'
            ];
        }

        // Gather all the meta data to be displayed in the user menu.
        $metadata = [
            'asotherrole' => [
                'value' => 'rolename',
                'class' => 'role role-##GENERATEDCLASS##',
            ],
            'userloginfail' => [
                'value' => 'userloginfail',
                'class' => 'loginfailures',
            ],
            'asmnetuser' => [
                'value' => 'mnetidprovidername',
                'class' => 'mnet mnet-##GENERATEDCLASS##',
            ],
        ];
        foreach ($metadata as $key => $value) {
            if (!empty($info->metadata[$key])) {
                $content = $info->metadata[$value['value']] ?? '';
                $generatedclass = strtolower(preg_replace('#[ ]+#', '-', trim($content)));
                $customclass = str_replace('##GENERATEDCLASS##', $generatedclass, ($value['class'] ?? ''));
                $usermenudata['metadata'][] = [
                    'content' => $content,
                    'classes' => $customclass
                ];
            }
        }

        // Include the language menu as a submenu within the user menu.
        $languagemenu = new \core\output\language_menu($this->page);
        $langmenu = $languagemenu->export_for_template($output);
        if (!empty($langmenu)) {
            $languageitems = $langmenu['items'];
            // If there are available languages, generate the data for the the language selector submenu.
            if (!empty($languageitems)) {
                $langsubmenuid = uniqid();
                // Generate the data for the link to language selector submenu.
                $language = (object) [
                    'itemtype' => 'submenu-link',
                    'submenuid' => $langsubmenuid,
                    'title' => get_string('language'),
                    'divider' => false,
                    'submenulink' => true,
                ];

                // Place the link before the 'Log out' menu item which is either the last item in the menu or
                // second to last when 'Switch roles' is available.
                $menuposition = count($info->navitems) - 1;
                if (has_capability('moodle/role:switchroles', $PAGE->context)) {
                    $menuposition = count($info->navitems) - 2;
                }
                array_splice($info->navitems, $menuposition, 0, [$language]);

                // Generate the data for the language selector submenu.
                $submenusdata[] = (object)[
                    'id' => $langsubmenuid,
                    'title' => get_string('languageselector'),
                    'items' => $languageitems,
                ];
            }
        }

        // Add divider before the last item.
        $info->navitems[count($info->navitems) - 2]->divider = true;
        $usermenudata['items'] = $info->navitems;
        $usermenudata['submenus'] = array_values($submenusdata);

        return $usermenudata;
    }
}
