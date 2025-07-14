---
layout: docs
title: "Search input"
description: "find items in a collection of data"
date: 2020-08-26T16:32:24+01:00
draft: false
weight: 60
tags:
- Introduced in Moodle 3.10
- MDL-69454
---

## How it works

Single searches allow the user the quickly search a collection of data. A search is input in the search field and the results are shown immediately (using JS) or after the search has been posted to Moodle.

Searches are used in the page navbar, blocks, module pages, settings, the contentbank etc.

## Example

<div class="small">
Default search input.
</div>
{{< mustache template="core/search_input" >}}
    {
        "action": "https://moodle.local/admin/search.php",
        "extraclasses": "my-2",
        "inputname": "search",
        "inform": false,
        "searchstring": "Search something",
        "hiddenfields": [
            {
                "name": "context",
                "value": "11"
            }
        ]
    }
{{< /mustache >}}
<div class="mt-3 small">
Emphasized search input using .btn-primary
</div>
{{< mustache template="core/search_input" >}}
    {
        "action": "https://moodle.local/admin/search.php",
        "extraclasses": "my-2",
        "uniqid": "Unique string",
        "inputname": "search",
        "inform": false,
        "btnclass": "btn-primary",
        "searchstring": "Search something",
        "hiddenfields": [
            {
                "name": "context",
                "value": "11"
            }
        ]
    }
{{< /mustache >}}

**Use**

Use a default search input when searching is not expected to be the primary action on this page. Use the emphasized search when it is the default action AND the only search input found on the page. (except the navbar search).

**Variables**

* action: used to specify the form (get) action
* extraclasses: add these css classes to the search wrapper
* uniqid: Unique string
* inputname: form field name for search input
* inform: search is part of a larger form
* query: current search value from user input
* btnclass: use a btn class for the btn design, (btn-secondary, btn-primary)
* searchstring: string describing current search for placeholder and aria-label
* hiddenfields: array with name valua pairs for extra hidden form fields

<div class="my-5"></div>

### Auto search input with clear option

{{< mustache template="core/search_input_auto" >}}
    {
        "placeholder": "Search settings",
        "uniqid": "45"
    }
{{< /mustache >}}

**Use**

This search option is used when the search imput immediately triggers updating data displayed below the search, for example: a table of usernames. It includes a clear button that clears the input on typeing.

**Variables**

* uniqid: Unique string
* placeholder: search placeholder

<div class="my-5"></div>

### Navbar search form

{{< mustache template="core/search_input_navbar" >}}
    {
        "action": "https://moodle.local/admin/search.php",
        "inputname": "search",
        "searchstring": "Search",
        "hiddenfields": [
            {
                "name": "cmid",
                "value": "11"
            }
        ]
    }
{{< /mustache >}}

**Use**

This search should be used once on the page and triggers a global site search. It uses a minimal amount of space to prevent breakage on mobile use.

**Features**

* click search button to start typing
* click close button to hide search
* click enter after input to post the form
* uses very little space
* overlaps navbar when viewed on mobile.
