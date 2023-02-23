---
layout: docs
title: "Dynamic tabs"
date: 2021-10-02T09:40:32+01:00
draft: false
tags:
- MDL-71943
- 4.0
---

## How it works

Dynamic tabs are tabs that load content in AJAX requests. Once the user clicks on the tab heading, the page does not reload but the content of the respective tab loads in AJAX request.

## Source files

* `lib/amd/src/dynamic_tabs.js`
* `lib/amd/src/local/repository/dynamic_tabs.js`
* `lib/classes/external/dynamic_tabs_get_content.php`
* `lib/classes/output/dynamic_tabs.php`
* `lib/classes/output/dynamic_tabs/base.php`
* `lib/db/services.php`
* `lib/templates/dynamic_tabs.mustache`

## How to use dynamic tabs

First of all we need to create a tab class file for each tab that we need, extending `lib/classes/output/dynamic_tabs/base.php`.

These tab classes need to include these 4 methods in order to work:

* `export_for_template` returns the data we export to the template
* `get_tab_label` returns the tab title
* `is_available` checks the tab permission and returns true/false that will enable/disable the individual tab
* `get_template` returns the path to the tab template file

{{< php >}}
class tab1 extends base {

    /**
     * Export this for use in a mustache template context.
     *
     * @param renderer_base $output
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $content = (object)[];
        $content->customtext = 'Tab 1 content example';
        return $content;
    }

    /**
     * The label to be displayed on the tab
     *
     * @return string
     */
    public function get_tab_label(): string {
        return 'Tab 1';
    }

    /**
     * Check permission of the current user to access this tab
     *
     * @return bool
     */
    public function is_available(): bool {
        // Define the correct permissions here.
        return true;
    }

    /**
     * Template to use to display tab contents
     *
     * @return string
     */
    public function get_template(): string {
        return 'tool_componentlibrary/dynamictabs_tab1';
    }
}
{{< / php >}}

Then we need to create the templates that each `get_template` method will call.

Finally, to add dynamic tabs to our page, we just need to call all the previously created tabs, and pass the attributes
needed in each tab.

These attributes will be stored as "data attributes" in the DOM and can be also used inside our tab classes
using `get_data` method (for example to check permissions in `is_available`).

{{< php >}}
    $tabs = [
        new tab1(['demotab' => 'Tab1', 'reportid' => $reportid]),
        new tab2(['demotab' => 'Tab2']),
    ];
    echo $OUTPUT->render_from_template('core/dynamic_tabs', (new dynamic_tabs($tabs))->export_for_template($OUTPUT));
{{< / php >}}

## Example

<iframe src="../../../../examples/dynamictabs.php" style="overflow:hidden;height:400px;width:100%;border:0" title="Moodle dynamic tabs"></iframe>
