`sqlauth:SQL`
=============

This is a authentication module for authenticating a user against a SQL database.


Options
-------

`dsn`
:   The DSN which should be used to connect to the database server.
    Check the various database drivers in the [PHP documentation](http://php.net/manual/en/pdo.drivers.php) for a description of the various DSN formats.

`username`
:   The username which should be used when connecting to the database server.


`password`
:   The password which should be used when connecting to the database server.

`query`
:   The SQL query which should be used to retrieve the user.
    The parameters :username and :password are available.
    If the username/password is incorrect, the query should return no rows.
    The name of the columns in resultset will be used as attribute names.
    If the query returns multiple rows, they will be merged into the attributes.
    Duplicate values and NULL values will be removed.


Examples
--------

Database layout used in some of the examples:

    CREATE TABLE users (
      uid VARCHAR(30) NOT NULL PRIMARY KEY,
      password TEXT NOT NULL,
      salt TEXT NOT NULL,
      givenName TEXT NOT NULL,
      email TEXT NOT NULL,
      eduPersonPrincipalName TEXT NOT NULL
    );
    CREATE TABLE usergroups (
      uid VARCHAR(30) NOT NULL REFERENCES users (uid) ON DELETE CASCADE ON UPDATE CASCADE,
      groupname VARCHAR(30) NOT NULL,
      UNIQUE(uid, groupname)
    );

Example query - SHA256 of salt + password, with the salt stored in an independent column, MySQL server:

    SELECT uid, givenName, email, eduPersonPrincipalName
    FROM users
    WHERE uid = :username
    AND PASSWORD = SHA2(
        CONCAT(
            (SELECT salt FROM users WHERE uid = :username),
            :password
        ),
        256
    )

Example query - SHA256 of salt + password, with the salt stored in an independent column. Multiple groups, MySQL server:

    SELECT users.uid, givenName, email, eduPersonPrincipalName, groupname AS groups
    FROM users LEFT JOIN usergroups ON users.uid = usergroups.username
    WHERE users.uid = :username
    AND PASSWORD = SHA2(
        CONCAT(
            (SELECT salt FROM users WHERE uid = :username),
            :password
        ),
        256
    )

Example query - SHA512 of salt + password, stored as salt (32 bytes) + sha256(salt + password) in password-field, PostgreSQL server:

    SELECT uid, givenName, email, eduPersonPrincipalName
    FROM users
    WHERE username = :username
    AND SUBSTRING(
        password FROM LENGTH(password) - 31
    ) = SHA2(
        CONCAT(
            SUBSTRING(password FROM 1 FOR LENGTH(password) - 32),
            :password
        ),
        512
    )

Security considerations
-----------------------

Please never store passwords in plaintext in a database. You should always hash your passwords with a secure one-way
function like the ones in the SHA2 family. Use randomly generated salts with a length at least equal to the hash of the
password itself. Salts should be per-password, that meaning every time a password changes, the salt must change, and
therefore salts must be stored in the database alongside the passwords they were used for. Application-wide salts can
be used (by just concatenating them to the input of the hash function), but should never replace per-password salts,
used instead as an additional security measure.

One way hashing algorithms like MD5 or SHA1 are considered insecure and should therefore be avoided.
