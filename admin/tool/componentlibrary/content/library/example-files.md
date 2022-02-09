---
layout: docs
title: "Example files"
date: 2020-01-21T13:00:00+08:00
draft: false
---

There may be times that we need to provide example implementations of the component being documented in order to further demonstrate how to use a component.

We need to put these files in `examples` folders. It helps organise things and makes the maintenance of the component library code easier by allowing us to distinguish examples from the actual component library code.


## Example pages

Example pages can be placed under the `componentlibrary/examples` folder.

See the `formfields.php` example page as an example:

```
componentlibrary
└── examples
     └── formfields.php
```

This can be embedded in the component's documentation page via iframe. For example in `componentlibrary/content/moodle/form-elements.md`:

```
<iframe src="../../../../examples/formfields.php" style="overflow:hidden;height:4000px;width:100%;border:0" title="Moodle form fields"></iframe>
```

## Example classes

Example classes can be placed under the `componentlibrary/classes/local/examples/[componentname]` folder.

In our form fields example, we have the `\tool_componentlibrary\local\examples\formelements\example` class under `componentlibrary/classes/local/examples/formelements`.

## Example templates

Example templates can be placed under the `componentlibrary/templates/examples/[componentname]` folder.

In our form fields example, we have the `tool_componentlibrary/examples/formelements/toggles` template under `componentlibrary/templates/examples/formelements`.

## Summary

Please put example files in their designated `examples` folders.
```
componentlibrary
└── classes
    └── local
        └── examples
            └── [component folder]
                └── [example classes]
└── examples
    └── [example page]
└── templates
    └── examples
        └── [component folder]
            └── [example templates]
```

For the form elements documentation, its example files are in the following `examples` folders.
```
componentlibrary
└── classes
    └── local
        └── examples
            └── formelements
                └── example.php
└── examples
    └── formfields.php
└── templates
    └── examples
        └── formelements
            └── toggles.mustache
```
