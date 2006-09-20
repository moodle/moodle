2006/09/08
----------
Google Summer of Code is finished, spent a couple of weeks away from
the project to think about it and also to take a break. Working on it
now I discovered bugs in the query parser (now fixed), and I also
un-convoluted the querylib logic (well slighlty).

Updated ZFS files to latest SVN.

2006/08/21
----------
Fixed index document count, and created new config variable to store
the size. (Search now has 3 global vars in $CFG, date, size and complete,
see indexer.php for var names). Index size is cached to provide an always
current value for the index - this is to take into account the fact that
deleted documents are in fact not removed from the index, but instead just
marked as deleted and not returned in search results. The actual document
still features in the index, and skews sizes. When the index optimiser is
completed in ZFS, then these deleted documents will be pruned, thus
correctly modifying the index size.

Additional commenting added.

Query page logic very slightly modified to clean up GET string a bit (removed
'p' variable).

Add/delete functions added to other document types.

A few TODO fields added to source, indicating changes still to come (or at
least to be considered).

2006/08/16
----------
Add/delete/update cron functions finished - can be called seperately
or all at once via cron.php.

Document date field added to index and database summary.

Some index db functionality abstracted out to indexlib.php - can
use IndexDBControl class to add/del documents from database, and
to make sure the db table is functioning.

DB sql files changed to add some extra fields.

Default 'simple' query modified to search title and author, as well
as contents of document, to provide better results for users.

2006/08/14
----------
First revision of the advanced search page completed. Functional,
but needs a date search field still.

2006/08/02
----------
Added resource search type, and the ability to specify custom 'virtual'
models to search - allowing for non-module specific information to be
indexed. Specify the extra search types to use in lib.php.

2006/07/28
----------
Added delete logic to documents; the moodle database log is checked
and any found delete events are used to remove the referenced documents
from the database table and search index.

Added database table name constant to lib.php, must change files using
the static table name.

Changed documents to use 'docid' instead of 'id' to reference the moodle
instance id, since Zend Search adds it's own internal 'id' field. Noticed
this whilst working on deletions.

Added some additional fields to the permissions checking method, must still
implement it though.

2006/07/25
----------
Query logic moved into the SearchQuery class in querylib.php. Should be able
to include this file in any page and run a query against the index (PHP 5
checks must be added to those pages then, though).

Index info can be retrieved using IndexInfo class in indexlib.php.

Abstracted some stuff away, to reduce rendundancy and decrease the
likelihood of errors. Improved the stats.php page to include some
diagnostics for adminstrators.

delete.php skeleton created for removing deleted documents from the
index. cron.php will contain the logic for running delete.php,
update.php and eventually add.php.

2006/07/11
----------
(Warning: It took me 1900 seconds to index the forum, go make coffee
whilst you wait.) [Moodle.org forum data]

Forum search functions changed to use 'get_recordset' instead of
'get_records', for speed reasons. This provides a significant improvement,
but indexing is still slow - getting data from the database and Zend's
tokenising _seem_ to be the prime suspects at the moment.

/search/tests/ added - index.php can be used to see which modules are
ready to be included in the search index, and it informs you of any
errors - should be a prerequisite for indexing.

Search result pagination added to query.php, will default to 20 until
an admin page for the search module is written.

2006/07/07
----------
Search-enabling functions moved out've the mod's lib.php files and into
/search/documents/mod_document.php - this requires the search module to
operate without requiring modification of lib files.

SearchDocument base class improved, and the way module documents extend
it. A custom-data field has been added to allow modules to add any custom
data they wish to be stored in the index - this field is serialised into
the index as a binary field.

Database field 'type' renamed to 'doctype' to match the renaming in the
index, 'type' seems to be a reserved word in Lucene. Several index field
names change to be more descriptive (cid -> course_id). URLs are now
stored in the index, and don't have to be generated on the fly during
display of query results.

2006/07/05
------
Started cleaning and standardising things.

cvs v1.1
--------
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
   email: cynnical@gmail.com
   skype: mchampan
   Summer of Code 2006