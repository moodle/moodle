---
layout: docs
title: "Keyboard access"
description: "Guidelines for create keyboard accessible User interfaces"
date: 2020-02-04T09:40:32+01:00
draft: false
tags:
 - MDL-64494
 - MDL-67874
 - 3.9
---

## Keyboard navigation

Keyboard accessibility is one of the most important aspects of web accessibility. Many users with motor disabilities rely on a keyboard. Blind users also typically use a keyboard for navigation. Some people have tremors which don't allow for fine muscle control. Others have little or no use of their hands. Some people simply do not have hands, whether due to a birth defect, an accident, or amputation. In addition to traditional keyboards, some users may use modified keyboards or other hardware that mimics the functionality of a keyboard. For more info visit the [Keyboard Accessibility](https://webaim.org/techniques/keyboard/) page on WebAIM.

## Moodle focus outline

A keyboard user typically uses the Tab key to navigate through interactive elements on a web page—links, buttons, fields for inputting text, etc. When an item has keyboard "focus", it can be activated or manipulated with the keyboard. A sighted keyboard user must be provided with a visual indicator of the element that currently has keyboard focus.

The focus outline contrast must meet the WCAG colour contrast guidelines, to ensure focus visibility of buttons the standard Bootstrap button focus colours are used. For links the focus colour was changed in [MDL-67874](https://tracker.moodle.org/browse/MDL-67874)

## Link focus colours
The focus outlines colours in Moodle have been made more accessible in [MDL-67874](https://tracker.moodle.org/browse/MDL-67874). Typically your OS or browser has default values for the focus outline colours. In some cases the colour contrast of these outlines is not enough so the outline colours for links has changed to a darker shade.

## Example focus outlines
{{< example show_markup="false">}}
<div id="focusexamples">
    <p> Normal buttons</p>
    <div>
        <span class="me-2"><button class="btn btn-primary">Primary</button></span>
        <span class="me-2"><button class="btn btn-secondary">Secondary</button></span>
        <span class="me-2"><button class="btn btn-danger">Danger</button></span>
        <span class="me-2"><button class="btn btn-outline-secondary">Outline</button></span>
        <span class=""><a href="#" class="aalink">clickable link</a></span>
    </div>
    <p  class="mt-4"> keyboard focus</p>
    <div>
        <span class="me-2"><button class="focusloop btn btn-primary">Primary</button></span>
        <span class="me-2"><button class="focusloop btn btn-secondary">Secondary</button></span>
        <span class="me-2"><button class="focusloop btn btn-danger">Danger</button></span>
        <span class="me-2"><button class="focusloop btn btn-outline-secondary">Outline</button></span>
        <span class=""><a href="#" class="focusloop aalink">clickable link</a></span>
    </div>
    <button id="showfocus" class="btn btn-success btn-large mt-5" type="button">Show focus</button>
</div>
{{#js}}
var exampleContainer = document.querySelector('#focusexamples');

document.getElementById("showfocus").addEventListener('click', function(){
    var elements = exampleContainer.querySelectorAll('.focusloop');
    elements.forEach(function(item) {
        item.focus();
        item.classList.add('focus');
    });
});

exampleContainer.querySelectorAll('.aalink').forEach(function (link) {
    window.console.log(link);
    link.addEventListener('click', function (e) {
        e.preventDefault();
    });
});
{{/js}}
{{< /example >}}

## Tab focus order.
The focus order when navigating a Moodle page needs to be logical.
An example of logical focus order would be starting at the main menu before then following through to the main content and finally the footer. To achieve this the navdrawer menu has been relocate in the DOM to be positioned right after the navdrawer in [MDL-67863](https://tracker.moodle.org/browse/MDL-67863).

Testing with a keyboard is essential when evaluating the accessibility of your compoment, You should never be able to tab to hidden items and there should always be a visual que to your current location in the page.
