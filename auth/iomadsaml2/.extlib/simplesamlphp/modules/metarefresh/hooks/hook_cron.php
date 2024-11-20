<?php

use SimpleSAML\Logger;

/**
 * Hook to run a cron job.
 *
 * @param array &$croninfo  Output
 * @return void
 */
function metarefresh_hook_cron(&$croninfo)
{
    assert(is_array($croninfo));
    assert(array_key_exists('summary', $croninfo));
    assert(array_key_exists('tag', $croninfo));

    Logger::info('cron [metarefresh]: Running cron in cron tag ['.$croninfo['tag'].'] ');

    try {
        $config = \SimpleSAML\Configuration::getInstance();
        $mconfig = \SimpleSAML\Configuration::getOptionalConfig('config-metarefresh.php');

        $sets = $mconfig->getConfigList('sets', []);
        /** @var string $datadir */
        $datadir = $config->getPathValue('datadir', 'data/');
        $stateFile = $datadir.'metarefresh-state.php';

        foreach ($sets as $setkey => $set) {
            // Only process sets where cron matches the current cron tag
            $cronTags = $set->getArray('cron');
            if (!in_array($croninfo['tag'], $cronTags, true)) {
                continue;
            }

            Logger::info('cron [metarefresh]: Executing set ['.$setkey.']');

            $expireAfter = $set->getInteger('expireAfter', null);
            if ($expireAfter !== null) {
                $expire = time() + $expireAfter;
            } else {
                $expire = null;
            }

            $outputDir = $set->getString('outputDir');
            $outputDir = $config->resolvePath($outputDir);
            if ($outputDir === null) {
                throw new \Exception("Invalid outputDir specified.");
            }

            $outputFormat = $set->getValueValidate('outputFormat', ['flatfile', 'serialize'], 'flatfile');

            $oldMetadataSrc = \SimpleSAML\Metadata\MetaDataStorageSource::getSource([
                'type' => $outputFormat,
                'directory' => $outputDir,
            ]);

            $metaloader = new \SimpleSAML\Module\metarefresh\MetaLoader($expire, $stateFile, $oldMetadataSrc);

            // Get global blacklist, whitelist, attributewhitelist and caching info
            $blacklist = $mconfig->getArray('blacklist', []);
            $whitelist = $mconfig->getArray('whitelist', []);
            $attributewhitelist = $mconfig->getArray('attributewhitelist', []);
            $conditionalGET = $mconfig->getBoolean('conditionalGET', false);

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

                // Merge global and src specific blacklists
                if (isset($source['blacklist'])) {
                    $source['blacklist'] = array_unique(array_merge($source['blacklist'], $blacklist));
                } else {
                    $source['blacklist'] = $blacklist;
                }

                // Merge global and src specific whitelists
                if (isset($source['whitelist'])) {
                    $source['whitelist'] = array_unique(array_merge($source['whitelist'], $whitelist));
                } else {
                    $source['whitelist'] = $whitelist;
                }

                # Merge global and src specific attributewhitelists: cannot use array_unique for multi-dim.
                if (isset($source['attributewhitelist'])) {
                    $source['attributewhitelist'] = array_merge($source['attributewhitelist'], $attributewhitelist);
                } else {
                    $source['attributewhitelist'] = $attributewhitelist;
                }

                // Let src specific conditionalGET override global one
                if (!isset($source['conditionalGET'])) {
                    $source['conditionalGET'] = $conditionalGET;
                }

                Logger::debug('cron [metarefresh]: In set ['.$setkey.'] loading source ['.$source['src'].']');
                $metaloader->loadSource($source);
            }

            // Write state information back to disk
            $metaloader->writeState();

            switch ($outputFormat) {
                case 'flatfile':
                    $metaloader->writeMetadataFiles($outputDir);
                    break;
                case 'serialize':
                    $metaloader->writeMetadataSerialize($outputDir);
                    break;
            }

            if ($set->hasValue('arp')) {
                $arpconfig = \SimpleSAML\Configuration::loadFromArray($set->getValue('arp'));
                $metaloader->writeARPfile($arpconfig);
            }
        }
    } catch (\Exception $e) {
        $croninfo['summary'][] = 'Error during metarefresh: '.$e->getMessage();
    }
}
