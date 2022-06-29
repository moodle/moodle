---
layout: docs
title: "Links"
description: "Designing links that lead to a change in context"
date: 2021-10-03T20:00:00+08:00
draft: false
---
## Links that open in a new window

Links that open in a new window or tab should indicate that they open in a new window or tab. This is especially helpful for screen reader users to let them know that clicking on the link will open a new window or tab. This helps eliminate confusion caused by changes in context such as opening a web page in a new window or tab.

There are several techniques that we can use when creating links that open in a new window.

### Using an image icon with alt text to indicate that the link opens in a new window

{{< example >}}
<a href="https://moodle.org" target="_blank">
    Moodle.org <i class="fa fa-external-link" role="img" aria-label="Opens in new window" title="Opens in new window"></i>
</a>
{{< /example  >}}

The core/userfeedback_footer_link is also good example for this one.

{{< mustache template="core/userfeedback_footer_link" >}}
{{< /mustache >}}

### Text that indicates that the link opens in a new window

This may be the most straightforward way of indicating that links open in a new window. The potential downside of this approach is that it can be quite distracting, especially when the page has a lot of links that open in a new window.

{{< example >}}
<a href="https://moodle.org" target="_blank">
    Moodle.org (Opens in new window)
</a>
{{< /example  >}}

## Links to a file

A link to a file such as a PDF file or Word document will usually open the relevant application associated to the file type and will cause a change in context. It would be helpful to let users know when a link will open a file.

### File type indicated in the link text
{{< example >}}
<a href="https://www.w3.org/WAI/WCAG20/versions/guidelines/wcag20-guidelines-20081211-a4.pdf">
    PDF version of the Web Content Accessibility Guidelines (WCAG) 2.0
</a>
{{< /example  >}}

### File type indicated via an icon/image's alt text
{{< example >}}
<a href="https://www.w3.org/WAI/WCAG20/versions/guidelines/wcag20-guidelines-20081211-a4.pdf">
    Web Content Accessibility Guidelines (WCAG) 2.0 <i class="fa fa-file-pdf-o" role="img" aria-label="PDF document" title="PDF document"></i>
</a>
{{< /example  >}}

## Links that lead to an external site

Similar to links that open in a new window, links that redirect to an external site may also have an indication that they lead to an external site to let users know that clicking on the link will take them away from Moodle.

The example link below opens in the same browser window and lets the user know that they will be redirected to Moodle.org, which is an external site.

{{< example >}}
<a href="https://moodle.org">
    Moodle.org <i class="fa fa-external-link" role="img" aria-label="Link leads to external site" title="Link leads to external site"></i>
</a>
{{< /example  >}}

## More information

* [Understanding Success Criterion 3.2.5](https://www.w3.org/TR/2016/NOTE-UNDERSTANDING-WCAG20-20161007/consistent-behavior-no-extreme-changes-context.html)
* [Opening new windows and tabs from a link only when necessary](https://www.w3.org/TR/WCAG20-TECHS/G200.html)
