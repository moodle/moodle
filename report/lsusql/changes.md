# Change log for the LSU Report API (Formerly Ad-hoc database queries)

## Changes in LSU 1.0

* This version supports limiting individual reports to their own subset of users.
* This version supports exporting unescaped data.

## Changes in 4.2

* This version works with Moodle 4.0.
* There is now an option to fetch query results via webservice/pluginfile.php, once the permissions are set up right.
* Better navigation when there are a lot of reports. There is now a separate page for each category,
  as well as the main index page.
* When editing queries, the input box for the report name is bigger.
* Actions (e.g. edit) for reports are now shown on the parameters page, and are shown before the list
  of preview report runs, under scheduled reports, which is more convenient. 
* Fixed a bug with the display of validation messages on the edit form.
* Added a missing index in the database.


## Changes in 4.1

* We now track when each query was created and last modified, along with the user who modified it.
  (With the necessary Privacy API bits.)
* Fix the sort-order of reports on the index page.
* Fix the bug with list of email recipients being saved wrongly.
* Fix a bug when the value in a cell ended in a backslash.
* Minor styling fix.
* Updates to make everything work with Moodle 3.11.


## Changes in 4.0

* Fix downloading for Moodle 3.9+.
* When editing queries, the SQL field is now styled more prettily.
* For scheduled reports, the list of users to email is now stored as a list of userids, rather than usernames.
  (Configuration of existing reports is automatically updated on upgrade.)


## Changes in 3.9

* Scheduled reports which accumulate one row at a time now display
  with the most recent data at the top.
* When sending emails for scheduled reports, the number of rows in
  the results is now added to the subject line.


## Changes in 3.8

* Report results can now be downloaded in any of data format that Moodle supports, not just CSV.
* Admins can control the maximum possible limit for the number of rows a query can return.
* Admins can now set which day is considered the first of the week for weekly reports.
  This defaults to the Moodle setting for this for new installs. For existing installs,
  it stays the same as before (Saturday) but you can change it.


## Changes in 3.7

* If a report has query parameters, they can now be set in the URL
  (GET parameters) so you can bookmark a particular variant of a report.
* There is a parameter you can add to the URL of a report, so it is
  displayed with 'embedded' page layout, suitable for showing in an iframe.
* Also, change the page layout used in the normal case to 'report'.
* Query param values that are integers are now sent to the database as
  integers, which can lead to queries being optimised better.
* Fix an issue with showing the results of scheduled reports, when the
  report only produces data sometimes.
* Improve handling of 'pretty' column names.
* Fix compatibility with the messaging API changes in Moodle 3.6.
* Improve message default settings on install.
* Move hard-coded English string 'Run report' to the language file.


## Changes in 3.6

* New feature for columns that are links. If the SQL query returns two
  columns `name` and `name_link_url` (for any value of `name`) then
  the that will be shown as a single column of links, where the visible
  link text comes from the `name` column, and the URL comes from the
  `name_link_url` column.
* The exact case of the column titles is extracted from the query. So,
  if your query is `SELECT 'x' AS My_HTML`, previously the table would
  display the column heading as 'my html'. Now it will show 'My HTML'.
* A summary of the number of rows output under the table.
* Moodle 3.3 compatibility re-established thanks to Paul Holden.
* Behat tests fixed for Moodle 3.6. 


## Changes in 3.5

* Privacy API implementation.
* Fix escaping of values in reports that contain HTML special characters.
* Fix bug where the report name was missing from scheduled task emails.
* Fix some coding style issues.
* Due to privacy API support, this version now only works in Moodle 3.4+
  For older Moodles, you will need to use a previous version of this plugin.


## 3.4 and before

Changes were not documented here.
