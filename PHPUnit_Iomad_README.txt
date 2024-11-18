In order to run PHPUnit tests you need to use the custom list of tests for Iomad.
Some standard tests will fail in an Iomad installation. These have been replaced
with special Iomad versions. 

vendor/bin/phpunit -c iomad_phpunit.xml
