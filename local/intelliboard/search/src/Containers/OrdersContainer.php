<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 * @package    local_intelliboard
 * @copyright  2019 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    http://intelliboard.net/
 */

namespace Containers;

use DataExtractor;

class OrdersContainer extends BaseContainer {

    public static function get($selected, DataExtractor $extractor, $params = array()) {
        $directions = array(
            1 => array(
                DataExtractor::MYSQL_MODE => 'ASC',
                DataExtractor::POSTGRES_MODE => 'ASC NULLS FIRST'
            ),
            2 => array(
                DataExtractor::MYSQL_MODE => 'DESC',
                DataExtractor::POSTGRES_MODE => 'DESC NULLS LAST'
            )
        );

        $mode = $extractor->getMode();

        $directions = array_map(function($direction) use ($mode) {
            if (is_array($direction)) {
                $direction = $direction[$mode];
            }
            return $direction;
        }, $directions);

        return array_map(function($order) use ($directions, $extractor) {

            if (empty($order['name'])) {
                $column = ColumnsContainer::get($order, $extractor)['sql'];
                $order['name'] = $column;
            }

            $order['direction'] = isset($order['direction'])? $directions[$order['direction']] : $directions[1];

            return $order;
        }, $selected);

    }

    public static function construct($orders, DataExtractor $extractor, $params = array()) {
        return implode(','  . $extractor->getSeparator() . ' '  . $extractor->getSeparator(), array_map(function($order) {
            return $order['name'] . ' ' . $order['direction'];
        }, $orders));
    }

}