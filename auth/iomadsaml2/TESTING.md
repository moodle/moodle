There are a variety of scenarions which are somewhat difficult to
test. In future these may be automated:

1) Test using 1 IdP (SSP) with dual off eg:

http://moodle.local/login/index.php


2) Test using mulitple IdP (SSP) with a choice of IdP eg:

https://samltest.id/start-sp-test/

http://moodle.local/auth/iomadsaml2/login.php?wants&idp=c4b9265e38e3107bee1ccdf9d6475676&passive=off


3) Test Single logout starting from the SP

http://moodle.local/login/logout.php?sesskey=ihwmEywPxu


4) Test Single logout starting from the IdP. Notice that `ReturnTo` URL domain should be in `trusted.url.domains` in IdP config.
If that is not the case, try using `ReturnTo=http://idp.local/simplesaml` which should work as SimpleSAMLphp trusts self hostname by default.

http://idp.local/simplesaml/iomadsaml2/idp/SingleLogoutService.php?ReturnTo=http://moodle.local/

5) Test IdP initiation login

http://idp.local/simplesaml/iomadsaml2/idp/SSOService.php?spentityid=http://moodle.local/auth/iomadsaml2/sp/metadata.php

6) Test IdP init login when the IdP is NOT the default IdP

http://idp.local/simplesaml/iomadsaml2/idp/SSOService.php?spentityid=http://moodle.local/auth/iomadsaml2/sp/metadata.php


