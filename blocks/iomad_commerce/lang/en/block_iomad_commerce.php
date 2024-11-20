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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['add_course_to_shop'] = 'Add new product';
$string['add_more_license_blocks'] = 'Add another license block';
$string['addnewcourse'] = 'Add new product';
$string['allow_license_blocks' ] = 'Allow license blocks';
$string['allow_license_blocks_help'] = 'When enabled a client administrator can purchase blocks of courses';
$string['allow_single_purchase'] = 'Allow single purchase';
$string['allow_single_purchase_help'] = 'When single purchase is enabled individual users can purchase their own access to the course';
$string['amount'] = 'Amount';
$string['blocktitle'] = 'IOMAD E-Commerce';
$string['basket'] = 'Basket';
$string['basket_1item'] = 'There is {$a} item in your basket.';
$string['basket_nitems'] = 'There are {$a} items in your basket.';
$string['buycourses'] = 'Buy courses';
$string['buynow'] = 'Buy now';
$string['categorization'] = 'Categorisation';
$string['checkout'] = 'Checkout';
$string['checkoutpreamble'] = '<p></p>';
$string['commerce_admin_email'] = 'Commerce Admin email address';
$string['commerce_admin_email_help'] = 'Email address of the person looking after the shop.';
$string['commerce_enabled'] = '{$a} enabled';
$string['commerce_admin_firstname'] = 'Commerce Admin displayed first name';
$string['commerce_admin_firstname_help'] = 'First name of the person looking after the shop.  Used in emails';
$string['commerce_admin_lastname'] = 'Commerce Admin displayed last name';
$string['commerce_admin_lastname_help'] = 'Last name of the person looking after the shop.  Used in emails';
$string['commerce_default_license_access_length'] = 'Default license access days';
$string['commerce_default_license_access_length_help'] = 'This is the length of time in days users have access to the courses when purchased from the external shop.';
$string['commerce_admin_default_license_shelf_life'] = 'Default license shelf life days';
$string['commerce_admin_default_license_shelf_life_help'] = 'This is the length of time in days that the license can remain unused before it is no longer valid.';
$string['commerce_externalshop_link_timeout'] = 'Number of seconds for external shop token expires'; 
$string['commerce_externalshop_link_timeout_help'] = 'This is the number of seconds that the link to the external shop will remain valid for.  Having this a a large number will mean that the links could be stolen for another user to log into the shop.';
$string['commerce_externalshop_url'] = 'Default URL for external ecommerce site';
$string['commerce_externalshop_url_company'] = 'URL for company specific external ecommerce site';
$string['confirm'] = 'Confirm';
$string['confirmation'] = 'Your order is complete';
$string['Course'] = 'Product';
$string['courses'] = 'Products';
$string['course_list_title'] = 'Manage eCommerce products';
$string['course_list_title_default'] = 'Manage eCommerce product templates';
$string['course_long_description'] = 'Long description';
$string['course_shop_enabled'] = 'Visible in shop';
$string['course_shop_enabled_help'] = 'Set to <b>Yes</b> for this course to be displayed on the shop screen';
$string['course_shop_title'] = 'Shop';
$string['course_short_summary'] = 'Short description';
$string['coursedeletecheckfull'] = 'Are you absolutely sure you want to delete {$a} and its settings from the shop?';
$string['courseunavailable'] = 'This product is not available.';
$string['currency'] = 'Currency';
$string['decimalnumberonly'] = 'Decimal numbers only, please.';
$string['deletecourse'] = 'Delete product from Shop';
$string['edit_course_shopsettings'] = 'Edit product shop settings';
$string['edit_invoice'] = 'Edit order';
$string['exportproduct'] = 'Export product item';
$string['exportproductcheckfull'] = 'This will save the selected product item as a template which can then be allocated to other companies.';
$string['importproduct'] = 'Import product item';
$string['importproductcheckfull'] = 'This will create a new product item in the current company shop from this template. Any courses which the current company cannot see will be removed from the product so please check he product setings after import.';
$string['iomad_commerce:add_course'] = 'Add a product to the shop';
$string['iomad_commerce:addinstance'] = 'Add a new IOMAD Ecommerce block';
$string['iomad_commerce:admin_view'] = 'View the Ecommerce admin pages';
$string['iomad_commerce:buyinbulk'] = 'Access to buy items in bulk in the shop';
$string['iomad_commerce:buyitnow'] = 'Access to the \'buy it now\' button in the shop';
$string['iomad_commerce:delete_course'] = 'Remove a product from the shop';
$string['iomad_commerce:edit_course'] = 'Edit a product in the shop';
$string['iomad_commerce:hide_course'] = 'Hide a product in the shop';
$string['iomad_commerce:manage_default'] = 'Manage template products';
$string['iomad_commerce:myaddinstance'] = 'Add a new IOMAD Ecommerce block to the users dashboard';
$string['itemaddedsuccessfully'] = 'Product created successfully';
$string['emptybasket'] = 'Your basket is empty';
$string['error_duplicateblockstarts'] = 'Duplicate # of licenses.';
$string['error_incompatibletype'] = 'Single purchase can only be allowed for multiple courses when this is a program of courses';
$string['error_invalidblockstarts'] = 'One or more # of licenses is invalid.';
$string['error_invalidblockprices'] = 'One or more prices are invalid.';
$string['error_invalidblockvalidlengths'] = 'One or more valid lengths are invalid.';
$string['error_invalidblockshelflives'] = 'One or more shelf lives are invalid.';
$string['error_singlepurchaseprice'] = 'Single purchase price should be more than 0.';
$string['error_singlepurchasevalidlength'] = 'Valid length should be more than 0.';
$string['filter_by_tag'] = 'Choose products by category: ';
$string['filtered_by_tag'] = 'Products within the category - {$a}.';
$string['filtered_by_search'] = 'You searched for {$a}.';
$string['gotoshop'] = 'Go to shop';
$string['hide'] = 'Hide from shop';
$string['licenseblock_start'] = 'From # of licenses';
$string['licenseblock_price'] = 'Price per license';
$string['licenseblock_shelflife'] = 'Shelf life (days)';
$string['licenseblock_validlength'] = 'Valid (days)';
$string['licenseblock_n'] = '{$a} or more licenses';
$string['licenseblocks'] = 'License blocks';
$string['licenseformempty'] = 'Please enter the number of licenses you need.';
$string['licenseoptionsavailableforregisteredcompanies'] = 'License options available for registered companies, please login';
$string['loginforlicenseoptions'] = 'Please log in to see license options';
$string['managecompanyproducts'] = 'Manage company products';
$string['managedefaultproducts'] = 'Manage template products';
$string['missingshortsummary'] = 'Short description is missing.';
$string['moreinfo'] = 'more info';
$string['multiplecurrencies'] = 'WARNING: You have added items from the shop which have different currencies. You cannot complete your transaction.';
$string['name'] = 'Name';
$string['nocoursesnotontheshop'] = 'There are no courses available to be added to the shop.';
$string['nocoursesontheshop'] = 'There are no products on the shop matching your criteria.';
$string['noinvoices'] = 'There are no invoices matching your criteria.';
$string['noproviders'] = 'No payment providers have been enabled. Please contact the site administrator';
$string['notconfigured'] = 'The eCommerce block has not been configured. Check the settings page for the block';
$string['opentoallcompanies'] = 'Shop is available to every company';
$string['opentoallcompanies_help'] = 'If this is disabled, then access to the shop can be turned on on a per company basis through the IOMAD control panel. If you enable it for the first time, you will then need to turn on the shop for the companies who require it.';
$string['or'] = 'or';
$string['order'] = 'Order';
$string['orders'] = 'Orders';
$string['payment_options'] = 'Payment options';
$string['paymentprocessing'] = 'Payment processing';
$string['paymentprovider'] = 'Payment provider';
$string['payment_provider_disabled'] = '{$a} is disabled';
$string['paymentprovider_enabled'] = 'Enable {$a}';
$string['paymentprovider_enabled_help'] = 'Select this if you want {$a} to be enabled as a payment provider.';
$string['pluginname'] = 'IOMAD eCommerce';
$string['postcode'] = 'Postcode';
$string['pp_historic'] = 'Historic PayPal';
$string['pricefrom'] = 'From {$a}';
$string['priceoptions'] = 'Price options';
$string['privacy:metadata'] = 'The IOMAD E-Commerce block only shows data stored in other locations.';
$string['privacy:metadata:invoice:id'] = 'ID from the {invoice} table';
$string['privacy:metadata:invoice:reference'] = 'Invoice reference';
$string['privacy:metadata:invoice:userid'] = 'Invoice userid';
$string['privacy:metadata:invoice:status'] = 'Invoice status';
$string['privacy:metadata:invoice:checkout_method'] = 'Invoice checkout method';
$string['privacy:metadata:invoice:email'] = 'Invoice email address';
$string['privacy:metadata:invoice:phone1'] = 'Invoice main phone number';
$string['privacy:metadata:invoice:paymentid'] = 'Invoice Moodle payment id';
$string['privacy:metadata:invoice:company'] = 'Invoice company';
$string['privacy:metadata:invoice:address'] = 'Invoice address';
$string['privacy:metadata:invoice:city'] = 'Invoice city';
$string['privacy:metadata:invoice:state'] = 'Invoice state';
$string['privacy:metadata:invoice:country'] = 'Invoice country';
$string['privacy:metadata:invoice:postcode'] = 'Invoice postcode';
$string['privacy:metadata:invoice:firstname'] = 'Invoice firstname';
$string['privacy:metadata:invoice:lastname'] = 'Invoice lastname';
$string['privacy:metadata:invoice:date'] = 'Invoice payment date';
$string['privacy:metadata:invoice'] = 'Invoice metadata';
$string['process'] = 'Process';
$string['processed'] = 'Processed';
$string['process_help'] = 'Items with checked boxes in the \'Process\' column will be processed as \'order complete\' when saving changes.';
$string['productexportedsuccessfully'] = 'Product item was successfully saved as a template.';
$string['productexportfailed'] = 'There was an issue when trying to save this product item as a template. If this persists, please contact your site administrator.';
$string['productimportedsuccessfully'] = 'Template item was successfully saved as a product.';
$string['productimportfailed'] = 'There was an issue when trying to save this template item as a product. If this persists, please contact your site administrator.';
$string['purchaser_details'] = 'Purchaser';
$string['reference'] = 'Reference';
$string['remove_filter'] = 'Show all products';
$string['returntoshop'] = 'Continue shopping';
$string['review'] = 'Review your order';
$string['search'] = 'Search';
$string['selectcoursetoadd'] = 'Select product to add to shop';
$string['select_tag'] = 'Select category';
$string['shop'] = 'Shop';
$string['shop_title'] = 'Shop';
$string['show'] = 'Show in shop';
$string['single_purchase'] = 'Single purchase';
$string['single_purchase_price'] = 'Single purchase price';
$string['single_purchase_price_help'] = 'Price for an individual license';
$string['single_purchase_validlength'] = 'Valid length (days)';
$string['single_purchase_validlength_help'] = 'The user will be enrolled in the course for this number of days after first enrolling. After this they will automatically removed (completely) from the course';
$string['single_purchase_shelflife'] = 'Shelf life (days)';
$string['single_purchase_shelflife_help'] = 'The user must enrol in the course within this number of days or the license will expire.';
$string['state'] = 'County';
$string['status'] = 'Status';
$string['status_b'] = 'Basket';
$string['status_u'] = 'Unpaid';
$string['status_p'] = 'Paid';
$string['tags'] = 'Categories';
$string['tags_help'] = 'Categorise this product by adding categories separated by commas. You can select existing categories or add new ones to be created.';
$string['total'] = 'Total';
$string['type_quantity_1_singlepurchase'] = 'Single purchase';
$string['type_quantity_n_singlepurchase'] = 'Single purchase';
$string['type_quantity_1_licenseblock'] = '{$a} license';
$string['type_quantity_n_licenseblock'] = '{$a} licenses';
$string['unitprice'] = 'Price per license';
$string['unprocessed'] = 'Unprocessed';
$string['unprocesseditems'] = 'Unprocessed items';
$string['useexternalshop'] = 'Use an external eCommerce solution for purchases';
$string['useexternalshop_help'] = 'Enable this if you have an external eCommerce solution which has the correct Webservices to work with IOMAD.';
$string['value'] = 'Value';
