# Installing with the Git repository
If you're here, it's probably because you want to improve the plugin ❤️. Firstly you'll need to fork the Github repository so that you have your own copy of it. You can fork the repository by clicking the "Fork" button to the right of the repository's name.

Once you've forked the repository, open your terminal and navigate to the root directory of your Moodle installation. In that directory you need to change to the directory where the plugin needs to be installed which is the `admin/tool/log/store` directory. If you've already installed the plugin before with the zip file, there will be an `xapi` folder in this directory and you need to delete it before we move on.

Once it's deleted, make sure you have [Git installed](https://git-scm.com/) and then clone your fork of this repository using `git clone git@github.com:YOUR_GITHUB_USERNAME/moodle-logstore_xapi.git xapi`, then move into the `xapi` directory that's created for you by the clone.

With the repository cloned, you can now install the plugin's dependencies by running `php -r "readfile('https://getcomposer.org/installer');" | php; rm -rf vendor; php composer.phar install --prefer-source`. Finally you'll need to [configure and enable the plugin](enable-the-plugin.md).

Hopefully this all made sense, but if you do have any issues ❗️ or questions ❓, please create a new issue on [our Github issue tracker](https://github.com/xAPI-vle/moodle-logstore_xapi/issues). You may also find [Github's guide on forking repositories](https://guides.github.com/activities/forking/) useful.
