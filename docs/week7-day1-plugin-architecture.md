# Moodle Plugin Architecture Overview

## 1. Plugin Types & Directory Structure
Moodle is structured around a modular plugin architecture where nearly every feature (activities, blocks, authentication plugins, filters, themes) resides in its own dedicated subdirectory under the root directory:
- /mod/: Activity modules (e.g., quiz, assign, forum) that students interact with inside courses.
- /blocks/: Side-panel UI blocks (e.g., calendar, recent activity) rendered across pages.
- /local/: Local custom plugins created for institution-specific modifications.
- /theme/: Visual design and layout templates.

## 2. Database Abstraction & XMLDB Schema
- Moodle uses a database abstraction layer (via PHP's PDO/Moodle database drivers) supporting MySQL, PostgreSQL, MariaDB, and MSSQL.
- Database tables are defined and versioned via XML schema files located at db/install.xml.
- Each plugin requires a version.php file defining the component name, plugin release version, and required Moodle core version to manage upgrades safely.

## 3. Hooks & Event System
- Moodle utilizes an event-driven observer model (db/events.php) allowing plugins to hook into core lifecycle events (e.g., core event course_viewed, user login, grade updates).
- Observers process asynchronous or synchronous reactions to system triggers without modifying core files.
