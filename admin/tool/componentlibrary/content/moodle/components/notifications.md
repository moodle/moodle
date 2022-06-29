---
layout: docs
title: "Notifications"
description: "Moodle notifications"
date: 2020-02-04T09:40:32+01:00
draft: false
weight: 50
tags:
- Available
- Needs review
---

## How it works

Notifications are coupled with actions and provide instant feedback to the action results. Moodle notifications are shown right above the actionable content or overlaying the user interface for JavaScript related actions.

## Example

{{< example show_markup="false">}}
<div class="alert alert-info alert-block fade in foo bar" role="alert" data-aria-autofocus="true" id="yui_3_17_2_1_1599746674354_24">
    <button type="button" class="close" data-dismiss="alert">×</button>
    Hello
</div>
{{< /example >}}

## Source files

* `lib/amd/src/notification.js`
* `lib/templates/notification_info.mustache`
* `lib/templates/notification_success.mustache`
* `lib/templates/notification_warning.mustache`
* `lib/templates/notification_error.mustache`

## Core renderer

Notifications can be added in PHP using the core renderer notification method

{{< php >}}
  $OUTPUT->notification('message', 'info');
{{< / php >}}

## Notification templates

{{< mustache template="core/notification_info" >}}
{{< /mustache >}}

{{< mustache template="core/notification_success" >}}
{
    "message": "Your pants are on awesome!",
    "closebutton": 1,
    "announce": 1,
    "extraclasses": "foo bar"
}
{{< /mustache >}}

{{< mustache template="core/notification_warning" >}}
{
    "message": "Your pants are on down!",
    "closebutton": 1,
    "announce": 1,
    "extraclasses": "foo bar"
}
{{< /mustache >}}

{{< mustache template="core/notification_error" >}}
{
    "message": "Your pants are on fire!",
    "closebutton": 1,
    "announce": 1,
    "extraclasses": "foo bar"
}
{{< /mustache >}}

## JavaScript Notifications

{{< example >}}
<button class="btn btn-secondary" data-action="shownotification">Show JS Notification</button>
{{#js}}
require(
[
    'core/notification'
],
function(
    Notification
) {
    document.querySelector('[data-action="shownotification"]').addEventListener('click', function() {
        Notification.alert('Notification message', 'Extra content for notification message');
    });
});
{{/js}}
{{< /example >}}

## Toast Notifications

{{< example >}}
<button class="btn btn-secondary" data-action="showtoastnotification">Show Toast Notification</button>
{{#js}}
require(
[
    'core/toast'
],
function(
    Toast
) {
    document.querySelector('[data-action="showtoastnotification"]').addEventListener('click', function() {
        Toast.add('Toast message');
    });
});
{{/js}}
{{< /example >}}
