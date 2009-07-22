This partial distribution contains a complete review of the
Global Search Engine of Moodle.

The Global Search Engine stores indexes about a huge quantity  
of information from within modules, block or resources stored 
by Moodle either in the database or the file system.

The administrator initialy indexes the existing content. Once this 
first initialization performed, the search engine maintains indexes
regularily, adding new entries, deleting obsolete one or updating
some that have changed.

Search will produce links for acceding the information in a similar
context as usually accessed, from the current user point of view.
Results filtering removes from results any link to information the
current user would not be allowed to acces on a straight situation.

Deployment
###########

For setting the engine : 


* File copy

1. Add to Moodle's library both additional libraries provided in the distribution
2. Replace the "search" directory with the new one
3. Replace the "blocks/search" with the new one.

* Logical install

4. Browse to the administrative notification screen and let the 
install/update process run. The install process creates the Moodle
table needed for backing the indexed documents identities.

5. Go to the block administration panel and setup once the Global Search
block. This will initialize useful parameters for the global search engine.

6. Insert a new Global Search block somewhere in a course or top-level screen. 

7. Launch an empty search (you must be administrator).

8. Go to the statistics screen.

9. Activate indexation (indexersplash.php). Beware, if your Moodle has
a large amount of content, indexing process may be VERY LONG.

To search, go back to the search block and try a query.

Handled information for indexing
################################

In the actual state, the engine indexes the following information:

- forum posts
- database records (using textual fields only)
- database comments
- glossary entries
- glossary comments on entries
- Moodle native resources
- physical MSWord files as resources (.doc)
- physical Powerpoint files as resources (.ppt)
- physical PDF files as resources 
- physical text files as resources (.txt)
- physical html files as resources (.htm and .html)
- physical xml files as resources (.xml)
- wiki pages
- techproject descriptions
- char sessions
- lesson pages

Extensions
##########

The reviewed search engine API allows: 

- indexing of blocks contents
- indexation of modules or blocks containing a complex information model
- securing the access to the results
- adding indexing handling adding a php calibrated script
- adding physical filetype handling adding a php calibrated script

Future extensions
#################

- Should be added more information to index such as forum and glossary attachements, so will other standard module contents.

- extending the search capability to a mnet network information space.

 

