<?php
$this->data['header'] = $this->t('{login:user_pass_header}');

if (strlen($this->data['username']) > 0) {
    $this->data['autofocus'] = 'password';
} else {
    $this->data['autofocus'] = 'username';
}
$this->includeAtTemplateBase('includes/header.php');

if ($this->data['errorcode'] !== null) {
?>
    <div style="border-left: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8; background: #f5f5f5">
        <img src="/<?php echo $this->data['baseurlpath']; ?>resources/icons/experience/gtk-dialog-error.48x48.png"
             class="float-l erroricon" style="margin: 15px" alt=""/>

        <h2><?php echo $this->t('{login:error_header}'); ?></h2>

        <p><strong>
        <?php
            echo htmlspecialchars(
                $this->t(
                    $this->data['errorcodes']['title'][$this->data['errorcode']],
                    $this->data['errorparams']
                )
            );
        ?>
        </strong></p>
        <p>
        <?php
            echo htmlspecialchars(
                $this->t(
                    $this->data['errorcodes']['descr'][$this->data['errorcode']],
                    $this->data['errorparams']
                )
            );
        ?>
        </p>
    </div>
<?php
}
?>
    <h2 style="break: both"><?php echo $this->t('{login:user_pass_header}'); ?></h2>

    <p class="logintext"><?php echo $this->t('{login:user_pass_text}'); ?></p>

    <form action="?" method="post" name="f">
        <table>
            <tr>
                <td rowspan="2" class="loginicon">
                    <img alt=""
                        src="/<?php echo $this->data['baseurlpath']; ?>resources/icons/experience/gtk-dialog-authentication.48x48.png" />
                </td>
                <td><label for="username"><?php echo $this->t('{login:username}'); ?></label></td>
                <td>
                    <input id="username" <?php echo ($this->data['forceUsername']) ? 'disabled="disabled"' : ''; ?>
                        type="text" name="username"<?php echo $this->data['forceUsername'] ? '' : ' autocomplete="username" tabindex="1"'; ?>
                        value="<?php echo htmlspecialchars($this->data['username']); ?>" />
                </td>
            <?php
            if ($this->data['rememberUsernameEnabled'] && !$this->data['forceUsername']) {
                // display the "remember my username" checkbox
            ?>
                <td id="regular_remember_username">
                    <input type="checkbox" id="remember_username" tabindex="4"
                           <?php echo ($this->data['rememberUsernameChecked']) ? 'checked="checked"' : ''; ?>
                           name="remember_username" value="Yes" />
                    <small><?php echo $this->t('{login:remember_username}'); ?></small>
                </td>
            <?php
            }
            ?>
            </tr>
            <?php
            if ($this->data['rememberUsernameEnabled'] && !$this->data['forceUsername']) {
                // display the "remember my username" checkbox
            ?>
            <tr id="mobile_remember_username">
                <td>&nbsp;</td>
                <td>
                    <input type="checkbox" id="remember_username" tabindex="4"
                        <?php echo ($this->data['rememberUsernameChecked']) ? 'checked="checked"' : ''; ?>
                        name="remember_username" value="Yes" />
                    <small><?php echo $this->t('{login:remember_username}'); ?></small>
                </td>
            </tr>
            <?php
            }
            ?>
            <tr>
                <td><label for="password"><?php echo $this->t('{login:password}'); ?></label></td>
                <td><input id="password" type="password" tabindex="2" name="password" autocomplete="current-password" /></td>
            <?php
            if ($this->data['rememberMeEnabled']) {
                // display the remember me checkbox (keep me logged in)
            ?>
                <td id="regular_remember_me">
                    <input type="checkbox" id="remember_me" tabindex="5"
                        <?php echo ($this->data['rememberMeChecked']) ? 'checked="checked"' : ''; ?>
                        name="remember_me" value="Yes" />
                    <small><?php echo $this->t('{login:remember_me}'); ?></small>
                </td>
            <?php
            }
            ?>
            </tr>
            <?php
            if ($this->data['rememberMeEnabled']) {
                // display the remember me checkbox (keep me logged in)
            ?>
            <tr>
                <td></td>
                <td id="mobile_remember_me">
                    <input type="checkbox" id="remember_me" tabindex="5"
                        <?php echo ($this->data['rememberMeChecked']) ? 'checked="checked"' : ''; ?>
                        name="remember_me" value="Yes" />
                    <small><?php echo $this->t('{login:remember_me}'); ?></small>
                </td>
            </tr>
            <?php
            }
            ?>
            <?php
            if (array_key_exists('organizations', $this->data)) {
            ?>
                <tr>
                    <td></td>
                    <td><label for="organization"><?php echo $this->t('{login:organization}'); ?></label></td>
                    <td><select name="organization" tabindex="3">
                        <?php
                        if (array_key_exists('selectedOrg', $this->data)) {
                            $selectedOrg = $this->data['selectedOrg'];
                        } else {
                            $selectedOrg = null;
                        }

                        foreach ($this->data['organizations'] as $orgId => $orgDesc) {
                            if (is_array($orgDesc)) {
                                $orgDesc = $this->t($orgDesc);
                            }

                            if ($orgId === $selectedOrg) {
                                $selected = 'selected="selected" ';
                            } else {
                                $selected = '';
                            }

                            echo '<option '.$selected.'value="'.htmlspecialchars($orgId).'">'.
                                htmlspecialchars($orgDesc).'</option>';
                        }
                        ?>
                        </select></td>
                    <td style="padding: .4em;">
                        <?php
                            if ($this->data['rememberOrganizationEnabled']) {
                                echo str_repeat("\t", 4);
                                echo '<input type="checkbox" id="remember_organization" tabindex="5"'.
                                    ' name="remember_organization" value="Yes" '.
                                    ($this->data['rememberOrganizationChecked'] ? 'checked="Yes" /> ' : '/> ').
                                     $this->t('{login:remember_organization}');
                            }
                        ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            <tr id="submit">
                <td class="loginicon"></td><td></td>
                <td>
                    <button id="submit_button" class="btn" tabindex="6" type="submit">
                        <?php echo $this->t('{login:login_button}'); ?>
                    </button>
                </td>
            </tr>
        </table>
        <input type="hidden" id="processing_trans" value="<?php echo $this->t('{login:processing}'); ?>" />
        <?php
        foreach ($this->data['stateparams'] as $name => $value) {
            echo '<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" />';
        }
        ?>
    </form>
<?php
if (!empty($this->data['links'])) {
    echo '<ul class="links" style="margin-top: 2em">';
    foreach ($this->data['links'] as $l) {
        echo '<li><a href="'.htmlspecialchars($l['href']).'">'.htmlspecialchars($this->t($l['text'])).'</a></li>';
    }
    echo '</ul>';
}
echo '<h2 class="logintext">'.$this->t('{login:help_header}').'</h2>';
echo '<p class="logintext">'.$this->t('{login:help_text}').'</p>';

$this->includeAtTemplateBase('includes/footer.php');
