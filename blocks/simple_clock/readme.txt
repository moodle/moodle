To install:
 1. Copy the simple_clock folder to the blocks folder.
 2. Visit the Notifications page (Site Administration -> Notifications)
 3. Check that the SERVER* timezone is set correctly in php/php.ini (restart
    server after changes)
 4. Check that the LOCAL* timezone is set correctly in Site Administration ->
    Location -> Location Settings (or use Server's local time)

The block can then be added from the Blocks list when Editing is turned on.

*If the instance is hosted on a server in a different timezone, the SERVER
timezone should be the timezone of the physical server and the LOCAL timezone
should be the timezone where the Moodle instance is used.
