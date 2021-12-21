---
layout: docs
title: "Syntax highlighting"
description: Use Hugo shortcodes for Syntax highlighting.
date: 2020-02-04T09:40:32+01:00
draft: false
weight: 3
---

## Using Shortcodes for syntax

Using this (shortcode) syntax you can higlight programming syntax in the component library

## HTML highlighting

Syntax for markdown (.md) files:

```
{{</* highlight html */>}}
<div class="myclass">
  This is an example.
</div>
{{</* /highlight  */>}}
```
Rendered result on this page:

{{< highlight html >}}
<div class="myclass">
  This is an example.
</div>
{{< /highlight >}}

## PHP shortcode

Syntax for markdown (.md) files:

```
{{</* php */>}}
  $mform->addElement('passwordunmask', 'password', get_string('label'), $attributes);
{{</* /php  */>}}
```
Rendered result on this page:

{{< php >}}
  $mform->addElement('passwordunmask', 'password', get_string('label'), $attributes);
{{< /php >}}

## Highlight shortcode

Syntax for markdown (.md) files:

```
{{</* highlight js */>}}
var config = {
    test: null,
    selector: '[data-drag-type=move]'
};
{{</* /highlight  */>}}
```
Rendered result on this page:

{{< highlight js >}}
var config = {
    test: null,
    selector: '[data-drag-type=move]'
};
{{< /highlight >}}


## Example shortcode

The example shortcode shows the HTML source with syntax highlighting and renders it on the page. This shortcode takes the following arguments:

show_markup="true/false"
show_preview="true/false"




```
{{</* example */>}}
<div class="input-group">
  <input type="text" class="form-control" placeholder="Search">
  <div class="input-group-append">
    <button class="btn btn-primary" type="button">
        <i class="fa fa-search"></i>
    </button>
  </div>
</div>
{{#js}}
    window.console.log('hello');
{{/js}}
{{</* /example  */>}}
```

{{< example >}}
<div class="input-group">
  <input type="text" class="form-control" placeholder="Search">
  <div class="input-group-append">
    <button class="btn btn-primary" type="button">
        <i class="fa fa-search"></i>
    </button>
  </div>
</div>
{{< /example >}}
