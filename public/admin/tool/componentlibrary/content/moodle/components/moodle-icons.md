---
layout: docs
title: "Icons"
description: "Moodle icons are rendered with Font Awesome or as small images"
weight: 40
date: 2020-02-04T09:40:32+01:00
draft: false
tags:
- available
---

## Description

Most Moodle icons are rendered using the 6.7.2 versions of [Fontawesome](https://fontawesome.com/v6/search). Iconnames are mapped from the Moodle icon name to the Font Awesome icon names in `/lib/classes/output/icon_system_fontawesome.php`

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

## Stacking Font Awesome icons

{{< example >}}
<span class="fa-stack fa-lg">
  <i class="fa-solid fa-comment fa-stack-2x"></i>
  <i class="fa-solid fa-thumbs-up fa-stack-1x fa-inverse"></i>
</span>

<span class="fa-stack fa-2x">
    <i class="fa-solid fa-camera fa-stack-1x"></i>
    <i class="fa-solid fa-ban fa-stack-2x" style="color:Tomato"></i>
</span>

<span class="fa-stack fa-2x">
    <i class="fa-solid fa-square fa-stack-2x"></i>
    <i class="fa-solid fa-terminal fa-stack-1x fa-inverse"></i>
</span>

<span class="fa-stack fa-4x">
    <i class="fa-solid fa-square fa-stack-2x"></i>
    <i class="fa-solid fa-terminal fa-stack-1x fa-inverse"></i>
</span>
{{< /example >}}
## List of mapped Font Awesome icons

The top title of each cards displays the name of the icon. The icon shown left is the Font Awesome icons. The icon shown on the right is the old image base icon.

{{< moodleicons >}}
