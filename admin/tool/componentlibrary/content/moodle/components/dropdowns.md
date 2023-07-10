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

## Capturing events with Javascript

Unfortunately, the current implementation does not yet include an ADM module for rendering or controlling dropdowns. However, you can create ad-hoc modules by adding id and data attributes to the relevant elements.

Here is an example of how to include id and data attributes on the main component:

{{< php >}}
$dialog = new core\output\local\dropdown\dialog(
    'Open dialog',
    'Dialog content',
    [
        extras' => ['id' => 'mydropdown', 'data-foo' => 'bar']
    ]
);
echo $OUTPUT->render($dialog);
{{< / php >}}

Below is an example of how to include additional attributes to the options provided to the user:

{{< php >}}
$choice = new core\output\choicelist('Dialog content');

$choice->add_option('option1', 'Option 1', [
    extras' => ['id' => 'myoption1', 'data-foo' => 'bar1']
]);
$choice->add_option('option2', 'Option 2', [
    extras' => ['id' => 'myoption2', 'data-foo' => 'bar2']
]);

$choice->set_selected_value('option2');

$dialog = new core\output\local\dropdown\status('Open dialog button', $choice);
echo $OUTPUT->render($dialog);
{{< / php >}}

## Examples

<iframe src="../../../../examples/dropdowns.php" style="overflow:hidden;height:400px;width:100%;border:0" title="Moodle dynamic tabs"></iframe>
