<?php

namespace SimpleSAML\Module\statistics\Statistics\FieldPresentation;

class Base
{
    /** @var array */
    protected $fields;

    /** @var \SimpleSAML\XHTML\Template */
    protected $template;

    /** @var \SimpleSAML\Locale\Translate */
    protected $translator;

    /** @var string */
    protected $config;


    /**
     * @param array $fields
     * @param string $config
     * @param \SimpleSAML\XHTML\Template $template
     */
    public function __construct(array $fields, $config, $template)
    {
        $this->config = $config;
        $this->fields = $fields;
        $this->template = $template;
        $this->translator = $template->getTranslator();
    }


    /**
     * @return array
     */
    public function getPresentation()
    {
        return ['_' => 'Total'];
    }
}
