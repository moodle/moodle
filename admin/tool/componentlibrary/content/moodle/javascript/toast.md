---
layout: docs
title: "Toast"
date: 2021-12-09T14:48:00+08:00
draft: false
tags:
- MDL-66828
- MDL-67074
- MDL-72544
- 3.8
---

## How it works

Toasts are lightweight notifications designed to mimic push notifications.
Moodle toasts are based upon core the Bootstrap notification feature, but with a Moodle Javascript module wrapper.

## Source files

* `lib/amd/src/toast.js` ({{< jsdoc module="core/toast" >}})
* `lib/templates/local/toast/message.mustache`

## Examples

Toasts can only be applied from JavaScript, and the most basic form just takes the message to be displayed.

### Displaying a simple message

{{< example >}}
<button type="button" class="btn btn-info" data-example-name="basic">Basic example</button>

{{#js}}
require(['core/toast'], Toast => {
    const button = document.querySelector("[data-example-name='basic']")
    button.addEventListener('click', () => {
        Toast.add('This is the message for the toast');
    });
});
{{/js}}
{{< /example >}}

### Applying semantic styles

The standard semantic Bootstrap styles can be applied.

{{< example >}}
<button type="button" class="btn btn-success" data-example-name="semantic" data-type="success">Success</button>
<button type="button" class="btn btn-danger" data-example-name="semantic" data-type="danger">Danger</button>
<button type="button" class="btn btn-warning" data-example-name="semantic" data-type="warning">Warning</button>
<button type="button" class="btn btn-info" data-example-name="semantic" data-type="info">Info</button>

{{#js}}
require(['core/toast'], Toast => {
    const container = document.querySelector("[data-example-name='semantic']").parentNode;
    container.addEventListener('click', e => {
        if (!e.target.closest('[data-type]')) {
            return;
        }
        Toast.add(`This toast will be displayed with the ${e.target.dataset.type} type.`, {
            type: e.target.dataset.type,
        });
    });
});
{{/js}}
{{< /example >}}

### Auto-hide, and close buttons

The standard behaviour of the toast is to auto-hide after a short period which
can be configured or disabled. A close button can also be displayed, which is
recommended when a longer period is used.

| Name          | Description                                                                   |
| ------------- | ----------------------------------------------------------------------------- |
| `delay`       | An auto-hide delay can be configured by providing a millisecond setting       |
| `autohide`    | The auto-hide can be entirely disabled using this boolean setting             |
| `closeButton` | The presence of the close button can be controlled using this boolean setting |

{{< example >}}
<button type="button" class="btn btn-primary" data-example-name="autohide-long">Auto-hide long</button>
<button type="button" class="btn btn-primary" data-example-name="autohide-disabled">Auto-hide disabled</button>

{{#js}}
require(['core/toast'], Toast => {
    document.querySelector("[data-example-name='autohide-long']").addEventListener('click', e => {
        Toast.add('This message will be displayed for 30 seconds with a closeButton', {
            delay: 30000,
            closeButton: true,
        });
    });

    document.querySelector("[data-example-name='autohide-disabled']").addEventListener('click', e => {
        Toast.add('This message will be displayed until closed using the closeButton.', {
            autohide: false,
            closeButton: true,
        });
    });
});
{{/js}}
{{< /example >}}


### Using a Language String

The standard behaviour of the toast is to auto-hide after a short period which
can be configured or disabled. A close button can also be displayed, which is
recommended when a longer period is used.

{{< example >}}
<button type="button" class="btn btn-primary" data-example-name="langstring">Language string</button>

{{#js}}
require(['core/toast', 'core/str'], (Toast, Str) => {
    document.querySelector("[data-example-name='langstring']").addEventListener('click', e => {
        Toast.add(Str.get_string('ok'));
    });
});
{{/js}}
{{< /example >}}

