---
layout: docs
title: "Notification badges"
description: "Notification badges used in Moodle"
date: 2025-02-25T09:40:32+01:00
draft: false
weight: 50
tags:
- Available
---

## How to use notification badges

Notification badges are used to display concise information or status indicators. They can be used to indicate the user an element has relevant information or new information.

## Example

{{< example show_markup="false">}}
<button class="btn btn-outline-secondary">
    Grade
    <span class="ms-1 badge rounded-pill text-bg-primary" title="Needs grading"><span class="visually-hidden"> (</span>1<span class="visually-hidden">)</span></span>
</button>
{{< /example >}}

### Usage

The `core_renderer` provides a `notice_badge` method to create a badge. The method accepts the following parameters:

- **contents** (`string`): The content to display inside the badge.
- **badgestyle** (`core\output\local\properties\badge`): The badge style to use. This is an enum that defines all possible badge styles. By default it will use primary style.
- **title** (`string`): An optional title attribute for the badge.

This is an example of how to render a notice badge using the `core_renderer` (the example uses dependency injection to get the renderer instance, see [Dependency Injection](https://moodledev.io/docs/5.0/apis/core/di) for more information):

```php
// Get the core renderer.
$renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

// Save the badge into a variable.
$badge = $renderer->notice_badge(
    contents: ($needgrading > 0) ? $needgrading : '',
    title: get_string('numberofsubmissionsneedgrading', 'assign'),
    badgestyle: \core\output\local\properties\badge::SECONDARY,
);

$content = new action_link(
    url: new url('/some/index.php'),
    text: get_string('gradeverb') . $badge,
);
echo $renderer->render($content);
```

### Badge Styling

The badge can be styled using the `\core\output\local\properties\badge` enum. The enum provides the following badge styles:

- `badge::PRIMARY`: 'primary' (default style for badges)
- `badge::SECONDARY`: 'secondary' (usually in dark gray color)
- `badge::SUCCESS`: 'success' (usually in green color)
- `badge::DANGER`: 'danger' (usually in red color)
- `badge::WARNING`: 'warning' (usually in yellow color)
- `badge::INFO`: 'info' (usually in blue color)

This is how every badge style looks like:

{{< example show_markup="false">}}
<ul class="list-group">
    <li class="list-group-item">
        Primary badge
        <span class="ms-1 badge rounded-pill text-bg-primary" title=""><span class="visually-hidden"> (</span>1<span class="visually-hidden">)</span></span>
    </li>
    <li class="list-group-item">
        Secondary badge
        <span class="ms-1 badge rounded-pill text-bg-secondary" title=""><span class="visually-hidden"> (</span>1<span class="visually-hidden">)</span></span>
    </li>
    <li class="list-group-item">
        Success badge
        <span class="ms-1 badge rounded-pill text-bg-success" title=""><span class="visually-hidden"> (</span>1<span class="visually-hidden">)</span></span>
    </li>
    <li class="list-group-item">
        Danger badge
        <span class="ms-1 badge rounded-pill text-bg-danger" title=""><span class="visually-hidden"> (</span>1<span class="visually-hidden">)</span></span>
    </li>
    <li class="list-group-item">
        Warning badge
        <span class="ms-1 badge rounded-pill text-bg-warning" title=""><span class="visually-hidden"> (</span>1<span class="visually-hidden">)</span></span>
    </li>
    <li class="list-group-item">
        Info badge
        <span class="ms-1 badge rounded-pill text-bg-info" title=""><span class="visually-hidden"> (</span>1<span class="visually-hidden">)</span></span>
    </li>
</ul>
{{< /example >}}

### Validate badges in behat

For behat and screen readers, the badge is read as a text in parentheses. For example, the badge with the text "1" is read as "1 (1)". However, it is not recommended to test the badge text directly in combination with the parent element text because mustache templates may add some line breaks. Instead, you should test the badge text separately.

This is an example of how to test the example "Grade" badge in behat:

```gherkin
And I should see "Grade" in the "Assign with pending grades" "table_row"
And I should see "(2)" in the "Assign with pending grades" "table_row"
```
