Ad-hoc database queries
https://moodle.org/plugins/report_customsql

This report plugin allows Administrators to set up arbitrary database queries
to act as ad-hoc reports. Reports can be of two types, either run on demand,
or scheduled to run automatically.

Other users with the right capability can go in and see a list of queries that
they have access to. Results can be viewed on-screen or downloaded as CSV.

Reports can contain placeholders, in which case, the user running the report is
presented with a form where they can enter the values to substitute for the
placeholders before running the report.

Scheduled reports can also be set to be send out be email whenever they are
generated.

If a column has a name ending in 'date' and contains integer values, then they
will be assumed to be unix timestamps, and formatted as dates. If a query
placeholder has a name ending in 'date', then users will be give a date-time
selector to input the value of that parameter.

You can set a limit on the maximum number of rows returned by a query
(up to the hard limit of 5000).

Reports can be grouped into categories, which helps when you have a lot of them.

See http://docs.moodle.org/34/en/Custom_SQL_queries_report for more information.

Written by Tim Hunt and converted to Moodle 2.0 by Derek Woolhead, both from
The Open University (http://www.open.ac.uk/). There have also been contibutions
but many others, as you can see in the git log.

To install using git, type this command in the root of your Moodle install
    git clone git@github.com:moodleou/moodle-report_customsql.git report/customsql
    echo '/report/customsql/' >> .git/info/exclude

This version of the report is compatible with Moodle 3.2 or later.
