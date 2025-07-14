---
layout: docs
title: "Emojipicker"
date: 2020-02-04T09:40:32+01:00
draft: false
tags:
- MDL-65896
- 3.8
---

## How it works

The emoji picker is used in the Moodle messaging interface and in Atto. It allows you to select an emoji which then is returned for use in your input element.

## Source files

* `lib/amd/src/emoji/picker.js`
* `lib/templates/emoji/picker.mustache`

## Examples

{{< example >}}
<div class="row">
    <div data-region="emojipickertest" class="col-md-9">
    </div>
<div class="col-md-3">
  <h4>Emoji picker result:<h4>
  <div data-region="emojivalue" style="font-size: 3rem"></div>
</div>

{{#js}}
require(
[
    'jquery',
    'core/templates',
    'core/emoji/picker'
],
function(
    $,
    templates,
    emojiPicker
) {
  var emojiCallback = function(emoji) {
    $('[data-region="emojivalue"]').html(emoji);
  }

    var testArea = $('[data-region="emojipickertest"]');
    templates.render('core/emoji/picker', {}).done(function(html, js) {
        templates.replaceNodeContents(testArea, html, js);
          emojiPicker(testArea[0], emojiCallback);
    });

});
{{/js}}
{{< /example >}}

## Usage

Fetch / render the core template ```core/emoji/picker.mustache``` and load the ```core/emoji/picker.js```. Render the template and run the js on the new domnode.

{{< highlight js >}}
emojiPicker(domNode, callback);
{{< /highlight >}}
