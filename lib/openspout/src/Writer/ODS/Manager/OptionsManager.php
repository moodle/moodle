<?php

namespace OpenSpout\Writer\ODS\Manager;

use OpenSpout\Common\Manager\OptionsManagerAbstract;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use OpenSpout\Writer\Common\Entity\Options;

/**
 * ODS Writer options manager.
 */
class OptionsManager extends OptionsManagerAbstract
{
    /** @var StyleBuilder Style builder */
    protected $styleBuilder;

    /**
     * OptionsManager constructor.
     */
    public function __construct(StyleBuilder $styleBuilder)
    {
        $this->styleBuilder = $styleBuilder;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedOptions()
    {
        return [
            Options::TEMP_FOLDER,
            Options::DEFAULT_ROW_STYLE,
            Options::SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY,
            Options::DEFAULT_COLUMN_WIDTH,
            Options::DEFAULT_ROW_HEIGHT,
            Options::COLUMN_WIDTHS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function setDefaultOptions()
    {
        $this->setOption(Options::TEMP_FOLDER, sys_get_temp_dir());
        $this->setOption(Options::DEFAULT_ROW_STYLE, $this->styleBuilder->build());
        $this->setOption(Options::SHOULD_CREATE_NEW_SHEETS_AUTOMATICALLY, true);
    }
}
