<?php
$this->data['header'] = $this->t($this->data['headerString']);
$this->includeAtTemplateBase('includes/header.php'); ?>
    <h2><?php echo $this->data['header']; ?></h2>
    <p><?php echo $this->t('metadata_intro'); ?></p>
<?php if (isset($this->data['metaurl'])) { ?>
    <p><?php echo($this->t('metadata_xmlurl', ['%METAURL%' => htmlspecialchars($this->data['metaurl'])])); ?></p>
    <div class="input-group">
        <pre id="metadataurl" class="input-left"><?php echo htmlspecialchars($this->data['metaurl']); ?></pre>
        <button data-clipboard-target="#metadataurl" id="btnurl" class="btnaddonright">
            <img src="/<?php echo $this->data['baseurlpath'].'resources/icons/clipboard.svg'; ?>"
                 alt="Copy to clipboard" />
        </button>
    </div>
<?php } ?>
    <h2><?php echo($this->t('metadata_metadata')); ?></h2>
    <p><?php echo($this->t('metadata_xmlformat')); ?></p>
    <div class="metadatabox">
         <button data-clipboard-target="#xmlmetadata" id="btnxml" class="btn topright" style="margin-right: 0.5em;">
            <img src="/<?php echo $this->data['baseurlpath'].'resources/icons/clipboard.svg'; ?>"
                 alt="Copy to clipboard" />
        </button>
        <pre id="xmlmetadata"><?php echo $this->data['metadata']; ?></pre>
    </div>

    <p><?php echo($this->t('metadata_simplesamlformat')); ?></p>
    <div class="metadatabox">
        <button data-clipboard-target="#phpmetadata" id="btnphp" class="btn topright" style="margin-right: 0.5em;">
            <img src="/<?php echo $this->data['baseurlpath'].'resources/icons/clipboard.svg'; ?>"
                 alt="Copy to clipboard" />
        </button>
        <pre id="phpmetadata"><?php echo $this->data['metadataflat']; ?></pre>
    </div>
    <script type="text/javascript">
        var clipboard1 = new ClipboardJS('#btnurl'),
            clipboard2 = new ClipboardJS('#btnxml'),
            clipboard3 = new ClipboardJS('#btnphp');
    </script>
<?php
if (array_key_exists('available_certs', $this->data)) { ?>
    <h2><?php echo($this->t('metadata_cert')); ?></h2>
    <p><?php echo($this->t('metadata_cert_intro')); ?></p>
    <ul>
<?php
    foreach (array_keys($this->data['available_certs']) as $certName) {
        echo '<li><a href="'.
            htmlspecialchars(SimpleSAML\Module::getModuleURL('saml/idp/certs.php').'/'.$certName).'">'.$certName.
            '</a>';

        if ($this->data['available_certs'][$certName]['certFingerprint'][0] ===
            'afe71c28ef740bc87425be13a2263d37971da1f9') {
            echo '&nbsp; <img style="display: inline;" src="/'.$this->data['baseurlpath'].
                'resources/icons/silk/exclamation.png" alt="default certificate" />'.
                'This is the default certificate. Generate a new certificate if this is a production system.';
        }
        echo '</li>';
    } ?>
    </ul>
<?php
}
$this->includeAtTemplateBase('includes/footer.php');
