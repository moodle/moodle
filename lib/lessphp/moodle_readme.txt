less.php
--------

Tag downloaded: v1.7.0.10
Downloaded from: https://github.com/oyejorge/less.php

All the files from the folder lib/Less are copied in
this directory. Only exception made for the directory
'.easymin' which is not included.

Also copy the license file from the project root.

Licensed under the Apache license 2.0.

Modifications:
* MDL-63422 - Verify that https://github.com/oyejorge/less.php/pull/367 has been applied to the
    imported version or apply it locally. PHP 7.3 compatibility.
* MDL-62294 - Cherry-picked upstream commit to fix PHP 7.2 compatibility when counting ruleset rules.
    https://github.com/oyejorge/less.php/commit/669acc51817a8da162b5f1b7137e79f0e4acc636
    TODO: Remove this note when this library gets upgraded to the latest release that already includes this fix.
