---
layout: docs
title: "Colour contrast"
description: "Colour contrast for Moodle UI components"
date: 2021-03-30T09:40:32+01:00
draft: false
tags:
 - 3.9
---

## Accessibile colours

In WCAG 2, contrast is a measure of the difference in perceived "luminance" or brightness between two colors (the phrase "color contrast" is never used). This brightness difference is expressed as a ratio ranging from 1:1 (e.g. white on white) to 21:1 (e.g., black on a white).

## Common colour contrast issues

Most contrast issue, where the ratio is below 4.5:1 in Moodle are found on pages that using a background colour with text on top. For example a striped table with light text. Contrast issues should be qualified as bugs and reported on the Moodle tracker.

Moodle's colour set is designed to provide high contrast and maximum brightness. If the Moodle colours are changed using a custom theme make sure you test the contrast of pages like the enrolment table, reports and Moodle activities.

To test colour contrast you can use the inbuild functionality of your browser, my favourite extension is the "WCAG Colour contrast checker" for Chrome.

## Tables with links

This table with links should show no colour contrast issues.

{{< example show_markup="false" >}}
<table class="flexible table table-striped table-hover generaltable generalbox">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Name</th>
      <th scope="col">Email</th>
      <th scope="col">Status</th>
    </tr>
  </thead>
  <tbody>
    <tr >
      <th scope="row"><input type="checkbox" class="m-1" value="" data-action="toggle" data-toggle="toggler" data-togglegroup="participants-table" data-toggle-selectall="Select all" data-toggle-deselectall="Deselect all"></th>
      <td><a href="#">Bas Brands</a></td>
      <td>bas@example.com</td>
      <td>
      </td>
    </tr>
    <tr>
      <th scope="row"><input type="checkbox" class="m-1" value="" data-action="toggle" data-toggle="toggler" data-togglegroup="participants-table" data-toggle-selectall="Select all" data-toggle-deselectall="Deselect all"></th>
      <td><a href="#">Chris Cross</a></td>
      <td>chris@example.com</td>
      <td>
        Active
        </td>
    </tr>
    <tr >
      <th scope="row"><input type="checkbox" class="m-1" value="" data-action="toggle" data-toggle="toggler" data-togglegroup="participants-table" data-toggle-selectall="Select all" data-toggle-deselectall="Deselect all"></th>
      <td>
        <a class="linktest-1" href="#">Irene Ipsum</a><br>
        </td>
      <td>irene@example.com</td>
      <td>@blab</td>
    </tr>
  </tbody>
</table>
{{< /example >}}
