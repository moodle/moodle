<?php

namespace OpenSpout\Reader\XLSX\Manager;

use OpenSpout\Common\Manager\OptionsManagerAbstract;
use OpenSpout\Reader\Common\Entity\Options;

/**
 * XLSX Reader options manager.
 */
class OptionsManager extends OptionsManagerAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedOptions()
    {
        return [
            Options::TEMP_FOLDER,
            Options::SHOULD_FORMAT_DATES,
            Options::SHOULD_PRESERVE_EMPTY_ROWS,
            Options::SHOULD_USE_1904_DATES,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions()
    {
        $this->setOption(Options::TEMP_FOLDER, sys_get_temp_dir());
        $this->setOption(Options::SHOULD_FORMAT_DATES, false);
        $this->setOption(Options::SHOULD_PRESERVE_EMPTY_ROWS, false);
        $this->setOption(Options::SHOULD_USE_1904_DATES, false);
    }
}
