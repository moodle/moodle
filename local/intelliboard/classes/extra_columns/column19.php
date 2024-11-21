<?php

namespace local_intelliboard\extra_columns;

class column19 extends base_column {
    public function get_join() {
        $useridcolumnsql = isset($this->fields[0]) ? $this->fields[0] : "u.id";
        $vendorsnamessql = get_operator('GROUP_CONCAT', 'DISTINCT(liv.name)', ['separator' => ', ']);

        return [
            "LEFT JOIN (
                SELECT liu.userid, {$vendorsnamessql} AS names
                  FROM {local_intellicart_users} liu
                  JOIN {local_intellicart_vendors} liv ON liv.id = liu.instanceid
                 WHERE liu.type = 'vendor'
              GROUP BY liu.userid
            ) intellicart_vendors ON intellicart_vendors.userid = {$useridcolumnsql}",
            []
        ];
    }
}
