---
layout: docs
title: "Component Library Backend"
date: 2021-05-27T15:43:07+01:00
group: moodle-components
draft: false
---

## Creating new Pages

Pages for the component library are written in [Markdown](https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet) and a number of [Hugo](https://gohugo.io) powered tools.

To add a page simply create a Markdown file in content/moodle/[foldername]/

## Generating the HTML versions of the Markdown files

The HTML pages for this library are created in the docs/ folder of the componentlibrary

Please run `npm install` in the $moodleroot folder to fetch all requirements for contributing to this library.

Once all requirements are installed you all you need to do is run `grunt componentlibrary` to create the component library pages.

## Location of Markdown files

```
└── content
    ├─ bootstrap
    └─ moodle
```

## HTML output folder

```
├── docs
```

## Page setup using the Hugo static site builder

The hugo config file can be found in /admin/tool/componentlibrary/config.yml

The HTML and CSS for the component library pages are found here:

```
└─── hugo
    ├── archetypes
    ├── dist
    ├── scss
    └─- site
```

`archetypes` are template markdown files used when creating a new hugo page.

`dist` CSS and JavaScript for use in Hugo pages

`scss` The SCSS used to generate the docs css for the Component Library.

`site` The page templates for hugo that include the left hand menu, navbar etc. And the templates to render example code shown in this component library.


## Creating a new docs page

Find the example page in `/admin/tool/componentlibrary/content/moodle/components/example.md` and use it as a template for your new
page. In the top part of the example.md file you will find the `frontmatter` configuration between the `---` characters. This part is used when hugo generates the page name and description. Change it to describe the new page you are creating.

The document setup is not fixed at all, the example page is just there for inspiration when starting to describe a Moodle component.
