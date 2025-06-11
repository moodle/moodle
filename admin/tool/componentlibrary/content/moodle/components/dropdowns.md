---
layout: docs
title: "Dropdowns"
description: "A reusable dropdown component"
date: 2023-06-13T10:10:00+08:00
draft: false
tags:
- MDL-78279
- "4.3"
---

## How it works

Moodle dropdowns are output components to generate elements that expand extra floating information when clicked.

Currently, the core comes with two prebuild dropdowns:

- Dropdown dialog: to display rich content inside the dropdown area.
- Dropdown status: to display a list of available statuses

## Source files

- `lib/classes/output/local/dialog.php`: to define a dropdown dialog.
- `lib/classes/output/local/dialog.php`: to define a dropdown dialog.
- `lib/classes/output/choicelist.php`: generic output class to define a user choice.
- `lib/templates/local/dropdown/dialog.mustache`
- `lib/templates/local/dropdown/status.mustache`

## Usage

### Dropdown dialog

The constructor for the dropdown dialog class only requires three parameters.

- The button content
- The dropdown content
- An array of additional definitions. However, the output public methods can override all the definition values once the instance is created. Check out the examples to learn how to use them

The following example is the most simple example of creating a dropdown:

{{< php >}}
$dialog = new core\output\local\dropdown\dialog('Open dialog button', 'Dialog content');
echo $OUTPUT->render($dialog);
{{< / php >}}

You have the option to include additional classes to the main component but also to the button itself.

{{< php >}}
$dialog = new core\output\local\dropdown\dialog(
    'Open dialog',
    'Dialog content',
    [
        'classes' => 'mb-4',
        'buttonclasses' => 'btn btn-primary extraclass',
    ]
);
echo $OUTPUT->render($dialog);
{{< / php >}}

{{< mustache template="core/local/dropdown/dialog" >}}
    {
        "buttonid" : "example01",
        "buttoncontent" : "Open dialog",
        "dialogcontent" : "Dialog content",
        "buttonclasses": "btn btn-primary extraclass"
    }
{{< /mustache >}}

If a specific item is floating towards the end of the page, you might consider aligning the dropdown menu to the left rather than to the right. To achieve this, you can use the POSITION constant values to set the `dropdownposition` $definition attribute or input it into the `set_position` method.

{{< php >}}
$dialog = new core\output\local\dropdown\dialog('Open dialog', 'Dialog content');
$dialog->set_position(core\output\local\dropdown\dialog::POSITION['end']);
echo $OUTPUT->render($dialog);
{{< / php >}}

By default, the dropdown width will adapt to the content. However, for long texts, there may be better scenarios. You can use the WIDTH constant values to set the `dialogwidth` $definition attribute or input it into the `set_dialog_width` method.

{{< php >}}
// Big but fixed-width example.
$dialog = new core\output\local\dropdown\dialog('Big dialog', $content);
$dialog->set_dialog_width(core\output\local\dropdown\dialog::WIDTH['big']);
echo $OUTPUT->render($dialog);

// Small width example.
$dialog = new core\output\local\dropdown\dialog('Small dialog', $content);
$dialog->set_dialog_width(core\output\local\dropdown\dialog::WIDTH['small']);
echo $OUTPUT->render($dialog);
{{< / php >}}

{{< mustache template="core/local/dropdown/dialog" >}}
    {
        "buttonid" : "example02",
        "buttoncontent" : "Big dialog",
        "dialogcontent" : "This is a long content for a big dialog that will be displayed in a fixed-width container.",
        "buttonclasses": "btn btn-primary extraclass",
        "dialogclasses": "dialog-big"
    }
{{< /mustache >}}

### Dropdown status

The dropdown status is a user-choice wrapper. To create it, first, you need to create an instance of `core\output\choicelist` that will be used to generate the dropdown content data.

{{< php >}}
$choice = new core\output\choicelist('Dialog content');
$choice->add_option('option1', 'Option 1');
$choice->add_option('option2', 'Option 2');
$choice->add_option('option3', 'Option 3');
$choice->set_selected_value('option2');

$dialog = new core\output\local\dropdown\status('Open dialog button', $choice);
echo $OUTPUT->render($dialog);
{{< / php >}}

{{< mustache template="core/local/dropdown/status" >}}
    {
        "buttonid" : "example04",
        "buttoncontent" : "Open dialog button",
        "dialogcontent" : "Dialog content",
        "choices" : {
            "hasoptions" : true,
            "options" : [
                {
                    "optionid" : "option1",
                    "value" : "option1",
                    "name" : "Option 1",
                    "hasicon" : false,
                    "first" : true,
                    "optionnumber" : 1,
                    "optionuniqid" : "option1uniqid"
                },
                {
                    "optionid" : "option2",
                    "value" : "option2",
                    "name" : "Option 2",
                    "hasicon" : false,
                    "selected" : true,
                    "optionnumber" : 2,
                    "optionuniqid" : "option2uniqid"
                },
                {
                    "optionid" : "option3",
                    "value" : "option3",
                    "name" : "Option 3",
                    "hasicon" : false,
                    "optionnumber" : 3,
                    "optionuniqid" : "option3uniqid"
                }
            ]
        }
    }
{{< /mustache >}}

The status dropdown is an extension of the dropdown dialog, which means that all the definitions mentioned earlier can also be applied to it.

- The status dropdown is also compatible with all the `core\output\choicelist` extra features like:
- Adding additional icons and descriptions to the options
- Disable options
- Add links to options

The following example shows how to use the advanced features:

{{< php >}}
$choice = new core\output\choicelist('Dialog content');

// Option one is a link.
$choice->add_option('option1', 'Option 1', [
    'url' => new moodle_url('/'),
]);
// Option two has an icon and description.
$choice->add_option('option2', 'Option 2', [
    'description' => 'Option 2 description',
    'icon' => new pix_icon('t/hide', 'Eye icon 2')
]);
// Option three is disabled.
$choice->add_option('option3', 'Option 3', [
    'disabled' => true,
]);

$choice->set_selected_value('option2');

$dialog = new core\output\local\dropdown\status('Open dialog button', $choice);
echo $OUTPUT->render($dialog);
{{< / php >}}

#### Sync button text with selected status

The status dropdown can be configured to sync the button text with the selected status.

To do so, you need to set the `buttonsync` $definition attribute to `true`.

{{< php >}}
$choice = new core\output\choicelist();
$choice->add_option('option1', get_string('option1', YOURPLUGIN));
$choice->add_option('option2', get_string('option2', YOURPLUGIN));
$choice->set_selected_value('option2');

// Add some attribute to select through a query selector.
$dialog = new core\output\local\dropdown\status(
    get_string('buttontext', YOURPLUGIN),
    $choice,
    [
        'extras' => ['id' => 'mydropdown'],
        'buttonsync' => true,
        // With 'updatestatus' it will change the status when the user clicks an option
        // See "Dropdown status in update mode" section for more information.
        'updatestatus' => true,
    ]
);
echo $OUTPUT->render($dialog);
{{< / php >}}

## Javascript

### Controlling dropdowns

Both `core/local/dropdown/status` and `core/local/dropdown/status` AMD modules provide functions to:

- Open and close the dropdown.
- Change the button content.
- Get the main dropdown HTML element.

Both modules are object-oriented. To get the dropdown instance, the process is as follows:

1. Add id or data attributes to the main component to select it using a query selector.
2. Import `getDropdownDialog` from `core/local/dropdown/dialog`, or `getDropdownStatus` from `core/local/dropdown/status`, depending on whether you use a dialogue or a status dropdown.
3. Call `getDropdownDialog` or `getDropdownStatus` with the query selector to get the instance

Both classes provide the following methods:

- `setVisible(Boolean)` to open or close the dropdown.
- `isVisible()` to know if it is open or closed.
- `setButtonContent(String)` to replace the button content.
- `setButtonDisabled(Boolean)` to disable or enable the dropdown button.
- `getElement()`to get the main HTMLElement to add eventListeners.

The following example uses the module to open the dropdown when an extra button is preset:

```js
import {getDropdownDialog} from 'core/local/dropdown/';

const dialog = getDropdownDialog('[MYDROPDOWNSELECTOR]');
document.querySelector('[data-for="openDropdown"]').addEventListener('click', (event) => {
    event.stopPropagation();
    dialog.setVisible(true);
});
```

### Specific dropdown status methods

The `core/local/dropdown/status` provides extra controls for the status selector, such as:

- `getSelectedValue()` and `setSelectedValue(String)` to control the currently selected status.
- `isButtonSyncEnabled()` and `setButtonSyncEnabled(Boolean)` to synchronise the button text with the selected status.
- `isUpdateStatusEnabled()` and `setUpdateStatusEnabled(Boolean)` to control the auto-update status mode.

## Using dropdown status from the frontend

The dropdown status can operate in two different ways.

### Dropdown status in display only

The display-only is the default behaviour for any dropdown. In display-only mode, the component will show all the status values to the user, but it won't handle and click the event nor change the current status.

If a plugin wants to change the status value when the user clicks, it should  code a custom module to:

1. Capture `click` event listeners to the choice items.
2. Send the new status to the backend (using an ad-hoc webservice).
3. If the webservice execution is ok, update the component value using the `setSelectedValue` instance method.

The following example shows how to render a display-only dropdown status in the backend:

{{< php >}}
$choice = new core\output\choicelist('Dialog content');

// Add some data attributes to the choices.
$choice->add_option(
    'option1',
    get_string('option1', YOURPLUGIN), [
    extras' => ['data-action' => 'updateActionName']
]);
$choice->add_option(
    'option2',
    get_string('option2', YOURPLUGIN), [
    extras' => ['data-action' => 'updateActionName']
]);
$choice->set_selected_value('option2');

// Add some attribute to select through a query selector.
$dialog = new core\output\local\dropdown\status(
    get_string('buttontext', YOURPLUGIN),
    $choice,
    ['extras' => ['id' => 'mydropdown']]
);
echo $OUTPUT->render($dialog);
{{< / php >}}

Having this PHP code, the AMD controller could be something like:

```js
import {getDropdownStatus} from 'core/local/dropdown/status';
import {sendValueToTheBackend} from 'YOURPLUGIN/example';

const status = getDropdownStatus('#mydropdown');
status.getElement().addEventListener('click', (event) => {
    const option = event.target.closest("[data-action='updateActionName']");
    if (!option) {
        return;
    }
    try {
        if(sendValueToTheBackend(option.dataset.value)) {
             status.setSelectedValue(option.dataset.value);
        }
    } catch (error) {
        // Do some error handling here.
    }
});
```

### Dropdown status in update mode

The component will act more like an HTML radio button in update mode. It will store the current status value and will trigger `change` events when the value changes.

In this case, the plugin controller has to:

1. Capture the component element `change` event. Remember that, as in radio events, the `change` event won't bubble, so it cannot be delegated to a parent element.
2. Send the new status to the backend (using an ad-hoc webservice).
3. If the webservice execution fails, do a value rollback using the `setSelectedValue` instance method.

The following example shows how to render an update mode dropdown status in the backend:

{{< php >}}
$choice = new core\output\choicelist('Dialog content');

$choice->add_option('option1', get_string('option1', YOURPLUGIN));
$choice->add_option('option2', get_string('option2', YOURPLUGIN));
$choice->set_selected_value('option2');

// Add some attribute to select through a query selector.
$dialog = new core\output\local\dropdown\status(
    get_string('buttontext', YOURPLUGIN),
    $choice,
    [
        'extras' => ['id' => 'mydropdown'],
        'updatestatus' => true,
    ]
);
echo $OUTPUT->render($dialog);
{{< / php >}}

Having this PHP code, the AMD controller could be something like:

```js
import {getDropdownStatus} from 'core/local/dropdown/status';
import {sendValueToTheBackend} from 'YOURPLUGIN/example';

const status = getDropdownStatus('#mydropdown');
let currentValue = status.getSelectedValue();

status.getElement().addEventListener('change', (event) => {
    if (currentValue == status.getSelectedValue()) {
        return;
    }
    try {
        sendValueToTheBackend(status.getSelectedValue());
        currentValue = status.getSelectedValue();
    } catch (error) {
        status.setSelectedValue(currentValue);
    }
});
```

**Note**: the `event.target` is also the main element. You can also get the current value from `event.target.dataset.value` if you prefer.

## Examples

<!-- markdownlint-disable-next-line MD033 -->
<iframe src="../../../../examples/dropdowns.php" style="overflow:hidden;height:400px;width:100%;border:0" title="Moodle dynamic tabs"></iframe>
