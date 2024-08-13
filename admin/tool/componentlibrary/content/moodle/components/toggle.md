---
layout: docs
title: "Toggle input"
date: 2022-01-17T00:00:00+01:00
draft: false
weight: 60
tags:
- MDL-73470
- 4.0
---

## How to use

Toggle input is rendered using a template found in lib/templates/toggle.mustache.

The parameters for the template context are:
* id: Unique id for the toggle input.
* extraclasses: Any extra classes added to the toggle input outer container.
* checked: If the initial status is checked.
* disabled: If toggle input is disabled.
* dataattributes: Array of name/value elements added as data-attributes.
* title: Title text.
* label: Label text.
* labelclasses: Any extra classes added to the label container.

## Examples

<div class="small">
Checked toggle and with "sr-only" label.
</div>
{{< mustache template="core/toggle" >}}
    {
        "id": "example-toggle-1",
        "checked": true,
        "dataattributes": [{
            "name": "action",
            "value": "toggle-status"
        }],
        "title": "Toggle Enabled",
        "label": "Enable/disable status",
        "labelclasses": "sr-only"
    }
{{< /mustache >}}

<div class="mt-3 small">
Disabled toggle with extra classes.
</div>
{{< mustache template="core/toggle" >}}
    {
        "id": "example-toggle-2",
        "disabled": true,
        "extraclasses": "mt-2 ms-2",
        "dataattributes": [{
            "name": "action",
            "value": "toggle-status"
        }],
        "title": "Toggle Disabled",
        "label": "Enable/disable status"
    }
{{< /mustache >}}

## Use toggle as a template block

It is also possible to include *core/toggle* in any other template using [blocks](https://moodledev.io/docs/guides/templates#blocks), instead of rendering it with a context.
The parameters that you can define are:
* id: Unique id for the toggle input.
* extraclasses: Any extra classes added to the toggle input outer container.
* attributes: Any attributes added to the toggle input.
    * data-attributes
    * checked
    * disabled
* labelmarkup: Label element code block.
  * Should include *class="custom-control-label"*.
* title: Title text.
* label: Label text.
* labelclasses: Any extra classes added to the label container.

<div class="small">
Example of template using toggle as a block.
</div>
{{< mustache template="tool_componentlibrary/examples/toggle/example" >}}
    {
    }
{{< /mustache >}}
