---
layout: docs
title: "Icons"
description: "Moodle icons are rendered with fontawesome or as small images"
weight: 40
date: 2020-02-04T09:40:32+01:00
draft: false
tags:
- available
---

## Description

Most Moodle icons are rendered using the 4.7 verions of [Fontawesome](https://fontawesome.com/v4.7.0/). Iconnames are mapped from the Moodle icon name to the fontawesome icon names in `/lib/classes/output/icon_system_fontawesome.php`

If needed a theme can override this map and provide its own mapping.

## Rendering icons in Mustache Templates

Icons can be rendered in moodle templates using this notation:

```{{#pix}} i/edit, core {{/pix}}```

## Rendering icons in Php

Use the pix_icon method to retreive the HTML for an icon.

{{< php >}}
    $icon = $OUTPUT->pix_icon('i/edit', 'Edit me', 'moodle');
{{< / php >}}

Options:

## Stacking fontawesome icons

{{< example >}}
<span class="fa-stack fa-lg">
  <i class="fa fa-comment fa-stack-2x"></i>
  <i class="fa fa-thumbs-o-up fa-stack-1x fa-inverse"></i>
</span>
{{< /example >}}
## List of mapped font-awesome icons

The top title of each cards displayse the name of the icon. The icon shown left is the font-awesome icons. The icon shown on the right is the old image base icon.

{{< moodleicons >}}
