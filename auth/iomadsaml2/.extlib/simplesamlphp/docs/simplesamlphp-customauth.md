Implementing custom username/password authentication
====================================================

This is a step-by-step guide for creating a custom username/password [authentication source](./simplesamlphp-authsource) for SimpleSAMLphp.
An authentication source is responsible for authenticating the user, typically by getting a username and password, and looking it up in some sort of database.

<!-- {{TOC}} -->

Create a custom module
----------------------

All custom code for SimpleSAMLphp should be contained in a [module](./simplesamlphp-modules).
This ensures that you can upgrade your SimpleSAMLphp installation without overwriting your own code.
In this example, we will call the module `mymodule`.
It will be located under `modules/mymodule`.

First we need to create the module directory:

    cd modules
    mkdir mymodule

Since this is a custom module, it should always be enabled.
Therefore we create a `default-enable` file in the module.
We do that by copying the `default-enable` file from the `core` module.

    cd mymodule
    cp ../core/default-enable .

Now that we have our own module, we can move on to creating an authentication source.


Creating a basic authentication source
--------------------------------------

Authentication sources are implemented using PHP classes.
We are going to create an authentication source named `mymodule:MyAuth`.
It will be implemented in the file `modules/mymodule/lib/Auth/Source/MyAuth.php`.

To begin with, we will create a very simple authentication source, where the username and password is hardcoded into the source code.
Create the file `modules/mymodule/lib/Auth/Source/MyAuth.php` with the following contents:

    <?php
    namespace SimpleSAML\Module\mymodule\Auth\Source;

    class MyAuth extends \SimpleSAML\Module\core\Auth\UserPassBase {
        protected function login($username, $password) {
            if ($username !== 'theusername' || $password !== 'thepassword') {
                throw new \SimpleSAML\Error\Error('WRONGUSERPASS');
            }
            return [
                'uid' => ['theusername'],
                'displayName' => ['Some Random User'],
                'eduPersonAffiliation' => ['member', 'employee'],
            ];
        }
    }

Some things to note:

  - The classname is `\SimpleSAML\Module\mymodule\Auth\Source\MyAuth`.
    This tells SimpleSAMLphp to look for the class in `modules/mymodule/lib/Auth/Source/MyAuth.php`.

  - Our authentication source subclasses `\SimpleSAML\Module\core\Auth\UserPassBase`.
    This is a helper-class that implements much of the common code needed for username/password authentication.

  - The `login` function receives the username and password the user enters.
    It is expected to authenticate the user.
    If the username or password is correct, it must return a set of attributes for the user.
    Otherwise, it must throw the `\SimpleSAML\Error\Error('WRONGUSERPASS');` exception.

  - Attributes are returned as an associative array of `name => values` pairs.
    All attributes can have multiple values, so the values are always stored in an array.


Configuring our authentication source
-------------------------------------

Before we can test our authentication source, we must add an entry for it in `config/authsources.php`.
`config/authsources.php` contains an list of enabled authentication sources.

The entry looks like this:

    'myauthinstance' => [
        'mymodule:MyAuth',
    ],

You can add it to the beginning of the list, so that the file looks something like this:

    <?php
    $config = [
        'myauthinstance' => [
            'mymodule:MyAuth',
        ],
        /* Other authentication sources follow. */
    ];

`myauthinstance` is the name of this instance of the authentication source.
(You are allowed to have multiple instances of an authentication source with different configuration.)
The instance name is used to refer to this authentication source in other configuration files.

The first element of the configuration of the authentication source must be `'mymodule:MyAuth'`.
This tells SimpleSAMLphp to look for the `\SimpleSAML\Module\mymodule\Auth\Source\MyAuth` class.


Testing our authentication source
---------------------------------

Now that we have configured the authentication source, we can test it by accessing "authentication"-page of the SimpleSAMLphp web interface.
By default, the web interface can be found on `http://yourhostname.com/simplesaml/`.
(Obviously, "yourhostname.com" should be replaced with your real hostname.)

Then select the "Authentication"-tab, and choose "Test configured authentication sources".
You should then receive a list of authentication sources from `config/authsources.php`.
Select `myauthinstance`, and log in using "theusername" as the username, and "thepassword" as the password.
You should then arrive on a page listing the attributes we return from the `login` function.

Next, you should log out by following the log out link.


Using our authentication source in an IdP
-----------------------------------------

To use our new authentication source in an IdP we just need to update the IdP configuration to use it.
Open `metadata/saml20-idp-hosted.php`.
In that file you should locate the `auth`-option for your IdP, and change it to `myauthinstance`:

    <?php
    /* ... */
    $metadata['__DYNAMIC:1__'] = [
        /* ... */
        /*
         * Authentication source to use. Must be one that is configured in
         * 'config/authsources.php'.
         */
        'auth' => 'myauthinstance',
        /* ... */
    ];

You can then test logging in to the IdP.
If you have logged in previously, you may need to log out first.


Adding configuration to our authentication source
-------------------------------------------------

Instead of hardcoding options in our authentication source, they should be configurable.
We are now going to extend our authentication source to allow us to configure the username and password in `config/authsources.php`.

First, we need to define the properties in the class that should hold our configuration:

    private $username;
    private $password;

Next, we create a constructor for the class.
The constructor is responsible for parsing the configuration and storing it in the properties.

    public function __construct($info, $config) {
        parent::__construct($info, $config);
        if (!is_string($config['username'])) {
            throw new Exception('Missing or invalid username option in config.');
        }
        $this->username = $config['username'];
        if (!is_string($config['password'])) {
            throw new Exception('Missing or invalid password option in config.');
        }
        $this->password = $config['password'];
    }

We can then use the properties in the `login` function.
The complete class file should look like this:

    <?php
    class MyAuth extends \SimpleSAML\Module\core\Auth\UserPassBase {

        private $username;
        private $password;

        public function __construct($info, $config) {
            parent::__construct($info, $config);
            if (!is_string($config['username'])) {
                throw new Exception('Missing or invalid username option in config.');
            }
            $this->username = $config['username'];
            if (!is_string($config['password'])) {
                throw new Exception('Missing or invalid password option in config.');
            }
            $this->password = $config['password'];
        }

        protected function login($username, $password) {
            if ($username !== $this->username || $password !== $this->password) {
                throw new \SimpleSAML\Error\Error('WRONGUSERPASS');
            }
            return [
                'uid' => [$this->username],
                'displayName' => ['Some Random User'],
                'eduPersonAffiliation' => ['member', 'employee'],
            ];
        }

    }

We can then update our entry in `config/authsources.php` with the configuration options:

    'myauthinstance' => [
        'mymodule:MyAuth',
        'username' => 'theconfigusername',
        'password' => 'theconfigpassword',
    ],

Next, you should go to the "Test configured authentication sources" page again, and test logging in.
Note that we have updated the username & password to "theconfigusername" and "theconfigpassword".
(You may need to log out first before you can log in again.)


A more complete example - custom database authentication
--------------------------------------------------------

The [sqlauth:SQL](./sqlauth:sql) authentication source can do simple authentication against SQL databases.
However, in some cases it cannot be used, for example because the database layout is too complex, or because the password validation routines cannot be implemented in SQL.
What follows is an example of an authentication source that fetches an user from a database, and validates the password using a custom function.

This code assumes that the database contains a table that looks like this:

    CREATE TABLE userdb (
        username VARCHAR(32) PRIMARY KEY NOT NULL,
        password_hash VARCHAR(64) NOT NULL,
        full_name TEXT NOT NULL);

An example user (with password "secret"):

    INSERT INTO userdb (username, password_hash, full_name)
        VALUES('exampleuser', 'QwVYkvlrAMsXIgULyQ/pDDwDI3dF2aJD4XeVxg==', 'Example User');

In this example, the `password_hash` contains a base64 encoded SSHA password.
A SSHA password is created like this:

    $password = 'secret';
    $numSalt = 8; /* Number of bytes with salt. */
    $salt = '';
    for ($i = 0; $i < $numSalt; $i++) {
        $salt .= chr(mt_rand(0, 255));
    }
    $digest = sha1($password . $salt, TRUE);
    $password_hash = base64_encode($digest . $salt);

The class follows:

    <?php
    class MyAuth extends \SimpleSAML\Module\core\Auth\UserPassBase {

        /* The database DSN.
         * See the documentation for the various database drivers for information about the syntax:
         *     http://www.php.net/manual/en/pdo.drivers.php
         */
        private $dsn;

        /* The database username, password & options. */
        private $username;
        private $password;
        private $options;

        public function __construct($info, $config) {
            parent::__construct($info, $config);

            if (!is_string($config['dsn'])) {
                throw new Exception('Missing or invalid dsn option in config.');
            }
            $this->dsn = $config['dsn'];
            if (!is_string($config['username'])) {
                throw new Exception('Missing or invalid username option in config.');
            }
            $this->username = $config['username'];
            if (!is_string($config['password'])) {
                throw new Exception('Missing or invalid password option in config.');
            }
            $this->password = $config['password'];
            if (isset($config['options']) {
                if (!is_array($config['options])) {
                    throw new Exception('Missing or invalid options option in config.');
                }
                $this->options = $config['options'];
            }
        }

        /**
         * A helper function for validating a password hash.
         *
         * In this example we check a SSHA-password, where the database
         * contains a base64 encoded byte string, where the first 20 bytes
         * from the byte string is the SHA1 sum, and the remaining bytes is
         * the salt.
         */
        private function checkPassword($passwordHash, $password) {
            $passwordHash = base64_decode($passwordHash);
            $digest = substr($passwordHash, 0, 20);
            $salt = substr($passwordHash, 20);

            $checkDigest = sha1($password . $salt, TRUE);
            return $digest === $checkDigest;
        }

        protected function login($username, $password) {

            /* Connect to the database. */
            $db = new PDO($this->dsn, $this->username, $this->password, $this->options);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            /* Ensure that we are operating with UTF-8 encoding.
             * This command is for MySQL. Other databases may need different commands.
             */
            $db->exec("SET NAMES 'utf8'");

            /* With PDO we use prepared statements. This saves us from having to escape
             * the username in the database query.
             */
            $st = $db->prepare('SELECT username, password_hash, full_name FROM userdb WHERE username=:username');

            if (!$st->execute(['username' => $username])) {
                throw new Exception('Failed to query database for user.');
            }

            /* Retrieve the row from the database. */
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                /* User not found. */
                SimpleSAML\Logger::warning('MyAuth: Could not find user ' . var_export($username, TRUE) . '.');
                throw new \SimpleSAML\Error\Error('WRONGUSERPASS');
            }

            /* Check the password. */
            if (!$this->checkPassword($row['password_hash'], $password)) {
                /* Invalid password. */
                SimpleSAML\Logger::warning('MyAuth: Wrong password for user ' . var_export($username, TRUE) . '.');
                throw new \SimpleSAML\Error\Error('WRONGUSERPASS');
            }

            /* Create the attribute array of the user. */
            $attributes = [
                'uid' => [$username],
                'displayName' => [$row['full_name']],
                'eduPersonAffiliation' => ['member', 'employee'],
            ];

            /* Return the attributes. */
            return $attributes;
        }

    }

And configured in `config/authsources.php`:

    'myauthinstance' => [
        'mymodule:MyAuth',
        'dsn' => 'mysql:host=sql.example.org;dbname=userdatabase',
        'username' => 'db_username',
        'password' => 'secret_db_password',
    ],

