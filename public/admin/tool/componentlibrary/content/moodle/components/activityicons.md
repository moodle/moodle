---
layout: docs
title: "Activity icons"
description: "Activity icons are used to quickly identify the activity types"
draft: false
weight: 5
toc: true
tags:
- Available
- '4.0'
- Updated
- '5.0'
---

## Activity icon types

Moodle activity icons are single black SVG icons that are stored in `mod/PLUGINNAME/pix/monologo.svg`.

## Rendering activity icons

The `core_course\output\activity_icon` class is used to render activity icons. It can be used in several ways depending on the context. Also, there is the `core_course\activity_icon` template that can be included directly from mustache templates.

### Rendering the activity plugin icon

The following example shows how to render the default activity icon:

{{< php >}}
use core_course\output\activity_icon;
$renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

$icon = activity_icon::from_modname('quiz');

echo $renderer->render($icon);
{{< /php >}}

By default, the activity icon will be rendered colored with the activity purpose color (see below).

### Rendering the activity icon from a cm_info object

Specific activity instances can have their own custom icons. For example, the `mod_resource` displays the MIME type icon for the resource. To render the activity icon from a `cm_info` object, use the static constructor `from_cm_info`. The method will return an instance of `activity_icon` with the icon URL set to the custom icon if necessary.

It is possible to render the activity icon from a `cm_info` object:

{{< php >}}
use core_course\output\activity_icon;
$renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();
$cminfo = get_fast_modinfo($courseid)->get_cm($cmid);

$icon = activity_icon::from_cm_info($cminfo);

echo $renderer->render($icon);
{{< /php >}}

### Rendering the activity icon in dark color

There are pages like the gradebook where the activity icons must be rendered in black color for accessibility or usability reasons. The `core_course\output\activity_icon` class has a `set_colourize` method to define if the icon must be colorized or not.

The following example shows how to render the default activity icon in black:

{{< php >}}
use core_course\output\activity_icon;
$renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

$icon = activity_icon::from_modname('quiz')
    ->set_colourize(false);

echo $renderer->render($icon);
{{< /php >}}

### Set the activity icon size

When rendered in a page with limited space the icons will be shown in their original design, for example on the course gradebook where activity show in the grade table header.

The `core_course\output\activity_icon` class has a `set_icon_size` method to define the icon size. The method accepts any value from `core\output\local\properties\iconsize` enum.

The following example shows how to render the default activity icon with a custom size:

{{< php >}}
use core_course\output\activity_icon;
use core\output\local\properties\iconsize;
$renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

$icon = activity_icon::from_modname('quiz')
    ->set_icon_size(iconsize::SIZE4);

echo $renderer->render($icon);
{{< /php >}}

### Add extra classes to the activity icon

The `core_course\output\activity_icon` class has a `set_extra_classes` method to add extra classes to the icon container.

The following example shows how to render the default activity icon with extra classes:

{{< php >}}
use core_course\output\activity_icon;
$renderer = \core\di::get(\core\output\renderer_helper::class)->get_core_renderer();

$icon = activity_icon::from_modname('quiz')
    ->set_extra_classes(['my-extra-class']);

echo $renderer->render($icon);
{{< /php >}}

## Activity purposes

In the HTML for the example above you might notice the ```assessment``` css class after ```.activityiconcontainer```. This class is the result of assigning a *purpose* to the quiz activity in ```/mod/quiz/lib.php```.

{{< php >}}
function quiz_supports($feature) {
    switch($feature) {
        ..
        case FEATURE_PLAGIARISM: return true;
        case FEATURE_MOD_PURPOSE: return MOD_PURPOSE_ASSESSMENT;
        ..
    }
}
{{< /php >}}

Since Moodle 4.4, the available activity purposes are:

* Administration (MOD_PURPOSE_ADMINISTRATION)
* Assessment (MOD_PURPOSE_ASSESSMENT)
* Collaboration (MOD_PURPOSE_COLLABORATION)
* Communication (MOD_PURPOSE_COMMUNICATION)
* Interactive content (MOD_PURPOSE_INTERACTIVECONTENT)
* Resource (MOD_PURPOSE_CONTENT)
* Other (MOD_PURPOSE_OTHER)

> NOTE: On Moodle 4.3 downwards, MOD_PURPOSE_INTERFACE was also available, but it has been deprecated, so it's not recommended to use it.

### Purpose colours

The activity icon colours can be customised using the theme Boost 'Raw initial SCSS' feature. The following variables are available:

{{< highlight scss >}}
$activity-icon-administration-bg:     #da58ef !default;
$activity-icon-assessment-bg:         #f90086 !default;
$activity-icon-collaboration-bg:      #5b40ff !default;
$activity-icon-communication-bg:      #eb6200 !default;
$activity-icon-content-bg:            #0099ad !default;
$activity-icon-interactivecontent-bg: #8d3d1b !default;
{{</ highlight >}}

### Custom activity icons

Some activities allow icons to be customised. This can be done by implementing callback `XXX_get_coursemodule_info()` returning instance of object (for instance, `mod/lti/lib.php`).

{{< php >}}
$info = new cached_cm_info();
$info->iconurl = new moodle_url('https://moodle.org/theme/moodleorg/pix/moodle_logo_small.svg');
{{< /php >}}

To get this customised icon url, use:

{{< php >}}
$iconurl = get_fast_modinfo($courseid)->get_cm($cmid)->get_icon_url()->out(false);
{{< /php >}}

And to render the custom icon:

{{< php >}}
use core_course\output\activity_icon;

echo $OUTPUT->render(activity_icon::from_cm_info($cminfo));
{{< /php >}}

<div class="d-flex mb-3">
    <div class="flex-shrink-0 activityiconcontainer lti me-3">
        <img alt="lti icon" title="lti icon" src="https://moodle.org/theme/moodleorg/pix/moodle_logo_small.svg" class="activityicon ">    </div>
    <div class="flex-grow-1 align-self-center">
        <div class="text-uppercase small">external</div>
        <div class="activityname"><a href="#">External tool module</a></div>
    </div>
</div>

### Branded icons

Since Moodle 4.4, a new callback has been added to the modules. Branded icons are displayed with their original colours and they are not affected by the activity purpose colours.

{{< php >}}
/**
 * Whether the activity is branded.
 * This information is used, for instance, to decide if a filter should be applied to the icon or not.
 *
 * @return bool True if the activity is branded, false otherwise.
 */
function h5pactivity_is_branded(): bool {
    return true;
}
{{< /php >}}

<div class="d-flex mb-3">
    <div class="flex-shrink-0 activityiconcontainer me-3">
        {{< image "h5pactivity/monologo.svg" "H5P activity icon" "activityicon">}}    </div>
    <div class="flex-grow-1 align-self-center">
        <div class="text-uppercase small">h5pactivity</div>
        <div class="activityname"><a href="#">H5P module</a></div>
    </div>
</div>

## Examples

<div class="d-flex mb-3">
    <div class="flex-shrink-0 activityiconcontainer administration me-3">
        {{< image "quiz/monologo.svg" "Admin icon" "activityicon">}}    </div>
    <div class="flex-grow-1 align-self-center">
        <div class="text-uppercase small">Administration</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="d-flex mb-3">
    <div class="flex-shrink-0 activityiconcontainer assessment me-3">
        {{< image "quiz/monologo.svg" "Assessment icon" "activityicon">}}    </div>
    <div class="flex-grow-1 align-self-center">
        <div class="text-uppercase small">Assessment</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="d-flex mb-3">
    <div class="flex-shrink-0 activityiconcontainer collaboration me-3">
        {{< image "wiki/monologo.svg" "Collaboration icon" "activityicon">}}    </div>
    <div class="flex-grow-1 align-self-center">
        <div class="text-uppercase small">Collaboration</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="d-flex mb-3">
    <div class="flex-shrink-0 activityiconcontainer communication me-3">
        {{< image "choice/monologo.svg" "Communication icon" "activityicon">}}    </div>
    <div class="flex-grow-1 align-self-center">
        <div class="text-uppercase small">Communication</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="d-flex mb-3">
    <div class="flex-shrink-0 activityiconcontainer interactivecontent me-3">
        {{< image "lesson/monologo.svg" "Interactive content icon" "activityicon">}}    </div>
    <div class="flex-grow-1 align-self-center">
        <div class="text-uppercase small">Interactive content</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="d-flex mb-3">
    <div class="flex-shrink-0 activityiconcontainer content me-3">
        {{< image "book/monologo.svg" "Resource icon" "activityicon">}}    </div>
    <div class="flex-grow-1 align-self-center">
        <div class="text-uppercase small">Resource</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="d-flex mb-3">
    <div class="flex-shrink-0 activityiconcontainer me-3">
        {{< image "lti/monologo.svg" "Other icon" "activityicon">}}    </div>
    <div class="flex-grow-1 align-self-center">
        <div class="text-uppercase small">Other</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>
