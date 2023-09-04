Using the Twitter authentication source with SimpleSAMLphp
==========================================================

Remember to configure `authsources.php`, with both Consumer key and secret.

To get an API key and a secret, register the application at:

 * <http://twitter.com/oauth_clients>

Set the callback URL to be:

 * `http://sp.example.org/simplesaml/module.php/authtwitter/linkback.php`

Replace `sp.example.org` with your hostname.

## Testing authentication

On the SimpleSAMLphp frontpage, go to the *Authentication* tab, and use the link:

  * *Test configured authentication sources*

Then choose the *twitter* authentication source.

Expected behaviour would then be that you are sent to twitter, and asked to login:

![](http://clippings.erlang.no/ZZ2EE26BF6.jpg)

The first time a user uses your application to login, he/she is asked for consent:

![](http://clippings.erlang.no/ZZ6B18B5D9.jpg)

You will then be authenticated in SimpleSAMLphp and see an attribute set similar to this:

![](http://clippings.erlang.no/ZZ74A6835E.jpg)

