This is the initial release (prototype) of Moodle's new search module -
so basically watch out for sharp edges.

The structure has not been finalised, but this is what is working at the
moment, when I start looking at other content to index, it will most likely
change. I don't recommend trying to make your own content modules indexable,
at least not until the whole flow is finalised. I will be implementing the
functions needed to index all of the default content modules on Moodle, so
expect that around mid-August.

Wiki pages were my goal for this release, they can be indexed and searched,
but not updated or deleted at this stage (was waiting for ZF 0.14 actually).

I need to check the PostgreSQL sql file, I don't have a PG7 install lying
around to test on, so the script is untested.

To index for the first time, login as an admin user and browse to /search/index.php
or /search/stats.php - there will be a message and a link telling you to go index.

-- Michael Champanis (mchampan)
   cynnical@gmail.com
   Summer of Code 2006