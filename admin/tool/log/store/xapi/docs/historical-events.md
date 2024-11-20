# Guide to Historical Events
Hey there 👋  so you want to process some events from the past 🕓, probably a time when this plugin wasn't installed on your Moodle. That's awesome and we really want to help with that. Unfortunately, we've [not built this into the plugin natively yet](https://github.com/xAPI-vle/moodle-logstore_xapi/issues/42), but we do have a recommended process for inserting these events.

Our process leverages the plugin's existing "background mode" (which is enabled by default in the plugin settings). In background mode, the plugin receives events from Moodle as they happen and stores them into the "mdl_logstore_xapi_log" table to be processed later by the [Moodle Cron](https://docs.moodle.org/35/en/Cron) (which you should have setup already on your server). When the Moodle Cron script is executed, the plugin extracts, transforms, and loads the events from the "mdl_logstore_xapi_log" table into the Learning Record Store and then deletes the events from the "mdl_logstore_xapi_log" table.

To leverage this for events that happened before the plugin was installed, you'll need to move events from the "mdl_logstore_standard_log" table into the "mdl_logstore_xapi_log" table. This can be done by running an SQL query like the one below.

```SQL
INSERT INTO mdl_logstore_xapi_log
SELECT * FROM mdl_logstore_standard_log
```

Note that you may want to add a `WHERE` clause to this SQL query if you only want to process some past events. For example, if you only wanted to process past events between `2018-01-01T00:00Z` and `2018-01-02T00:00Z` you would use the `WHERE` clause below, where the dates have been [converted to Unix timestamp integers](http://www.4webhelp.net/us/timestamp.php?action=date&day=01&month=01&year=2018&hour=00&minute=00&second=00&timezone=0). 

```sql
WHERE timecreated BETWEEN 1514764800 AND 1514851200
```

Once this SQL query has been executed, the plugin will start processing the events each time the Moodle Cron script is executed. Note that you can [run the Moodle Cron script manually](https://docs.moodle.org/20/en/Cron#Testing_cron_and_manual_trigger) if you want to and you can change the "Maximum batch size" in the plugin settings (which defaults to 30) in order to speed things up.

If you have a quick question about this process, please ask in [our Gitter chat room](https://gitter.im/LearningLocker/learninglocker) 💬, if you think there's a problem with this guide, please create a new issue in [our Github issue tracker](https://github.com/xAPI-vle/moodle-logstore_xapi/issues).
