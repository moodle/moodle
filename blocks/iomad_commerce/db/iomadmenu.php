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

// Define the Iomad menu items that are defined by this plugin

function block_iomad_commerce_menu() {

    return array(
        'ShopSettings_list' => array(
            'category' => 'ECommerceAdmin',
            'tab' => 6,
            'name' => get_string('courses', 'block_iomad_commerce'),
            'url' => '/blocks/iomad_commerce/courselist.php',
            'cap' => 'block/iomad_commerce:admin_view',
            'icondefault' => 'courses',
            'style' => 'ecomm',
            'icon' => 'fa-file-text',
            'iconsmall' => 'fa-money'
        ),
        'Orders' => array(
            'category' => 'ECommerceAdmin',
            'tab' => 6,
            'name' => get_string('orders', 'block_iomad_commerce'),
            'url' => '/blocks/iomad_commerce/orderlist.php',
            'cap' => 'block/iomad_commerce:admin_view',
            'icondefault' => 'orders',
            'style' => 'ecomm',
            'icon' => 'fa-truck',
            'iconsmall' => 'fa-eye'
        ),
    );
}
