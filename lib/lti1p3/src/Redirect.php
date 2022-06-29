<?php

namespace Packback\Lti1p3;

use Packback\Lti1p3\Interfaces\ICookie;

class Redirect
{
    private $location;
    private $referer_query;
    private static $CAN_302_COOKIE = 'LTI_302_Redirect';

    public function __construct(string $location, string $referer_query = null)
    {
        $this->location = $location;
        $this->referer_query = $referer_query;
    }

    public function doRedirect()
    {
        header('Location: '.$this->location, true, 302);
        exit;
    }

    public function doHybridRedirect(ICookie $cookie)
    {
        if (!empty($cookie->getCookie(self::$CAN_302_COOKIE))) {
            return $this->doRedirect();
        }
        $cookie->setCookie(self::$CAN_302_COOKIE, 'true');
        $this->doJsRedirect();
    }

    public function getRedirectUrl()
    {
        return $this->location;
    }

    public function doJsRedirect()
    {
        ?>
        <a id="try-again" target="_blank">If you are not automatically redirected, click here to continue</a>
        <script>

        document.getElementById('try-again').href=<?php
        if (empty($this->referer_query)) {
            echo 'window.location.href';
        } else {
            echo "window.location.origin + window.location.pathname + '?".$this->referer_query."'";
        } ?>;

        var canAccessCookies = function() {
            if (!navigator.cookieEnabled) {
                // We don't have access
                return false;
            }
            // Firefox returns true even if we don't actually have access
            try {
                if (!document.cookie || document.cookie == "" || document.cookie.indexOf('<?php echo self::$CAN_302_COOKIE; ?>') === -1) {
                    return false;
                }
            } catch (e) {
                return false;
            }
            return true;
        };

        if (canAccessCookies()) {
            // We have access, continue with redirect
            window.location = '<?php echo $this->location; ?>';
        } else {
            // We don't have access, reopen flow in a new window.
            var opened = window.open(document.getElementById('try-again').href, '_blank');
            if (opened) {
                document.getElementById('try-again').innerText = "New window opened, click to reopen";
            } else {
                document.getElementById('try-again').innerText = "Popup blocked, click to open in a new window";
            }
        }

        </script>
        <?php
    }
}
