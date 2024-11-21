<?php

namespace local_intelliboard\reports\entities;

use local_intelliboard\helpers\CountryHelper;
use local_intelliboard\helpers\DBHelper;

class reportColumnFilter
{
    const TYPE_COUNTRY = "country";
    const TYPE_NORMAL = "normal";
    const TYPE_FILECOMPONENT = "file_component";
    const TYPE_ROLENAME = "rolename";

    private $column;
    private $filterVal;
    private $filterPrefix;

    public function __construct($column, $filterVal, $filterPrefix)
    {
        if (is_array($column)) {
            $this->column = $column;
        } else {
            $this->column = [
                "sql_column" => $column,
                "type" => self::TYPE_NORMAL
            ];
        }

        $this->filterVal = $filterVal;
        $this->filterPrefix = $filterPrefix;
    }

    public function getFilterValue()
    {
        try {
            switch ($this->column["type"]) {
                case self::TYPE_COUNTRY:
                    $countryCode = CountryHelper::getCountryCodeByName($this->filterVal);

                    if ($countryCode) {
                        return "%{$countryCode}%";
                    }

                    return "%{$this->filterVal}%";
                case self::TYPE_FILECOMPONENT:
                    $formattedfilterval = strtolower(str_replace(" ", "_", $this->filterVal));
                    return "%{$formattedfilterval}%";
                case self::TYPE_ROLENAME:
                    $roles = role_fix_names(get_all_roles());
                    $roleid = 0;

                    foreach ($roles as $role) {
                        if (strtolower($role->localname) == strtolower($this->filterVal)) {
                            $roleid = $role->id;
                            break;
                        }
                    }

                    if ($roleid) {
                        return $roleid;
                    }

                    return $this->filterVal;
                default:
                    return "%{$this->filterVal}%";
            }
        } catch (\Exception $e) {
            return "%{$this->filterVal}%";
        }
    }

    /**
     * @return string
     * @throws \coding_exception
     * @throws \Exception
     */
    public function getFilterSQL()
    {
        global $DB;

        $textTypeCast = DBHelper::get_typecast("text");
        $key = $this->getFilterKey();

        return $DB->sql_like($this->column["sql_column"] . $textTypeCast, ":{$key}", false, false);
    }

    /**
     * @return string
     * @throws \coding_exception
     */
    public function getFilterKey()
    {
        return substr(strtolower(clean_param($this->column["sql_column"], PARAM_ALPHANUMEXT)), 0, 20) . $this->filterPrefix;
    }
}