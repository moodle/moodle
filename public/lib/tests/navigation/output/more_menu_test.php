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

use advanced_testcase;
use core\navigation\navigation_node;
use core\output\renderer_base;
use core\url;
use ReflectionMethod;
use stdClass;

/**
 * More menu navigation renderable test.
 *
 * @package     core
 * @category    navigation
 * @copyright   Stefan Topfstedt
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers      \core\navigation\output\more_menu
 */
final class more_menu_test extends advanced_testcase {
    /**
     * Checks that export_for_template() returns an empty array if the given content is empty.
     * See MDL-86416.
     *
     * @return void
     */
    public function test_export_for_template_returns_empty_array(): void {
        $moremenu = new more_menu(new stdClass(), 'whatever', false, false);
        $output = $this->createStub(renderer_base::class);
        $data = $moremenu->export_for_template($output);
        $this->assertIsArray($data);
        $this->assertEmpty($data);
    }

    /**
     * Build a navigation_node with autofindactive disabled, so tests don't depend on $PAGE/$FULLME state.
     *
     * @param string $text The node's display text.
     * @param url|null $action Optional action URL for the node.
     * @param string|null $key Optional node key.
     * @return navigation_node
     */
    private function create_node(string $text, ?url $action = null, ?string $key = null): navigation_node {
        $autofindactive = navigation_node::$autofindactive;
        navigation_node::$autofindactive = false;
        try {
            return navigation_node::create($text, $action, navigation_node::TYPE_CUSTOM, null, $key);
        } finally {
            navigation_node::$autofindactive = $autofindactive;
        }
    }

    /**
     * Checks that export_for_template() for the haschildren=true path exports both {@see reactprops}
     * JSON for the React Nav component, and the legacy 'nodecollection' structure, which is
     * rendered as static server-side fallback markup so the menu still works without JavaScript
     * (NonJS Behat, no-JS browsers).
     *
     * @return void
     */
    public function test_export_for_template_haschildren_exports_reactprops_and_nodecollection(): void {
        $root = $this->create_node('Root');
        $alpha = $this->create_node('Alpha', new url('/alpha.php'), 'alpha');
        $alpha->isactive = true;
        $root->add_node($alpha);
        $root->add_node($this->create_node('Beta', new url('/beta.php'), 'beta'));

        $content = new stdClass();
        $content->children = $root->children;

        $moremenu = new more_menu($content, 'navbarclass', true, false);
        $output = $this->createStub(renderer_base::class);
        $data = $moremenu->export_for_template($output);

        $this->assertArrayHasKey('navbarstyle', $data);
        $this->assertSame('navbarclass', $data['navbarstyle']);
        $this->assertArrayHasKey('istablist', $data);
        $this->assertFalse($data['istablist']);
        $this->assertArrayHasKey('reactprops', $data);
        $this->assertIsString($data['reactprops']);
        $this->assertArrayHasKey('moremenuid', $data);

        // The haschildren=true path does not export a nodearray (that's only for haschildren=false).
        $this->assertArrayNotHasKey('nodearray', $data);

        // This content has no headertitle, so no accessible name is exported for the landmark and
        // the Nav component renders a bare <ul> rather than a named <nav>.
        $this->assertArrayNotHasKey('navlabel', $data);
        $this->assertArrayNotHasKey('navlabel', json_decode($data['reactprops'], true));

        // The legacy nodecollection structure must be present: it's rendered as the static
        // server-side fallback inside the React mount point (see secondarymoremenu.mustache),
        // so the menu still works for NonJS Behat and no-JS browsers.
        $this->assertArrayHasKey('nodecollection', $data);
        $this->assertSame($content, $data['nodecollection']);
        $this->assertCount(2, $data['nodecollection']->children);

        $reactprops = json_decode($data['reactprops'], true);
        $this->assertIsArray($reactprops);
        $this->assertSame('More', $reactprops['morelabel']);
        $this->assertFalse($reactprops['istablist']);
        $this->assertCount(2, $reactprops['items']);
        $this->assertSame('alpha', $reactprops['items'][0]['key']);
        $this->assertSame('Alpha', $reactprops['items'][0]['text']);
        $this->assertTrue($reactprops['items'][0]['active']);
        $this->assertSame('beta', $reactprops['items'][1]['key']);
        $this->assertFalse($reactprops['items'][1]['active']);
    }

    /**
     * Checks that export_for_template() exports the content's headertitle as 'navlabel', both as a
     * template variable (for secondarymoremenu.mustache's NonJS fallback landmark) and inside the
     * React props (for the landmark the Nav component renders once mounted). Both paths must carry
     * the same name, or the landmark would be renamed on hydration.
     *
     * Without a name the secondary navigation cannot be told apart from the navbar's own
     * "Site navigation" landmark when navigating by landmark.
     *
     * @return void
     */
    public function test_export_for_template_exports_headertitle_as_navlabel(): void {
        $root = $this->create_node('Root');
        $root->add_node($this->create_node('Alpha', new url('/alpha.php'), 'alpha'));

        $content = new stdClass();
        $content->children = $root->children;
        $content->headertitle = 'Course menu';

        $moremenu = new more_menu($content, 'nav-tabs', true, true);
        $data = $moremenu->export_for_template($this->createStub(renderer_base::class));

        $this->assertArrayHasKey('navlabel', $data);
        $this->assertSame('Course menu', $data['navlabel']);

        $reactprops = json_decode($data['reactprops'], true);
        $this->assertArrayHasKey('navlabel', $reactprops);
        $this->assertSame('Course menu', $reactprops['navlabel']);
    }

    /**
     * Checks that export_react_props() encodes {items, morelabel, istablist} for the React
     * Nav component, propagating the istablist flag passed to the more_menu constructor.
     *
     * @return void
     */
    public function test_export_react_props_encodes_items_morelabel_and_istablist(): void {
        $root = $this->create_node('Root');
        $root->add_node($this->create_node('Alpha', new url('/alpha.php'), 'alpha'));

        $moremenu = new more_menu(new stdClass(), 'whatever', true, true);
        $method = new ReflectionMethod(more_menu::class, 'export_react_props');
        $json = $method->invoke($moremenu, $root->children);

        $this->assertIsString($json);
        $decoded = json_decode($json, true);
        $this->assertSame('More', $decoded['morelabel']);
        $this->assertTrue($decoded['istablist']);
        $this->assertCount(1, $decoded['items']);
        $this->assertSame('alpha', $decoded['items'][0]['key']);
    }

    /**
     * Checks that export_node_for_react() flattens a navigation_node's relevant properties,
     * including nested children when the node has any.
     *
     * @return void
     */
    public function test_export_node_for_react_flattens_node_properties(): void {
        $moremenu = new more_menu(new stdClass(), 'whatever', true, false);
        $method = new ReflectionMethod(more_menu::class, 'export_node_for_react');

        // A leaf node with no children, marked active and forced into the more menu.
        $leaf = $this->create_node('Leaf', new url('/leaf.php'), 'leaf');
        $leaf->isactive = true;
        $leaf->forceintomoremenu = true;

        $leafarray = $method->invoke($moremenu, $leaf);
        $this->assertSame([
            'key' => 'leaf',
            'text' => 'Leaf',
            'href' => (new url('/leaf.php'))->out(false),
            'active' => true,
            'forceintomoremenu' => true,
            'showchildreninsubmenu' => false,
            'id' => null,
            'attributes' => [],
            'actions' => [],
            'children' => [],
        ], $leafarray);

        // A branch node with showchildreninsubmenu set and one child: children must be flattened too.
        $branch = $this->create_node('Branch', null, 'branch');
        $branch->showchildreninsubmenu = true;
        $child = $this->create_node('Child', new url('/child.php'), 'child');
        $branch->add_node($child);

        $brancharray = $method->invoke($moremenu, $branch);
        $this->assertSame('branch', $brancharray['key']);
        $this->assertTrue($brancharray['showchildreninsubmenu']);
        $this->assertNull($brancharray['href']);
        $this->assertCount(1, $brancharray['children']);
        $this->assertSame('child', $brancharray['children'][0]['key']);
        $this->assertSame('Child', $brancharray['children'][0]['text']);
        $this->assertSame((new url('/child.php'))->out(false), $brancharray['children'][0]['href']);
    }

    /**
     * Checks that export_node_for_react() carries through an action_link's custom HTML attributes
     * and component_actions (e.g. popup_action), which the React Nav component needs to
     * reproduce behaviour like "Download course content"'s confirmation modal (data-downloadcourse
     * and friends) and "Print book"/"Print chapter"'s popup window. See MDL-87830.
     *
     * @return void
     */
    public function test_export_node_for_react_carries_action_link_attributes_and_actions(): void {
        $this->resetAfterTest();

        $moremenu = new more_menu(new stdClass(), 'whatever', true, false);
        $method = new ReflectionMethod(more_menu::class, 'export_node_for_react');

        // A plain node with no action_link: id is null, attributes/actions are empty.
        $plain = $this->create_node('Plain', new url('/plain.php'), 'plain');
        $plainarray = $method->invoke($moremenu, $plain);
        $this->assertNull($plainarray['id']);
        $this->assertSame([], $plainarray['attributes']);
        $this->assertSame([], $plainarray['actions']);

        // A node whose action is an action_link with custom attributes and a popup_action,
        // matching how "Download course content" and "Print book" attach their behaviour.
        $actionlink = new \action_link(
            new url('/download.php'),
            'Download course content',
            new \popup_action('click', new url('/download.php?download=1')),
            ['data-downloadcourse' => 1, 'data-download-title' => 'Download course content'],
        );
        $withaction = $this->create_node('WithAction', null, 'withaction');
        $withaction->action = $actionlink;

        $actionarray = $method->invoke($moremenu, $withaction);
        $this->assertSame($actionlink->attributes['id'], $actionarray['id']);
        $this->assertContains(['name' => 'data-downloadcourse', 'value' => 1], $actionarray['attributes']);
        $this->assertContains(
            ['name' => 'data-download-title', 'value' => 'Download course content'],
            $actionarray['attributes'],
        );
        $this->assertCount(1, $actionarray['actions']);
        $this->assertSame('click', $actionarray['actions'][0]->event);
        $this->assertSame('openpopup', $actionarray['actions'][0]->jsfunction);
        $this->assertSame($actionlink->attributes['id'], $actionarray['actions'][0]->id);
    }

    /**
     * Checks that get_node_href() resolves to the node's action URL when not a tablist, and falls
     * back to the node's tab anchor (or null) when there is no action, matching the legacy
     * moremenu_children.mustache behaviour.
     *
     * @return void
     */
    public function test_get_node_href_prefers_action_url_when_not_tablist(): void {
        $moremenu = new more_menu(new stdClass(), 'whatever', true, false);
        $method = new ReflectionMethod(more_menu::class, 'get_node_href');

        // Node with an action URL: the action URL wins.
        $withaction = $this->create_node('WithAction', new url('/action.php'));
        $withaction->tab = '#linkusers';
        $this->assertSame((new url('/action.php'))->out(false), $method->invoke($moremenu, $withaction));

        // Node with no action, but a tab anchor: falls back to the tab anchor.
        $tabonly = $this->create_node('TabOnly');
        $tabonly->tab = '#linkusers';
        $this->assertSame('#linkusers', $method->invoke($moremenu, $tabonly));

        // Node with neither an action nor a tab anchor: null.
        $neither = $this->create_node('Neither');
        $this->assertNull($method->invoke($moremenu, $neither));
    }

    /**
     * Checks that get_node_href() prefers the node's tab anchor over its action URL when the
     * more_menu is rendered as a tablist (in-page tab switching rather than page navigation).
     *
     * @return void
     */
    public function test_get_node_href_prefers_tab_anchor_when_tablist(): void {
        $moremenu = new more_menu(new stdClass(), 'whatever', true, true);
        $method = new ReflectionMethod(more_menu::class, 'get_node_href');

        // Node with both an action URL and a tab anchor: the tab anchor wins when istablist.
        $both = $this->create_node('Both', new url('/action.php'));
        $both->tab = '#linkusers';
        $this->assertSame('#linkusers', $method->invoke($moremenu, $both));

        // Node with only an action URL: falls back to the action URL.
        $actiononly = $this->create_node('ActionOnly', new url('/action.php'));
        $this->assertSame((new url('/action.php'))->out(false), $method->invoke($moremenu, $actiononly));

        // Node with neither: null.
        $neither = $this->create_node('Neither');
        $this->assertNull($method->invoke($moremenu, $neither));
    }
}
