---
layout: docs
title: "Action menus"
description: "A reusable action menu component"
date: 2023-07-27T10:10:00+08:00
draft: false
tags:
- MDL-78665
- "4.3"
---

## How it works

Moodle action menus are a reusable component that can display a list of actions in a dropdown menu. They are used in many places in Moodle, including the user menu, the course administration menu, and the activity administration menu.

## Source files

- `lib/outputcomponents.php`: contains the main `action_menu`, `action_menu_link` and `pix_icon`.
- `lib/classes/output/local/action_menu/subpanel.php`: contains the `subpanel` menu item class.
- `lib/templates/action_menu.mustache`: contains the main template for the action menu.
- `lib/templates/action_menu_*`: location for the legacy auxliar mustache files.
- `lib/templates/local/action_menu/*`: location for any new auxiliar mustache files.

## Examples

<!-- markdownlint-disable-next-line MD033 -->
<iframe src="../../../../examples/actionmenu.php" style="overflow:hidden;height:400px;width:100%;border:0" title="Moodle action menus"></iframe>

## Usage

### Rendering an action menu

The component output classes can render an action menu entirely in PHP. The steps to do it are:

1. Create an action menu instance (with or without items)
2. (optional) Setup the action menu trigger
3. (optional) Add items to the menu (if they are not added on creation)
4. Render the menu.

The following code is a basic example of an action menu:

{{< php >}}
/** @var core_renderer $output*/
$output = $PAGE->get_renderer('core');

$menu = new action_menu();

// Add items.
$menu->add(new action_menu_link(
    new moodle_url($PAGE->url, ['foo' => 'bar']),
    new pix_icon('t/emptystar', ''),
    'Action link example',
    false
));

echo $output->render($menu);
{{< / php >}}

And this is the same example but passing the items in the creation:

{{< php >}}
/** @var core_renderer $output*/
$output = $PAGE->get_renderer('core');

$menu = new action_menu([
    new action_menu_link(
        new moodle_url($PAGE->url, ['foo' => 'bar']),
        new pix_icon('t/emptystar', ''),
        'Action link example',
        false
    ),
]);

echo $output->render($menu);
{{< / php >}}

### Setup the menu trigger

By default, the action menu trigger is a cog icon. However, the class has methods to convert it to a kebab menu or even display any arbitrary content.

Example of a kebab menu:

{{< php >}}
/** @var core_renderer $output*/
$output = $PAGE->get_renderer('core');

$menu = new action_menu();
$menu->set_kebab_trigger(get_string('edit'), $output);
$menu->set_additional_classes('fields-actions');
{{< / php >}}

Example of a custom trigger:

{{< php >}}
/** @var core_renderer $output*/
$output = $PAGE->get_renderer('core');

$menu = new action_menu();
$menu->set_menu_trigger(get_string('edit'));
{{< / php >}}

### Add items

Items can be added as an array on creation or using the `add` method. Depending on the param passed to `add` the item can be displayed in two different locations:

Primary items: are displayed next to the trigger button as direct actions.
Secondary items: are displayed inside the action menu dropdown.

The item location must be configured before adding the element. The following example shows different ways to add primary and secondary menu items.

{{< php >}}
// Primary items examples.
$menu->add(new action_menu_link(
    new moodle_url($PAGE->url),
    new pix_icon('t/emptystar', ''),
    'Action link example',
    true
));
$menu->add(new action_menu_link_primary(
    $PAGE->url,
    new pix_icon('t/emptystar', ''),
    'Action link example',
));

// Secondary items examples.
$menu->add(new action_menu_link(
    new moodle_url($PAGE->url),
    new pix_icon('t/emptystar', ''),
    'Action link example',
    false
));
$menu->add(new action_menu_link_secondary(
    $PAGE->url,
    new pix_icon('t/user', ''),
    'Action link example',
));
{{< / php >}}

## Types of items

The `add` method accepts several item types.

### `action_menu_link`

The `action_menu_link` class is the generic class for link items. It has several construct params:

- `moodle_url $url`: the link URL.
- `pix_icon $icon`: an optional pix_icon. If none passed, the item will show the trigger icon or none if it is a kebab menu.
- `string $text`: the text to display.
- `bool $primary`: if the item is primary or secondary. By default, all items are primary.
- `array $attributes`: an optional array of HTML attributes.

Two convenience classes extend `action_menu_link`:

- `action_menu_link_primary`: will be added as a primary item.
- `action_menu_link_secondary`: will be added as a secondary item.

### `pix_icon`

The action menu can render `pix_icon` as primary actions. The `pix_icon` is a standard output class for generating icons in Moodle.

Construct params:

- `String $pix`: the internal icon location. For example, "t/user".
- `String $alt`: an optional alternative text
- `String $component`: the pix icon component. By default, only core icons from the `pix` folder will be used
- `Array $attributes`: optional HTML attributes.

### `core\output\local\action_menu\subpanel`

The `core\output\local\action_menu` allow the `action_menu` to add items that display subpanels when hovered or clicked.

Construct params:

- `string $text`: text to display in the menu item
- `renderable $subpanel`: the output to render inside the subpanel. This param should be renderable using the standard `output::render` method.
- `array $attributes` optional HTML attributes

The following example creates a subpanel using a renderable choicelist instance:

{{< php >}}
/** @var core_renderer $output*/
$output = $PAGE->get_renderer('core');

// A choice list is a renderable class to outpout a user choice.
$choice = new core\output\choicelist('Choice example');
$choice->add_option("statusa", "Status A", [
    'url' => $PAGE->url,
    'description' => 'Status A description',
    'icon' => new pix_icon('t/user', '', ''),
]);
$choice->add_option("statusb", "Status B", [
    'url' => $PAGE->url,
    'description' => 'Status B description',
    'icon' => new pix_icon('t/groupv', '', ''),
]);
$choice->set_selected_value('statusb');

$menu = new action_menu();

// Add subpanel item.
$menu->add(new core\output\local\action_menu\subpanel(
    'Subpanel example',
    $choice
));

echo $output->render($menu);
{{< / php >}}

### HTML string

If a plain string is added to an action_menu, the action_menu::add method will be printed as it is inside the dropdown menu (as a secondary item).
