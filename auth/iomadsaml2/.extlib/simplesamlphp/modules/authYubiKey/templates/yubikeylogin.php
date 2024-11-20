<?php

$this->includeAtTemplateBase('includes/header.php');

if ($this->data['errorCode'] !== null) {
?>
    <div style="border-left: 1px solid #e8e8e8; border-bottom: 1px solid #e8e8e8; background: #f5f5f5">
        <img src="/<?php echo $this->data['baseurlpath']; ?>resources/icons/experience/gtk-dialog-error.48x48.png" class="float-l" style="margin: 15px" alt="" />
        <h2><?php echo $this->t('{login:error_header}'); ?></h2>
        <p><b><?php echo $this->t($this->data['errorcodes']['title'][$this->data['errorcode']]); ?></b></p>
        <p><?php echo $this->t($this->data['errorcodes']['descr'][$this->data['errorcode']]); ?></p>
    </div>
<?php
}
?>
    <img style="float: right" src="<?php echo($this->data['logoUrl']); ?>" alt="" />
    <img style="clear: right; float: right" src="<?php echo($this->data['devicepicUrl']); ?>" alt="YubiKey" />

    <h2 style=""><?php echo $this->data['header']; ?></h2>

    <form action="?" method="post" name="f">

        <p><?php echo $this->t('{authYubiKey:yubikey:intro}'); ?></p>

        <p><input id="otp" style="border: 1px solid #ccc; background: #eee; padding: .5em; font-size: medium; width: 70%; color: #aaa" type="text" tabindex="2" name="otp" /></p>

<?php
foreach ($this->data['stateParams'] as $name => $value) {
    echo '<input type="hidden" name="'.htmlspecialchars($name).'" value="'.htmlspecialchars($value).'" />';
}
?>
    </form>
<?php

$this->includeAtTemplateBase('includes/footer.php');
