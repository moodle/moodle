---
layout: docs
title: "Show more"
date: 2023-06-12T00:00:00+01:00
draft: false
weight: 70
tags:
- MDL-78204
- 4.3
---

## How to use

The show more component is used to show and hide content. It is useful for showing a preview of content and then allowing the user to expand it to see more.

The parameters for the template context are:
* collapsedcontent: The content to show when collapsed.
* expandedcontent: The content to show when expanded.
* extraclasses: Any extra classes added to the showmore outer container.
* buttonextraclasses: Any extra classes added to the button.
* collapsedextraclasses: Any extra classes added to the collapsed content container.
* expandedextraclasses: Any extra classes added to the expanded content container.

## Example

{{< mustache template="core/showmore" >}}
    {
        "collapsedcontent": "Hello...",
        "expandedcontent": "Hello<br>Is it me you're looking for? I can see it in your eyes",
        "extraclasses": "rounded p-2 border",
        "buttonextraclasses": "fw-bold"
    }
{{< /mustache >}}

## Example used as a template block

{{< mustache template="tool_componentlibrary/examples/showmore/example" >}}
    {
    }
{{< /mustache >}}
