---
layout: docs
title: "Adding images"
date: 2020-02-04T09:40:32+01:00
draft: false
weight: 2
---

## Images

Images that need to be show in the component library should be placed in a separate folder for each new page.

The Markdown file for this page is located here:

```
└── content
     └── moodle
         └── getting-started
            └── adding-images.md
```

To access images for this page create a new folder here.

```
└── static
     └── moodle
         └── getting-started
            └── adding-images
```

Place your images in this new folder:

```
└── static
     └── moodle
         └── getting-started
            └── adding-images
                ├── wildebeest-1200.jpg
                ├── kitten1.png
                └── kitten2.png
```

To use images use this syntax:

Syntax for markdown (.md) files:

```
{{</* image "wildebeest-1200.jpg" "Image of a Wildebeest" "img-fluid" */>}}
```

Rendered result on this page:

{{< image "wildebeest-1200.jpg" "Image of a Wildebeest" "img-fluid">}}
