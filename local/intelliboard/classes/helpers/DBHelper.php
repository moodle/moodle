<?php

namespace local_intelliboard\helpers;

class DBHelper
{
    const MYSQL_TYPE = 'mysqli';
    const POSTGRES_TYPE = 'pgsql';
    const MARIADB_TYPE = 'mariadb';

    /**
     * @param string $groupperiod daytime|week|monthyearday|month|monthyear|quarter|year
     * @param $sqlfield
     * @return string
     * @throws \coding_exception
     * @throws \Exception
     */
    public static function group_by_date_val($groupperiod, $sqlfield, $params = []) {
        global $CFG;

        if (isset($params['offset'])) {
            $offset = intval($params['offset']);
        } else {
            $offset = 0;
        }

        switch ($groupperiod) {
            case 'daymonth':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgredaymonth', 'local_intelliboard');;
                    $result = "to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}')";
                } else {
                    $format = get_string('mysqldaymonth', 'local_intelliboard');
                    $result = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                }

                break;
            case 'daytime':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgretimedate', 'local_intelliboard');;
                    $result = "to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}')";
                } else {
                    $format = get_string('mysqltimedate', 'local_intelliboard');
                    $result = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                }

                break;
            case 'week':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgreweek', 'local_intelliboard');;
                    $result = "to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}')";
                } else {
                    $format = get_string('mysqlweek', 'local_intelliboard');
                    $result = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                }

                break;
            case 'monthyearday':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgremonthyearday', 'local_intelliboard');;
                    $result = "to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}')";
                } else {
                    $format = get_string('mysqlmonthyearday', 'local_intelliboard');
                    $result = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                }

                break;
            case 'month':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgremonth', 'local_intelliboard');;
                    $result = "to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}')";
                } else {
                    $format = get_string('mysqlmonth', 'local_intelliboard');
                    $result = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                }

                break;
            case 'monthyear':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgremonthyear', 'local_intelliboard');;
                    $result = "to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}')";
                } else {
                    $format = get_string('mysqlmonthyear', 'local_intelliboard');
                    $result = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                }

                break;
            case 'monthdayyear':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgremonthdayyear', 'local_intelliboard');;
                    $result = "to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}')";
                } else {
                    $format = get_string('mysqlmonthdayyear', 'local_intelliboard');
                    $result = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                }

                break;
            case 'timemonthdayyear':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgretimemonthdayyear', 'local_intelliboard');;
                    $result = "to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}')";
                } else {
                    $format = get_string('mysqltimemonthdayyear', 'local_intelliboard');
                    $result = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                }

                break;
            case 'quarter':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgrequarteryear', 'local_intelliboard');;
                    $result = "CONCAT('Q', to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}'))";
                } else {
                    $format = get_string('mysqlyear', 'local_intelliboard');
                    $quarter = "QUARTER(FROM_UNIXTIME({$sqlfield} + {$offset}))";
                    $year = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                    $result = "CONCAT('Q', {$quarter}, ' ', {$year})";
                }

                break;
            case 'year':
                if ($CFG->dbtype == self::POSTGRES_TYPE) {
                    $format = get_string('postgreyear', 'local_intelliboard');;
                    $result = "to_char(to_timestamp({$sqlfield} + {$offset}),'{$format}')";
                } else {
                    $format = get_string('mysqlyear', 'local_intelliboard');
                    $result = "FROM_UNIXTIME({$sqlfield} + {$offset}, '{$format}')";
                }

                break;
            default:
                throw new \Exception('Invalid grouping period');
        }

        return $result;
    }

    /**
     * @param $type
     * @return string
     * @throws \Exception
     */
    public static function get_typecast($type) {
        global $CFG;

        if ($CFG->dbtype != self::POSTGRES_TYPE) {
            return '';
        }

        switch ($type) {
            case 'numeric':
                return '::NUMERIC';
            case 'integer':
                    return '::INTEGER';
            case 'text':
                return '::TEXT';
            default:
                throw new \Exception('Invalid type');
        }
    }

    /**
     * @param $paramname
     * @param $separator
     * @return string
     */
    public static function get_group_concat($paramname, $separator) {
        global $CFG;

        if ($CFG->dbtype == 'pgsql') {
            $group_concat = "string_agg($paramname::character, '$separator')";
        } else {
            $group_concat = "GROUP_CONCAT(DISTINCT $paramname SEPARATOR '$separator')";
        }

        return $group_concat;
    }

    public static function prepare_sql_in_query($column, $ids) {
        if (!empty($ids)) {
            return "$column IN (".implode(",", $ids).")";
        } else {
            return "$column = -1";
        }
    }

    public static function prepare_sql_for_row_number_counting() {
        global $CFG;

        if ($CFG->dbtype == 'pgsql') {
            $uniqueIdColumn = "ROW_NUMBER () OVER ()";
            $uniqueIdColumn2 = "";
        } else {
            $uniqueIdColumn = "@rowid := @rowid + 1";
            $uniqueIdColumn2 = "(SELECT @rowid := 0) as curr_row, ";
        }
        return [$uniqueIdColumn,$uniqueIdColumn2];
    }
}