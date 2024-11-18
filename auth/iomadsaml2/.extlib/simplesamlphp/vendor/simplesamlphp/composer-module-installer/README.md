SimpleSAMLphp Composer module installer
=======================================

This package is a Composer plugin that allows a SimpleSAMLphp module to be
installed through Composer. Installation can be as easy as executing:

```
composer.phar require vendor/simplesamlphp-module-mymodule 1.*
```

That command would install `vendor/simplesamlphp-module-mymodule` matching
version `1.*`.


Making a module installable through Composer
--------------------------------------------

To make a module installable through Composer, you need to add a
`composer.json`-file to the root of the module. It should look
something like:

```json
{
    "name": "vendor/simplesamlphp-module-mymodule",
    "description": "A description of the module 'mymodule'.",
    "type": "simplesamlphp-module",
    "require": {
        "simplesamlphp/composer-module-installer": "~1.0"
    }
}
```

The package name must be on the form:

```
<vendor>/simplesamlphp-module-<module name>
```

`<vendor>` is the vendor name you use, and `<module name>` is the name
of your module. Your module will be installed in the `modules/<module name>`
directory in the SimpleSAMLphp installation directory.


Installing your custom module
-----------------------------

If you publish your module on [Packagist](https://packagist.org/), no special
configuration is required to install your module. However, if your module is
hosted in a private repository, you need to add a repository for the module to
your SimpleSAMLphp `composer.json` file. For example, if your module is located
in a Git repository in `/home/username/mymodule`, you would add something like
the following to `repositories` in `composer.json`:

```json
{
    "type": "vcs",
    "url": "/home/username/mymodule"
}
```

The `repositories array may look something like:

```json
"repositories": [
    {
        "type": "package",
        "package": {
            "name": "robrichards/xmlseclibs",
            "version": "1.3.1",
            "source": {
                "type": "svn",
                "url": "http://xmlseclibs.googlecode.com/svn",
                "reference": "trunk@50"
            },
            "autoload": {
                "files": ["xmlseclibs.php"]
            }
        }
    },
    {
        "type": "vcs",
        "url": "/home/username/mymodule"
    }
]
```

Once you have added the repository, you should be able to install your module
by executing:

```
composer.phar require vendor/simplesamlphp-module-mymodule:dev-master
```

(`dev-master` instructs Composer to install the `master`-branch from the Git
repository.)

See the [Composer Repository documentation](https://getcomposer.org/doc/05-repositories.md)
for more information about adding your own custom repositories to Composer.


Module names that contain uppercase letters
-------------------------------------------

New modules should only have lowercase letters in the module name, however a
lot of existing module names contain uppercase letters. Since Composer package
names should only contain lowercase letters, a mixed-case variant of the module
name can be provided in the `ssp-mixedcase-module-name` extra data option:

```json
{
    "name": "vendor/simplesamlphp-module-mymodule",
    "description": "A description of the module 'MyModule'.",
    "type": "simplesamlphp-module",
    "extra": {
        "ssp-mixedcase-module-name": "myModule"
    },
    "require": {
        "simplesamlphp/composer-module-installer": "~1.1"
    }
}
```

Note that this is only meant for migration of existing modules. New modules
should only use lowercase letters in the name.
