---
layout: docs
title: "Activity icons"
description: "Activity icons are used to quickly identify the activty types"
date: 2020-01-14T16:32:24+01:00
draft: false
weight: 5
toc: true
tags:
- Available
- '4.0'
---

## Activity icon types

Moodle activity icons are single black svg icons that is stored in mod/PLUGINNAME/pix/icon.svg.

### Minimal activity icons
When rendered in a page with limited space the icons will be shown in their original design, for example on the course gradebook where activity show in the grade table header. Note: the icon is using the ```.icon``` css class for sizing.

<div class="d-flex mb-3">
    <div class="d-flex border align-items-center p-1">
        {{< image "quiz/icon.svg" "Quiz icon" "icon">}} Multiple choice quiz 1
    </div>
</div>

### Coloured activity icons
In places like the course page and the activity chooser icons have a more prominent role and they should be rendered on a coloured background in white.

The CSS classes for these icons are ```activityiconcontainer``` wrapper class with the added activity name. And the ```activityicon``` class for the image. See the template ```course/format/templates/local/content/cm/title.mustache``` for more info.

{{< example >}}
<div class="media mb-3">
    <div class="activityiconcontainer assessment mr-3">
        {{< image "quiz/icon.svg" "Quiz icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">quiz</div>
        <div class="activityname"><a href="#">Multiple choice quiz 1</a></div>
    </div>
</div>
{{< /example  >}}

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

The available activity purposes are:

* Administration
* Assessment
* Collaboration
* Communication
* Content
* Interface
* Other

each defined as 'MOD_PURPOSE_X', so Assessment is MOD_PURPOSE_ASSESSMENT.

### Purpose colours

The activity icon colours can be customised using the theme Boost 'Raw initial SCSS' feature. Simply copy this array of scss colours, customise the colours and done! There is no background colour for the 'Other' type purpose, it defaults to ```light-grey: #f8f9fa```.

{{< highlight scss >}}
$activity-icon-colors: (
    "administration": #5d63f6,
    "assessment": #eb00a2,
    "collaboration": #f7634d,
    "communication": #11a676,
    "content": #399be2,
    "interface": #a378ff
);
{{</ highlight >}}

## Examples

<div class="media mb-3">
    <div class="activityiconcontainer administration mr-3">
        {{< image "quiz/icon.svg" "Admin icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">admin</div>
        <div class="activityname"><a href="#">Administration module</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer assessment mr-3">
        {{< image "quiz/icon.svg" "Quiz icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">quiz</div>
        <div class="activityname"><a href="#">Assessment module</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer collaboration mr-3">
        {{< image "wiki/icon.svg" "Wiki icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">wiki</div>
        <div class="activityname"><a href="#">Collaboration module</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer collaboration mr-3">
        {{< image "choice/icon.svg" "Choice icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">choice</div>
        <div class="activityname"><a href="#">Learner type</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer content mr-3">
        {{< image "lesson/icon.svg" "Choice icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">lesson</div>
        <div class="activityname"><a href="#">Content module</a></div>
    </div>
</div>

<div class="media mb-3">
    <div class="activityiconcontainer interface mr-3">
        {{< image "quiz/icon.svg" "Interface icon" "activityicon">}}    </div>
    <div class="media-body align-self-center">
        <div class="text-uppercase small">interface</div>
        <div class="activityname"><a href="#">Interface module</a></div>
    </div>
</div>
