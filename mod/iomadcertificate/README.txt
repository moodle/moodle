QUICK INSTALL
=============

There are two installation methods that are available. Follow one of these, then log into your Moodle site as an administrator and visit the notifications page to complete the install.

==================== MOST RECOMMENDED METHOD - Git ====================

If you do not have git installed, please see the below link. Please note, it is not necessary to set up the SSH Keys. This is only needed if you are going to create a repository of your own on github.com.

Information on installing git - http://help.github.com/set-up-git-redirect/

Once you have git installed, simply visit the Moodle mod directory and clone git://github.com/markn86/moodle-mod_certificate.git, remember to rename the folder to certificate if you do not specify this in the clone command

Eg. Linux command line would be as follow -

git clone git://github.com/markn86/moodle-mod_certificate.git certificate

Use git pull to update this repository periodically to ensure you have the latest version.

==================== Download the certificate module. ====================

Visit https://github.com/markn86/moodle-mod_certificate and download the zip, uncompress this zip and extract the folder. The folder will have a name similar to markn86-moodle-mod_certificate-c9fbadb, you MUST rename this to certificate. Place this folder in your mod folder in your Moodle directory.

nb. The reason this is not the recommended method is due to the fact you have to over-write the contents of this folder to apply any future updates to the certificate module. In the above method there is a simple command to update the files.
