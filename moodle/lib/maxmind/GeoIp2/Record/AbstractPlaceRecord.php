<?php

namespace GeoIp2\Record;

abstract class AbstractPlaceRecord extends AbstractRecord
{
    private $locales;

    /**
     * @ignore
     */
    public function __construct($record, $locales = array('en'))
    {
        $this->locales = $locales;
        parent::__construct($record);
    }

    /**
     * @ignore
     */
    public function __get($attr)
    {
        if ($attr == 'name') {
            return $this->name();
        } else {
            return parent::__get($attr);
        }
    }

    private function name()
    {
        foreach ($this->locales as $locale) {
            if (isset($this->names[$locale])) {
                return $this->names[$locale];
            }
        }
    }
}
