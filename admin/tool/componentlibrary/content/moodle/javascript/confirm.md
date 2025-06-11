---
layout: docs
title: "Confirm"
description: "A reusable confirmation modal component"
date: 2021-12-09T14:48:00+08:00
draft: false
tags:
- MDL-71963
- "4.0"
---

## How it works

The confirm module is automatically invoked on page load, you just need to add some specific data attributes
to the element that will trigger the confirmation modal.

## Source files

* `lib/amd/src/utility.js` ({{< jsdoc module="core/utility" >}})
* `lib/templates/modal.mustache`

## Usage
The confirmation AMD module is loaded automatically, so the only thing you need to do is to add some specific data attributes
to the target element:
{{< highlight html >}}
<button type="button" class="btn btn-primary" data-confirmation="modal" data-confirmation-title-str='["delete", "core"]'
data-confirmation-content-str='["areyousure"]' data-confirmation-yes-button-str='["delete", "core"]'>Show confirmation modal</button>
{{< /highlight >}}

You can also use it on PHP, you just need to set the attributes parameter to any moodle output component that takes attributes:
{{< php >}}
echo $OUTPUT->single_button('#', get_string('delete'), 'get', [
    'data-confirmation' => 'modal',
    'data-confirmation-title-str' => json_encode(['delete', 'core']),
    'data-confirmation-content-str' => json_encode(['areyousure']),
    'data-confirmation-yes-button-str' => json_encode(['delete', 'core'])
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
      <td>data-confirmation</td>
      <td>The identifier value must be "modal" so the module can find and register an event listener for that element.</td>
    </tr>
    <tr>
      <td>data-confirmation-title-str</td>
      <td>The modal title language string identifier, must be provided in JSON encoded format.</td>
    </tr>
    <tr>
      <td>data-confirmation-content-str</td>
      <td>The modal main content language string identifier, must be provided in JSON encoded format.</td>
    </tr>
    <tr>
      <td>data-confirmation-yes-button-str</td>
      <td>The language string identifier for the "Yes" button, must be provided in JSON encoded format.</td>
    </tr>
    <tr>
      <td>data-confirmation-toast</td>
      <td>If set to "true" it will display a confirmation toast in the end.</td>
    </tr>
    <tr>
      <td>data-confirmation-toast-confirmation-str</td>
      <td>The confirmation toast language string identifier, must be provided in JSON encoded format.</td>
    </tr>
    <tr>
      <td>data-confirmation-destination</td>
      <td>An url to redirect the user to.</td>
    </tr>
  </tbody>
</table>

## Examples

### Basic confirmation modal

#### Simple Modal

{{< example >}}
<button type="button" class="btn btn-primary" data-confirmation="modal" data-confirmation-title-str='["ok", "core"]'
data-confirmation-content-str='["areyousure"]' data-confirmation-yes-button-str='["ok", "core"]'>Show confirmation modal</button>
{{< /example >}}

#### Delete Modal

{{< example >}}
<button type="button" class="btn btn-primary" data-confirmation="modal" data-confirmation-type="delete" data-confirmation-title-str='["delete", "core"]'
data-confirmation-content-str='["areyousure"]' data-confirmation-yes-button-str='["delete", "core"]'>Show delete modal</button>
{{< /example >}}


### Confirmation modal with a toast

{{< example >}}
<button type="button" class="btn btn-primary" data-confirmation="modal" data-confirmation-title-str='["save", "core"]'
data-confirmation-content-str='["areyousure"]' data-confirmation-yes-button-str='["save", "core"]' data-confirmation-toast="true"
data-confirmation-toast-confirmation-str='["saved", "core_question", "My question"]'>Show confirmation modal</button>
{{< /example >}}

### Confirmation modal with redirect

{{< example >}}
<button type="button" class="btn btn-primary" data-confirmation="modal" data-confirmation-title-str='["save", "core"]'
data-confirmation-content-str='["areyousure"]' data-confirmation-yes-button-str='["save", "core"]'
data-confirmation-destination="http://moodle.com">Show confirmation modal</button>
{{< /example >}}
