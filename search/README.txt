This directoery contains the central implementation of
Moodle's Global Search Engine.

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

The search engine is now part of Moodle core distribution.

Some extra libraries might be added for converting physical documents to text
so it can be indexed. Moodle CVS (entry contrib/patches/global_search_libraries)
provides packs for antiword and xpdf GPL libraries the search engine is ready for 
shockwave indexing, but will not provide Adobe Search converters that should be 
obtained at http://www.adobe.com/licensing/developer/

1. Go to the block administration panel and setup once the Global Search
block. This will initialize useful parameters for the global search engine.

2. Insert a new Global Search block somewhere in a course or top-level screen. 

3. Launch an empty search (you must be administrator).

4. Go to the statistics screen.

5. Activate indexation (indexersplash.php). Beware, if your Moodle has
a large amount of content, indexing process may be VERY LONG.

To search, go back to the search block and try a query.

Handled information for indexing
################################

In the actual state, the engine indexes the following information:

- assignment descriptions
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
- chat sessions
- lesson pages

Some third party plugins are also searchable using the new Search API implementation

- Techproject

Extensions
##########

The reviewed search engine API allows: 

- indexing of blocks contents
- indexation of modules or blocks containing a complex information model
- securing the access to the results
- adding indexing handling for additional modules and plugins adding a php calibrated script
- adding physical filetype handling adding a php calibrated script

Global Search on NFS Mounted clusters
#####################################

This version contains a patched Lucene Zend implementation that allows using the Global Search engine in an NFS mounted shared volume for Web clustering. This implementation 
remains highly experimental and not all tests have been processed. Some changes may
occur in the SoftLockManager that was added to the Lucene engine.

Future extensions
#################

- Should be added more information to index such as forum and glossary attachements, 
  so will other standard module contents.
- extending the search capability to a mnet network information space by aggregating remote search responses.