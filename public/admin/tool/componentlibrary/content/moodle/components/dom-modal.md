
---
layout: docs
title: "HTML Modals"
description: "A reusable handled modal component"
date: 2021-12-09T14:48:00+08:00
draft: false
tags:
- MDL-71963
- MDL-72928
- "4.0"
---

## How it works

The core/utility module allows different modals to be displayed automatically when interacting with the page.

Modals are configured using a set of specific data-attributes.

## Source files

* `lib/amd/src/utility.js` ({{< jsdoc module="core/utility" >}})
* `lib/templates/modal.mustache`

## Usage
The confirmation AMD module is loaded automatically, so the only thing you need to do is to add some specific data attributes
to the target element.

To display a confirmation modal.
{{< highlight html >}}
<button type="button" class="btn btn-primary" data-modal="confirmation" data-modal-title-str='["delete", "core"]'
data-modal-content-str='["areyousure"]' data-modal-yes-button-str='["delete", "core"]'>Show confirmation modal</button>
{{< /highlight >}}

To display an alert modal.
{{< highlight html >}}
<button type="button" class="btn btn-primary" data-modal="alert" data-modal-title-str='["cookiesenabled", "core"]'
data-modal-content-str='["cookiesenabled_help_html", "core"]'>Show alert modal</button>
{{< /highlight >}}

You can also use it on PHP, you just need to set the attributes parameter to any moodle output component that takes attributes:
{{< php >}}
echo $OUTPUT->single_button('#', get_string('delete'), 'get', [
    'data-modal' => 'modal',
    'data-modal-title-str' => json_encode(['delete', 'core']),
    'data-modal-content-str' => json_encode(['areyousure']),
    'data-modal-yes-button-str' => json_encode(['delete', 'core'])
]);
{{< / php >}}

## Attributes

<table class="table">
  <thead>
    <tr>
      <th style="width: 250px;">Data attribute</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>data-modal</td>
      <td>One of either "confirmation", or "alert".</td>
    </tr>
    <tr>
      <td>data-modal-title-str</td>
      <td>The modal title language string identifier, must be provided in JSON encoded format.</td>
    </tr>
    <tr>
      <td>data-modal-content-str</td>
      <td>The modal content or content language string identifier, must be provided in JSON encoded format.</td>
    </tr>
    <tr>
      <td>data-modal-yes-button-str</td>
      <td>
        The language string identifier for the "Yes" button, must be provided in JSON encoded format.
        Confirmation modals only.
      </td>
    </tr>
    <tr>
      <td>data-modal-toast</td>
      <td>
        If set to "true" it will display a modal toast in the end.
        Confirmation modals only.
      </td>
    </tr>
    <tr>
      <td>data-modal-toast-confirmation-str</td>
      <td>
        The confirmation toast language string identifier, must be provided in JSON encoded format.
        Confirmation modals only.
      </td>
    </tr>
    <tr>
      <td>data-modal-destination</td>
      <td>
        An url to redirect the user to.
        Confirmation modals only.
      </td>
    </tr>
  </tbody>
</table>

## Examples

### Basic Alert modal

{{< example >}}
<button type="button" class="btn btn-primary" data-modal="alert" data-modal-title-str='["cookiesenabled", "core"]'
data-modal-content-str='["cookiesenabled_help_html", "core"]'>Show alert modal</button>
{{< /example >}}

### Basic confirmation modal

{{< example >}}
<button type="button" class="btn btn-primary" data-modal="confirmation" data-modal-title-str='["delete", "core"]'
data-modal-content-str='["areyousure"]' data-modal-yes-button-str='["delete", "core"]'>Show confirmation modal</button>
{{< /example >}}

### Confirmation modal with a toast

{{< example >}}
<button type="button" class="btn btn-primary" data-modal="confirmation" data-modal-title-str='["delete", "core"]'
data-modal-content-str='["areyousure"]' data-modal-yes-button-str='["delete", "core"]' data-modal-toast="true"
data-modal-toast-confirmation-str='["deleteblockinprogress", "block", "Online users"]'>Show confirmation modal</button>
{{< /example >}}

### Confirmation modal with redirect

{{< example >}}
<button type="button" class="btn btn-primary" data-modal="confirmation" data-modal-title-str='["delete", "core"]'
data-modal-content-str='["areyousure"]' data-modal-yes-button-str='["delete", "core"]'
data-modal-destination="http://moodle.com">Show confirmation modal</button>
{{< /example >}}
