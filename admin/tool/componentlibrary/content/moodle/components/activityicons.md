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
- '4.4'
---

## Activity icon types

Moodle activity icons are single black SVG icons that are stored in `mod/PLUGINNAME/pix/monologo.svg`.

### Minimal activity icons

When rendered in a page with limited space the icons will be shown in their original design, for example on the course gradebook where activity show in the grade table header.

> NOTE: The icon is using the ```.icon``` CSS class which limits the maximum width and height. It's recommended to define width and height into the SVG.

{{< example >}}
<div class="d-flex mb-3">
    <div class="d-flex border align-items-center p-1">
        {{< image "quiz/monologo.svg" "Quiz icon" "icon">}} Multiple choice quiz 1
    </div>
</div>
{{< /example  >}}

### Coloured activity icons

In places like the course page and the activity chooser icons have a more prominent role and they should be rendered outlined colored against a transparent background.

The CSS classes for these icons are ```activityiconcontainer``` wrapper class with the added activity name. And the ```activityicon``` class for the image. See the template ```course/format/templates/local/content/cm/title.mustache``` for more info.

<div class="media mb-3">
    <div class="activityiconcontainer assessment me-3">
        {{< image "quiz/monologo.svg" "Quiz icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">quiz</div>
        <div class="activityname"><a href="#">Multiple choice quiz 1</a></div>
    </div>
</div>

### Activity purposes

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

The activity icon colours can be customised using the theme Boost 'Raw initial SCSS' feature. Simply copy any of these scss variables that you want to customize, change the colour value, generate the filter using, for instance https://codepen.io/sosuke/pen/Pjoqqp and done! There is no background colour or filter for the 'Other' or the 'Interface' purposes.

{{< highlight scss >}}
$activity-icon-administration-bg:     #da58ef !default;
$activity-icon-assessment-bg:         #f90086 !default;
$activity-icon-collaboration-bg:      #5b40ff !default;
$activity-icon-communication-bg:      #eb6200 !default;
$activity-icon-content-bg:            #0099ad !default;
$activity-icon-interactivecontent-bg: #8d3d1b !default;

$activity-icon-administration-filter:
    invert(45%) sepia(46%) saturate(3819%) hue-rotate(260deg) brightness(101%) contrast(87%) !default;
$activity-icon-assessment-filter:
    invert(36%) sepia(98%) saturate(6969%) hue-rotate(315deg) brightness(90%) contrast(119%) !default;
$activity-icon-collaboration-filter:
    invert(25%) sepia(54%) saturate(6226%) hue-rotate(245deg) brightness(100%) contrast(102%) !default;
$activity-icon-communication-filter:
    invert(48%) sepia(74%) saturate(4887%) hue-rotate(11deg) brightness(102%) contrast(101%) !default;
$activity-icon-content-filter:
    invert(49%) sepia(52%) saturate(4675%) hue-rotate(156deg) brightness(89%) contrast(102%) !default;
$activity-icon-interactivecontent-filter:
    invert(25%) sepia(63%) saturate(1152%) hue-rotate(344deg) brightness(94%) contrast(91%) !default;
{{</ highlight >}}

### Custom activity icons

Some activities allow icons to be customised. This can be done by implementing callback `XXX_get_coursemodule_info()` returning instance of object (for instance, `mod/lti/lib.php`).

{{< php >}}
$info = new cached_cm_info();
$info->iconurl = new moodle_url('https://moodle.org/theme/moodleorg/pix/moodle_logo_small.svg');
{{< /php >}}

To get this customised icon, use:

{{< php >}}
$iconurl = get_fast_modinfo($courseid)->get_cm($cmid)->get_icon_url()->out(false);
{{< /php >}}

<div class="media mb-3">
    <div class="activityiconcontainer lti me-3">
        <img alt="lti icon" title="lti icon" src="https://moodle.org/theme/moodleorg/pix/moodle_logo_small.svg" class="activityicon ">    </div>
    <div class="media-body align-self-center">
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

<div class="media mb-3">
    <div class="activityiconcontainer me-3">
        {{< image "h5pactivity/monologo.svg" "H5P activity icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">h5pactivity</div>
        <div class="activityname"><a href="#">H5P module</a></div>
    </div>
</div>

## Examples

<div class="media mb-3">
    <div class="activityiconcontainer administration me-3">
        {{< image "quiz/monologo.svg" "Admin icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">Administration</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer assessment me-3">
        {{< image "quiz/monologo.svg" "Assessment icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">Assessment</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer collaboration me-3">
        {{< image "wiki/monologo.svg" "Collaboration icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">Collaboration</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer communication me-3">
        {{< image "choice/monologo.svg" "Communication icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">Communication</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer interactivecontent me-3">
        {{< image "lesson/monologo.svg" "Interactive content icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">Interactive content</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer content me-3">
        {{< image "book/monologo.svg" "Resource icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">Resource</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer me-3">
        {{< image "lti/monologo.svg" "Other icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">Other</div>
        <div class="activityname"><a href="#">Module name</a></div>
    </div>
</div>
