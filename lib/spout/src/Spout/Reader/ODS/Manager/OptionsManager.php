<?php

namespace Box\Spout\Reader\ODS\Manager;

use Box\Spout\Common\Manager\OptionsManagerAbstract;
use Box\Spout\Reader\Common\Entity\Options;

/**
 * Class OptionsManager
 * ODS Reader options manager
 */
class OptionsManager extends OptionsManagerAbstract
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedOptions()
    {
        return [
            Options::SHOULD_FORMAT_DATES,
            Options::SHOULD_PRESERVE_EMPTY_ROWS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions()
    {
        $this->setOption(Options::SHOULD_FORMAT_DATES, false);
        $this->setOption(Options::SHOULD_PRESERVE_EMPTY_ROWS, false);
    }
}
