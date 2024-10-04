scssphp
-------

Downloaded from: https://github.com/scssphp/scssphp

Import procedure:

- Delete everything from this directory except readme_moodle.txt (this file).
- Copy all the files from the folder 'src' to this directory.
- Copy the license file from the project root.
- Review the local changes defined below, if any. Reapply
  them if needed. If already available upstream, please remove
  them from the list.

Licensed under MIT, Copyright (c) 2015 Leaf Corcoran.

Local changes:

- Apply local changes to ensure that all nullable method parameters are correctly type-hinted.
  These can be detected using:
  phpcs --sniffs=PHPCompatibility.FunctionDeclarations.RemovedImplicitlyNullableParam lib/scssphp
