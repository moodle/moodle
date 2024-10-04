---
layout: docs
title: "Example"
description: "This is an example page describing the example component"
date: 2020-01-28T10:13:29+01:00
draft: true
weight: 30
---

## How it works

The EXAMPLE is used to [...] . It can be found in Moodle on pages where [...]

## Example

Show what the example looks like in Moodle, if your component includes and JavaScript backend please describe how it is initiated.

{{< example >}}

<div class="example w-25 border border-secondary p-3">
    <button class="btn btn-primary btn-block" id="clickme">
        Click me
        <span id="waiting" class="spinner-grow-sm" role="status" aria-hidden="true"></span>
    </button>
</div>

{{#js}}
require(['jquery'], function($) {
    $('#clickme').on('click', function() {
        $('#waiting').toggleClass('spinner-grow');
    });
});
{{/js}}
{{< /example >}}

## Example explained

How can you use this example?
Are there any example pages in Moodle?
What are the different options you have for different contexts (places in Moodle)?
What colours can be used?
Can it be called from PHP and JavaScript

## JavaScript behavior

Showcase the different ways this buttons can behave:

Does it trigger any events, does it listen to events?
Does it require any core AMD modules?
Is there a webservice backend required?

## Accessibility

Descripbe the `aria-something` parts of the element. Are there any possible accessibility issues using this example?
What are the considerations for keyboard navigation?
What accessibile colors can be used?
How to test its accessibility?
