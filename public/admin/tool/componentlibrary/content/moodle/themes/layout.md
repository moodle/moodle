---
layout: docs
title: "Layout"
description: "Moodle page layouts for themes"
date: 2019-12-10T13:53:41+01:00
draft: false
---

## High level templates

Theme layouts are the highest level templates found in Moodle. They construct the Moodle Page users see when interacting with Moodle.

Layout files define the location of page elements like the primary and secondary navigation, the main content, Moodle blocks and the footer.

Theme layouts are defined in a themes config.php and themes can serve different layout files depending on the page type, example layouts are

* frontpage
* course page
* activity page
* secure page
* login page

## Accessibility considerations

When interacting with a theme the layout needs to be constructed with a logical DOM order: First render the primary navigation, then the secondary navigation then the page content, then the footer.

## Responsiveness

Use [Bootstrap grids]({{< docsref "/layout/grid" >}}) to create a responsive design. Make sure the primary and secondary navigation can be found easily on a mobile device.

Always try themes on all different theme layouts and ensure font-sizes, paddings and margins are used correctly. Especially for frequently used pages like courses and activities.
