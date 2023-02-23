<?php

namespace OpenSpout\Writer\ODS\Creator;

use OpenSpout\Common\Helper\Escaper;
use OpenSpout\Common\Helper\StringHelper;
use OpenSpout\Common\Manager\OptionsManagerInterface;
use OpenSpout\Writer\Common\Creator\InternalEntityFactory;
use OpenSpout\Writer\Common\Entity\Options;
use OpenSpout\Writer\Common\Helper\ZipHelper;
use OpenSpout\Writer\ODS\Helper\FileSystemHelper;

/**
 * Factory for helpers needed by the ODS Writer.
 */
class HelperFactory extends \OpenSpout\Common\Creator\HelperFactory
{
    /**
     * @return FileSystemHelper
     */
    public function createSpecificFileSystemHelper(OptionsManagerInterface $optionsManager, InternalEntityFactory $entityFactory)
    {
        $tempFolder = $optionsManager->getOption(Options::TEMP_FOLDER);
        $zipHelper = $this->createZipHelper($entityFactory);

        return new FileSystemHelper($tempFolder, $zipHelper);
    }

    /**
     * @return Escaper\ODS
     */
    public function createStringsEscaper()
    {
        return new Escaper\ODS();
    }

    /**
     * @return StringHelper
     */
    public function createStringHelper()
    {
        return new StringHelper();
    }

    /**
     * @param InternalEntityFactory $entityFactory
     *
     * @return ZipHelper
     */
    private function createZipHelper($entityFactory)
    {
        return new ZipHelper($entityFactory);
    }
}
