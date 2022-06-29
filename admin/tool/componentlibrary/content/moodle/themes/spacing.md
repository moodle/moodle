---
layout: docs
title: "Spacing"
description: Moodle spacing
date: 2020-02-04T09:40:32+01:00
draft: false
weight: 4
---

## How it works

Moodle's spacing classes build on Bootstrap spacing classes which can be set for margins and paddings on different screen breakpoints. Using these classes is preferred over setting custom spacing on UI elements using CSS.

### example class pt-3

class: ```pt-3:```

result: padding-top-three

css:
{{< highlight css >}}
.pt-3 {
  padding-top: 1rem; /** 16px **/
}
{{< /highlight >}}

The classes are named using the format `{property}{sides}-{size}` for `xs` and `{property}{sides}-{breakpoint}-{size}` for `sm`, `md`, `lg`, and `xl`.

### Moodle spacing values

Moodle add's a 6th spacing value on top of the Bootstrap default spacing.

* `0` - for classes that eliminate the `margin` or `padding` by setting it to `0`
* `1` - (by default) for classes that set the `margin` or `padding` to `$spacer * .25`
* `2` - (by default) for classes that set the `margin` or `padding` to `$spacer * .5`
* `3` - (by default) for classes that set the `margin` or `padding` to `$spacer`
* `4` - (by default) for classes that set the `margin` or `padding` to `$spacer * 1.5`
* `5` - (by default) for classes that set the `margin` or `padding` to `$spacer * 2`
* `6` - (by default) for classes that set the `margin` or `padding` to `$spacer * 3`
* `auto` - for classes that set the `margin` to auto

### Example of paddings

{{< example>}}
<div class="d-flex align-items-center justify-content-center">
  <div class="p-6 bg-dark">
    <div class="p-5 bg-white">
      <div class="p-4 bg-info">
        <div class="p-3 bg-success">
          <div class="p-2 bg-warning">
            <div class="p-1 bg-danger">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
{{< /example >}}
