<?php

namespace OpenSpout\Writer\XLSX\Creator;

use OpenSpout\Common\Helper\Escaper;
use OpenSpout\Common\Helper\StringHelper;
use OpenSpout\Common\Manager\OptionsManagerInterface;
use OpenSpout\Writer\Common\Creator\InternalEntityFactory;
use OpenSpout\Writer\Common\Entity\Options;
use OpenSpout\Writer\Common\Helper\ZipHelper;
use OpenSpout\Writer\XLSX\Helper\FileSystemHelper;

/**
 * Factory for helpers needed by the XLSX Writer.
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
        $escaper = $this->createStringsEscaper();

        return new FileSystemHelper($tempFolder, $zipHelper, $escaper);
    }

    /**
     * @return Escaper\XLSX
     */
    public function createStringsEscaper()
    {
        return new Escaper\XLSX();
    }

    /**
     * @return StringHelper
     */
    public function createStringHelper()
    {
        return new StringHelper();
    }

    /**
     * @return ZipHelper
     */
    private function createZipHelper(InternalEntityFactory $entityFactory)
    {
        return new ZipHelper($entityFactory);
    }
}
