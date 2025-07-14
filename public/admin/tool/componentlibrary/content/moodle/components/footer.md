---
layout: docs
title: "Footer"
description: Moodle footer HTML and required code injected in the page footer
date: 2020-03-02T16:32:24+01:00
draft: false
weight: 40
tags:
- available
- needs review
---

## How are footers implemented in Moodle?

The page footer is shown at the bottom of every page in Moodle, typically a Moodle footer contains links to:

* A link to the Moodle docs for the current page
* resetting user tours
* user login information
* a link to the Moodle homepage

When the setting ```perfdebug``` is turned on (Site Administration > Development > Debugging) additional performance info is shown in the page footer.

## Footer styling

Since footers are repeated on all pages they are styled distinctly using a dark background colour and light fonts. Links need to be underlined and use light fonts too.

The page footer should always stick to the bottom of the page and never overlap any page content.

### Minimal footer requirements

The page footer is required to inject the page JavaScript, show the reset user tours link and show user info and (if avaliable) the contents of the user configure theme custom menu.

{{< example >}}
<footer id="page-footer" class="py-3 bg-dark text-light">
    <div class="container">
        <p class="helplink"><a href="#">Moodle docs for this page</a></p>
        You are logged in as <a href="#">Test User</a> (<a href="#">logout</a>)
        <a href="#">Home</a>
        <div class="tool_usertours-resettourcontainer"></div>
    </div>
</footer>
{{< /example >}}
