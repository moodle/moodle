<?php 
    if (!isset($this->data['autofocus'])) {
        $this->data['autofocus'] = 'username';
    }
    $this->includeAtTemplateBase('includes/header.php'); 
    if (isset($this->data['error'])) {
?>
    <div style="border-left: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8; background: #f5f5f5">
        <img src="/<?php echo $this->data['baseurlpath']; ?>resources/icons/experience/gtk-dialog-error.48x48.png" class="float-l erroricon" style="margin: 15px" alt="" />
        <h2><?php echo $this->t('error_header'); ?></h2>

        <p><?php echo $this->t($this->data['error']); ?> </p>
    </div>
<?php
}
?>
    <h2 style="break: both"><?php echo $this->t('user_pass_header'); ?></h2>
    <p class="logintext"><?php echo $this->t('user_pass_text'); ?></p>

    <form action="?" method="post" name="f">

        <table>
            <tr>
                <td rowspan="2"><img src="/<?php echo $this->data['baseurlpath']; ?>resources/icons/experience/gtk-dialog-authentication.48x48.png" id="loginicon" alt="" /></td>
                <td style="padding: .3em;"><?php echo $this->t('username'); ?></td>
                <td>
<?php
if (array_key_exists('admin', $this->data)) {
    echo '<strong style="font-size: medium">Administrator</strong>';
} else {
    echo '<input type="text" id="username" tabindex="1" name="username" autocomplete="username" ';
    if (isset($this->data['username'])) {
        echo 'value="'.htmlspecialchars($this->data['username']).'"';
    }
    echo '/>';
}
?>
                </td>
                <td style="padding: .4em;" rowspan="2">
                    <button type="submit" class="regularsubmit" tabindex="3" class="btn"><?php echo $this->t('login_button'); ?></button>
                    <input type="hidden" name="RelayState" value="<?php echo htmlspecialchars($this->data['relaystate']); ?>" />
                </td>
            </tr>
            <tr>
                <td style="padding: .3em;"><?php echo $this->t('password'); ?></td>
                <td><input id="password" type="password" tabindex="2" name="password" autocomplete="current-password" /></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>
                    <button type="submit" tabindex="5" id="mobilesubmit" class="btn"><?php echo $this->t('login_button'); ?></button>
                </td>
            </tr>
        </table>
    </form>
        <h2 class="logintext"><?php echo $this->t('help_header'); ?></h2>
        <p class="logintext"><?php echo $this->t('help_text'); ?></p>
<?php
    $this->includeAtTemplateBase('includes/footer.php');
