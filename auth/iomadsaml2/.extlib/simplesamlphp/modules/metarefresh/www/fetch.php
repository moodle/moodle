<?php

$config = \SimpleSAML\Configuration::getInstance();
$mconfig = \SimpleSAML\Configuration::getOptionalConfig('config-metarefresh.php');

\SimpleSAML\Utils\Auth::requireAdmin();

\SimpleSAML\Logger::setCaptureLog(true);

$sets = $mconfig->getConfigList('sets', []);

foreach ($sets as $setkey => $set) {
    \SimpleSAML\Logger::info('[metarefresh]: Executing set ['.$setkey.']');

    try {
        $expireAfter = $set->getInteger('expireAfter', null);
        if ($expireAfter !== null) {
            $expire = time() + $expireAfter;
        } else {
            $expire = null;
        }
        $metaloader = new \SimpleSAML\Module\metarefresh\MetaLoader($expire);

        # Get global black/whitelists
        $blacklist = $mconfig->getArray('blacklist', []);
        $whitelist = $mconfig->getArray('whitelist', []);
        $attributewhitelist = $mconfig->getArray('attributewhitelist', []);

        // get global type filters
        $available_types = [
            'saml20-idp-remote',
            'saml20-sp-remote',
            'shib13-idp-remote',
            'shib13-sp-remote',
            'attributeauthority-remote'
        ];
        $set_types = $set->getArrayize('types', $available_types);

        foreach ($set->getArray('sources') as $source) {
            // filter metadata by type of entity
            if (isset($source['types'])) {
                $metaloader->setTypes($source['types']);
            } else {
                $metaloader->setTypes($set_types);
            }

            # Merge global and src specific blacklists
            if (isset($source['blacklist'])) {
                $source['blacklist'] = array_unique(array_merge($source['blacklist'], $blacklist));
            } else {
                $source['blacklist'] = $blacklist;
            }

            # Merge global and src specific whitelists
            if (isset($source['whitelist'])) {
                $source['whitelist'] = array_unique(array_merge($source['whitelist'], $whitelist));
            } else {
                $source['whitelist'] = $whitelist;
            }

            # Merge global and src specific attributewhitelists, cannot use array_unique on multi-dim.
            if (isset($source['attributewhitelist'])) {
                $source['attributewhitelist'] = array_merge($source['attributewhitelist'], $attributewhitelist);
            } else {
                $source['attributewhitelist'] = $attributewhitelist;
            }

            \SimpleSAML\Logger::debug(
                '[metarefresh]: In set [' . $setkey . '] loading source [' . $source['src'] . ']'
            );
            $metaloader->loadSource($source);
        }

        $outputDir = $set->getString('outputDir');
        $outputDir = $config->resolvePath($outputDir);

        $outputFormat = $set->getValueValidate('outputFormat', ['flatfile', 'serialize'], 'flatfile');
        switch ($outputFormat) {
            case 'flatfile':
                $metaloader->writeMetadataFiles($outputDir);
                break;
            case 'serialize':
                $metaloader->writeMetadataSerialize($outputDir);
                break;
        }
    } catch (\Exception $e) {
        $e = \SimpleSAML\Error\Exception::fromException($e);
        $e->logWarning();
    }
}

$logentries = \SimpleSAML\Logger::getCapturedLog();

$t = new \SimpleSAML\XHTML\Template($config, 'metarefresh:fetch.tpl.php');
$t->data['logentries'] = $logentries;
$t->show();
