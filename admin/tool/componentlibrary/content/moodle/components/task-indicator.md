---
layout: docs
title: "Task indicator"
description: "A progress indicator for background tasks"
date: 2024-08-21T00:00:00+01:00
draft: false
tags:
- MDL-81714
- 5.0
---

{{< mustache template="core/task_indicator" >}}
{{< /mustache >}}

## How to use

The task indicator component is used to display on any page the status and progress of an ad-hoc or scheduled task running in the
background. If a task is running that will update the content on the page, it can be displayed in place of the content to inform
the user that the current content is out-of-date, and will be updated when the task is complete.

## Source files

* lib/amd/src/task_indicator.js
* lib/classes/output/task_indicator.php
* lib/templates/task_indicator.mustache

## Usage

The task indicator can only be used to display the progress of a task if its class uses `core\task\stored_progress_task_trait`.

When the task is queued, you must call the `initialise_stored_progress()` method to store the progress record in a pending state,
for the indicator to display while the task is queued.

{{< php >}}
$task = new \core\task\mytask($id);
$taskid = \core\task\manager::queue_adhoc_task($task, true);
if ($taskid) {
    $task->set_id($taskid);
    $task->initialise_stored_progress();
}
{{< /php  >}}

When the task runs, it must start, progress and complete its stored progress bar.
See `core_course\task\regrade_final_grades` for a real-life example.

{{< php >}}

class mytask extends adhoc_task {
    use \core\task\stored_progress_task_trait;

    public function execute(): void {
        $this->start_stored_progress();
        $storedprogress = $this->get_progress();
        foreach ($this->get_records() as $record) {
            $this->process_record($record);
            $storedprogress->progress();
        }
        $storedprogress->end_progress();
    }
}

{{< /php  >}}

Any page that wishes to display the status of the task must create an instance of the task object with the same parameters,
and pass it to a `task_indicator`.

{{< php >}}

$task = new mytask($id);
$taskindicator = new \core\output\task_indicator(
    task: $task,
    heading: 'Task processing',
    message: get_string('recalculatinggradesadhoc', 'grades'),
    icon: new \core\output\pix_icon('i/grades', ''),
    redirecturl: $PAGE->url,
    extraclasses: ['mytask'],
);

{{< /php  >}}

If there is currently a queued instance of the task, `$taskindicator->has_task_record()` will return true. We can use this to
decide whether we display the indicator. See `grade/report/summary/index.php` for a real-life example.

{{< php >}}

if ($taskindicator->has_task_record()) {
    echo $OUTPUT->render($taskindicator);
}

{{< /php  >}}

When the task begins running and the progress is updated, the progress bar will automatically be displayed.

If the optional `redirecturl` parameter is set when creating the indicator, the page will automatically reload or redirect to
this URL when the progress bar completes.

While the task is still queued, admins will see a "Run now" button below the indicator. This is designed for convenience if
a user is blocked on a job and needs the task run immediately. It will run the specific instance of the task tracked by the
indicator.

{{< mustache template="core/task_indicator" >}}
{
    "heading": "Regrade in progress",
    "icon": {
        "attributes": [
            {"name": "src", "value": "/pix/i/timer.svg"},
            {"name": "alt", "value": ""}
        ]
    },
    "message": "Grades are being recalculated due to recent changes.",
    "progress": {
        "id": "progressbar_test",
        "message": "Task pending",
        "idnumber": "progressbar_test",
        "class": "stored-progress-bar stored-progress-notstarted",
        "width": "500",
        "value": "0"
    },
    "runurl": "http://example.com/runtask.php?id=1",
    "runlabel": "Run now"
}
{{< /mustache >}}
