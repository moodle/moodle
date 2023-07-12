<?php

namespace SimpleSAML\Module\statistics\Statistics\FieldPresentation;

use SimpleSAML\Metadata\MetaDataStorageHandler;

class Entity extends Base
{
    /**
     * @return array
     */
    public function getPresentation()
    {
        $mh = MetaDataStorageHandler::getMetadataHandler();
        $metadata = $mh->getList($this->config);

        $translation = ['_' => 'All services'];
        foreach ($this->fields as $field) {
            if (array_key_exists($field, $metadata)) {
                if (array_key_exists('name', $metadata[$field])) {
                    $translation[$field] = $this->translator->t($metadata[$field]['name']);
                }
            }
        }
        return $translation;
    }
}
