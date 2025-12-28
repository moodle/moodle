# Moodle

<p align="center"><a href="https://moodle.org" target="_blank" title="Moodle Website">
  <img src="https://raw.githubusercontent.com/moodle/moodle/main/.github/moodlelogo.svg" alt="The Moodle Logo">
</a></p>

[Moodle][1] is the World's Open Source Learning Platform, widely used around the world by countless universities, schools, companies, and all manner of organisations and individuals.

Moodle is designed to allow educators, administrators and learners to create personalised learning environments with a single robust, secure and integrated system.

## Quick Start

If you want to set up a local development instance of Moodle quickly, follow these steps:

1. **Prerequisites**: Ensure you have PHP 7.4+ (or 8.x), a database (MySQL/PostgreSQL/MariaDB), and a web server (Apache/Nginx) installed. You may also need Composer and Node.js for development.

2. **Clone the repository**:
   ```bash
   git clone https://github.com/moodle/moodle.git
   cd moodle
   ```

3. **Prepare the web root**: Point your web server's document root to the `moodle` directory (or create a symbolic link). Alternatively, you can use PHP's built-in web server for testing:
   ```bash
   php -S localhost:8000
   ```

4. **Run the installation script**: Open your browser and navigate to `http://localhost:8000`. You will be guided through the installation process, where you'll configure the database and create an administrator account.

5. **Set up cron**: Moodle requires a cron job for scheduled tasks. You can set up a cron entry that calls `admin/cron.php` every minute, or use the "web cron" option during installation.

For more detailed instructions, including Docker setups and production deployments, please refer to the [full installation guide][3].

## Documentation

- Read our [User documentation][3]
- Discover our [developer documentation][5]
- Take a look at our [demo site][4]

## Community

[moodle.org][1] is the central hub for the Moodle Community, with spaces for educators, administrators and developers to meet and work together.

You may also be interested in:

- attending a [Moodle Moot][6]
- our regular series of [developer meetings][7]
- the [Moodle User Association][8]

## Installation and hosting

Moodle is Free, and Open Source software. You can easily [download Moodle][9] and run it on your own web server, however you may prefer to work with one of our experienced [Moodle Partners][10].

Moodle also offers hosting through both [MoodleCloud][11], and our [partner network][10].

## License

Moodle is provided freely as open source software, under version 3 of the GNU General Public License. For more information on our license see

[1]: https://moodle.org
[2]: https://moodle.com
[3]: https://docs.moodle.org/
[4]: https://sandbox.moodledemo.net/
[5]: https://moodledev.io
[6]: https://moodle.com/events/mootglobal/
[7]: https://moodledev.io/general/community/meetings
[8]: https://moodleassociation.org/
[9]: https://download.moodle.org
[10]: https://moodle.com/partners
[11]: https://moodle.com/cloud
[12]: https://moodledev.io/general/license