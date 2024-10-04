---
layout: docs
title: "Adding pages"
date: 2020-03-02T10:13:29+01:00
draft: false
weight: 2
---

## Adding or editing a page in the component library

In this step-by-step guide you will create a new page called Breadcrumb navigation and compile it from a markdown text file to a HTML page in the component library.

To add a page to the component library on your local machine navigate open your editor or file manager and create a new markdown text file in folder /content/moodle/components/breadcrumbs.md


```
└── content
    └── moodle
       └── components
          └── breadcrumbs.md
```

Open the file in your favourite editor and start it with some metadata, we call this syntax [frontmatter](https://gohugo.io/content-management/front-matter/).

```
---
layout: docs
title: "Breadcrumb navigation"
date: 2020-03-02T10:13:29+01:00
draft: false
---
```

Make sure you add these characters `---` before and after your metadata. You can change the title and date to match your document.

## Run the component library Grunt task.

In your terminal run the command `npm install` and then `grunt componentlibrary` in your Moodle root folder. This will install all required resources and compile the componentlibrary pages. If you do not have npm installed on your system please visit [npmjs.com](https://www.npmjs.com/get-npm) to learn how to get a working setup.


The output should be similar to this:

```
Running "componentlibrary:docsBuild" task
Building sites …
                   | EN
+------------------+-----+
  Pages            | 113
  Paginator pages  |   0
  Non-page files   |  18
  Static files     |  18
  Processed images |   0
  Aliases          |   7
  Sitemaps         |   1
  Cleaned          |   0

Total in 913 ms
Running "componentlibrary:cssBuild" task
Rendering Complete, saving .css file...
Wrote CSS to /var/www/repositories/cl_master/moodle/admin/tool/componentlibrary/hugo/dist/css/docs.css
Wrote Source Map to /var/www/repositories/cl_master/moodle/admin/tool/componentlibrary/hugo/dist/css/docs.css.map
Running "componentlibrary:indexBuild" task

Done.

```

The grunt watch task will pick up changes in the componentlibrary Markdown files and Scss files. So run `grunt watch` if you want to edit the pages.
