---
layout: docs
title: "Collapsable sections"
description: "A reusable collapsable section component"
date: 2024-12-20T10:10:00+08:00
draft: false
tags:
- MDL-83869
- "5.0"
---

## How it works

The collapsable section component in Moodle allows you to create sections of content that can be expanded or collapsed by the user. This is useful for organizing content in a way that doesn't overwhelm the user with too much information at once. The component is built using a combination of PHP, Mustache templates, and JavaScript.

## Source files

- `lib/templates/local/collapsable_section.mustache`: The Mustache template for rendering the collapsable section.
- `lib/classes/output/local/collapsable_section.php`: The output class for the collapsable section component.
- `lib/amd/src/local/collapsable_section/events.js`: JavaScript module for handling events related to the collapsable section.
- `lib/amd/src/local/collapsable_section/controls.js`: JavaScript module for controlling the collapsable section.

## Usage

To use the collapsable section component, you need to create an instance of the `collapsable_section` class and render it using Moodle's output renderer. You can customize the title content, section content, CSS classes, and additional HTML attributes.

Example:

{{< php >}}
use core\output\local\collapsable_section;

// Create an instance of the collapsable section.
$section = new collapsable_section(
    titlecontent: 'Section Title',
    sectioncontent: 'This is the content of the section.',
);

echo $OUTPUT->render($section);
{{< / php >}}

{{< mustache template="core/local/collapsable_section" >}}
    {
        "titlecontent": "Section Title",
        "sectioncontent": "This is the content of the section."
    }
{{< /mustache >}}

You can also add CSS classes, extra HTML attributes, and customize the expand and collapse labels of the collapsable section:

{{< php >}}
$section = new collapsable_section(
    titlecontent: 'Section Title',
    sectioncontent: 'This is the content of the section.',
    open: true, // Optional parameter to set the section as open by default.
    classes: 'p-3 rounded bg-dark text-white', // Optional parameter to add custom CSS classes.
    extras: ['id' => 'MyCollapsableSection', 'data-foo' => 'bar'], // Optional HTML attributes.
    expandlabel: 'Show more', // Optional label for the expand button.
    collapselabel: 'Show less', // Optional label for the collapse button.
);

echo $OUTPUT->render($section);
{{< / php >}}

{{< mustache template="core/local/collapsable_section" >}}
    {
        "titlecontent": "Section Title",
        "sectioncontent": "This is the content of the section.",
        "open": true,
        "classes": "p-3 rounded bg-dark text-white",
        "elementid": "someuniqueid",
        "extras": [
            {
                "attribute": "id",
                "value": "MyCollapsableSection"
            },
            {
                "attribute": "data-foo",
                "value": "bar"
            }
        ],
        "expandlabel": "Show more",
        "collapselabel": "Show less"
    }
{{< /mustache >}}

## Include a collapsable section from a mustache template

Collapsable sections can also be included from a Mustache template using the `core/local/collapsable_section` template. This template allows you to define the title content and section content within the template.

{{< mustache template="tool_componentlibrary/examples/collapsablesections/includesection" >}}
    {
    }
{{< /mustache >}}

## JavaScript

### Control a section

The collapsable sections component includes a JavaScript module for controlling the sections. This module provides methods to hide, show, and toggle the visibility of the sections.

To use the JavaScript controls, you need to import the `CollapsableSection` module and create an instance from a selector:

```javascript
import CollapsableSection from 'core/local/collapsable_section/controls';

const section = CollapsableSection.instanceFromSelector('#MyCollapsableSection');

// Use hide, show, and toggle methods to control the section.
section.hide();
section.show();
section.toggle();
```

### Get the state of a section

You can also check the state of a section using the `isHidden` method:

```javascript
import CollapsableSection from 'core/local/collapsable_section/controls';

const section = CollapsableSection.instanceFromSelector('#MyCollapsableSection');

if (section.isVisible()) {
    console.log('The section is hidden.');
} else {
    console.log('The section is visible.');
}
```

### Events

The collapsable sections component also includes a JavaScript module for handling events. This module wraps the standard Bootstrap collapsable events and provides custom event types for collapsable sections.

The component triggers two main events:

- `core_collapsable_section_shown`: when the collapsed content is shown.
- `core_collapsable_section_hidden`: when the collapsed content is hidden.

For convenience, the `core/local/collapsable_section/events` also list the original Bootstrap events. They should not be needed in most cases, but they are available if you need them:

- `show.bs.collapse`: when the collapse is starting to show.
- `shown.bs.collapse`: when the collapse has been shown.
- `hide.bs.collapse`: when the collapse is starting to hide.
- `hidden.bs.collapse`: when the collapse has been hidden.

To listen for events related to the collapsable sections, you need to import the `eventTypes` from the `events` module and add event listeners:

```javascript
import {eventTypes as collapsableSectionEventTypes} from 'core/local/collapsable_section/events';

document.addEventListener(collapsableSectionEventTypes.shown, event => {
    console.log(event.target); // The HTMLElement relating to the section that was shown.
});

document.addEventListener(collapsableSectionEventTypes.hidden, event => {
    console.log(event.target); // The HTMLElement relating to the section that was hidden.
});
```
