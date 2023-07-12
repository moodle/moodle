<?php

/**
 * Hook to add the metadata for hosted entities to the frontpage.
 *
 * @param array &$metadataHosted  The metadata links for hosted metadata on the frontpage.
 * @return void
 */
function saml_hook_metadata_hosted(&$metadataHosted)
{
    assert(is_array($metadataHosted));

    $sources = \SimpleSAML\Auth\Source::getSourcesOfType('saml:SP');

    foreach ($sources as $source) {
        /** @var \SimpleSAML\Module\saml\Auth\Source\SP $source */
        $metadata = $source->getMetadata();

        $name = $metadata->getValue('name', null);
        if ($name === null) {
            $name = $metadata->getValue('OrganizationDisplayName', null);
        }
        if ($name === null) {
            $name = $source->getAuthId();
        }

        $md = [
            'entityid' => $source->getEntityId(),
            'metadata-index' => $source->getEntityId(),
            'metadata-set' => 'saml20-sp-hosted',
            'metadata-url' => $source->getMetadataURL() . '?output=xhtml',
            'name' => $name,
        ];

        $metadataHosted[] = $md;
    }
}
