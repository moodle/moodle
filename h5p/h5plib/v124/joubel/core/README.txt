This folder contains the general H5P library. The files within this folder are not specific to any framework.

Any interaction with an LMS, CMS or other frameworks is done through interfaces. Platforms need to implement
the H5PFrameworkInterface(in h5p.classes.php) and also do the following:

 - Provide a form for uploading H5P packages.
 - Place the uploaded H5P packages in a temporary directory
 +++

See existing implementations for details. For instance the Drupal H5P module located at drupal.org/project/h5p

We will make available documentation and tutorials for creating platform integrations in the future.

The H5P PHP library is GPL licensed due to GPL code being used for purifying HTML provided by authors.
