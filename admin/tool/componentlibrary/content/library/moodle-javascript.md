---
layout: docs
title: "Moodle JavaScript"
date: 2020-01-14T13:38:37+01:00
group: moodle-components
draft: false
menu: "main"
---

## Running AMD modules

When using this (shortcode) syntax you can showcase your HTML and add some RequireJS style Javascript that will call core AMD modules.

In order for this to work you need to use the JavaScript syntax used in core Mustache templates. See the ```{{js}}``` tags in this example below:

{{< example >}}
<div id="toasttest" role="alert" aria-live="assertive" aria-atomic="true" class="toast" data-autohide="false">
  <div class="toast-header">
    <img src="http://placekitten.com/50/50" class="rounded me-2" alt="PlaceKitten">
    <strong class="me-auto">Bootstrap</strong>
    <small>11 mins ago</small>
    <button type="button" class="ms-2 mb-1 btn-close" data-dismiss="toast" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <div class="toast-body">
    Hello, world! This is a toast message.
  </div>
</div>
{{#js}}
require(
[
    'jquery',
    'theme_boost/toast',
],
function(
    $,
    Toast
) {
    var root = $('#toasttest');
    root.toast('show');
});
{{/js}}
{{< /example >}}