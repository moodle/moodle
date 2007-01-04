<?php  // $Id$
       // expired.php - called by hive when the session has expired.

    require('../../config.php');

    require('lib.php');

    require_login();

    //MW theres no easy way to log in seamlessly. We need the users unhashed password.
    // It's a security risk to carry that in $SESSION so we put up login form.
    print_header();
    notify('Your session has expired. Please log in again.'); 
?>
      <form action="login.php" method="post" id="login">
        <table border="0" align="center">
        <tr>
          <td width="80%">
            <table align="center" class="loginform">
              <tr class="username">
                <td align="right" class="c0">
                  <?php print_string("username") ?>:
                </td>
                <td class="c1">
                  <input type="text" name="username" size="15" value="<?php p($frm->username) ?>" alt="<?php print_string("username") ?>" />
                </td>
              </tr>
              <tr class="password">
                <td align="right" class="c0">
                  <?php print_string("password") ?>:
                </td>
                <td class="c1">
                  <input type="password" name="password" size="15" value="" alt="<?php print_string("password") ?>" />
                </td>
              </tr>
            </table>
          </td>
          <td width="20%">
            <input type="submit" value="<?php print_string("login") ?>" />
          </td>
        </tr>
        </table>
      </form>
<br />
<?php
    close_window_button();
?>
