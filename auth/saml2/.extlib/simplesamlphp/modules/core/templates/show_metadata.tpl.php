<?php
$this->includeAtTemplateBase('includes/header.php');
?>
<div class="metadatabox">
    <button data-clipboard-target="#metadata" id="btncp" class="btn topright" style="margin-right: 0.5em;">
        <img src="/<?php echo $this->data['baseurlpath'].'resources/icons/clipboard.svg'; ?>"
             alt="Copy to clipboard" />
    </button>
    <pre id="metadata">
$metadata['<?php echo $this->data['m']['metadata-index']; unset($this->data['m']['metadata-index']) ?>'] = <?php
    echo htmlspecialchars(var_export($this->data['m'], true));
?>;
    </pre>
</div>
<script type="text/javascript">
    var clipboard = new ClipboardJS('#btncp');
</script>
<br/>
<p><a href="<?php echo $this->data['backlink']; ?>"><span class="btn">Back</span></a></p>

<?php
$this->includeAtTemplateBase('includes/footer.php');
