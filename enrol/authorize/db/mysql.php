<?PHP  //$Id$

// MySQL commands for upgrading this enrolment module

function authorize_upgrade($oldversion=0) {

    global $CFG, $THEME, $db;

    $result = true;

    if ($oldversion == 0) {
        modify_database("$CFG->dirroot/enrol/authorize/db/mysql.sql");
    }

    if ($oldversion < 2005071400) {
        execute_sql("CREATE TABLE `{$CFG->prefix}currencies` (
                                  `id`         int(10) unsigned NOT NULL auto_increment,
                                  `code`       char(3) NOT NULL default '',
                                  `name`       varchar(64) NOT NULL default '',
                                  PRIMARY KEY  (`id`),
                                  UNIQUE KEY `code` (`code`)
                                  ) TYPE=MyISAM 
                                  COMMENT='Currencies codes and names';");
        insert_currencies();

        $curcode = empty($CFG->enrol_currency) ? 'USD' : $CFG->enrol_currency;
        $objcur = get_record("currencies", "code", $curcode);
        table_column('course', '', 'currency', 'integer', '10', 'unsigned', $objcur->id, 'not null', 'cost');
    }

    if ($oldversion < 2005071600) {
        // Be sure, only last 4 digit is inserted.
        table_column('enrol_authorize', 'cclastfour', 'cclastfour', 'integer', '4', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'courseid', 'courseid', 'integer', '10', 'unsigned', '0', 'not null');
        table_column('enrol_authorize', 'userid ', 'userid ', 'integer', '10', 'unsigned', '0', 'not null');
	// Add some indexes for speed.
        execute_sql(" ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX courseid(courseid) ");
        execute_sql(" ALTER TABLE `{$CFG->prefix}enrol_authorize` ADD INDEX userid(userid) ");
    }

    return $result;

}

function insert_currencies() {

    global $CFG;

    $currencies = array(
        'AFA' => 'Afghanistan Afghani',
        'ALL' => 'Albanian Lek',
        'DZD' => 'Algerian Dinar',
        'ADP' => 'Andorran Peseta',
        'AOK' => 'Angolan Kwanza',
        'ARA' => 'Argentinian Austral',
        'AWG' => 'Aruban Florin',
        'AUD' => 'Australian Dollar',
        'ATS' => 'Austrian Schilling',
        'BSD' => 'Bahamian Dollar',
        'BHD' => 'Bahraini Dinar',
        'BDT' => 'Bangladeshi Taka',
        'BBD' => 'Barbados Dollar',
        'BEF' => 'Belgian Franc',
        'BZD' => 'Belize Dollar',
        'BMD' => 'Bermudian Dollar',
        'BTN' => 'Bhutan Ngultrum',
        'BOB' => 'Bolivian Boliviano',
        'BWP' => 'Botswanian Pula',
        'BRC' => 'Brazilian Cruzeiro',
        'GBP' => 'British Pound',
        'BND' => 'Brunei Dollar',
        'BGL' => 'Bulgarian Lev',
        'BUK' => 'Burma Kyat',
        'BIF' => 'Burundi Franc',
        'CAD' => 'Canadian Dollar',
        'CVE' => 'Cape Verde Escudo',
        'KYD' => 'Cayman Islands Dollar',
        'CLP' => 'Chilean Peso',
        'CLF' => 'Chilean Unidades de Fomento',
        'COP' => 'Colombian Peso',
        'KMF' => 'Comoros Franc',
        'CRC' => 'Costa Rican Colon',
        'CUP' => 'Cuban Peso',
        'CYP' => 'Cyprus Pound',
        'CSK' => 'Czech Koruna',
        'DKK' => 'Danish Krone',
        'YDD' => 'Democratic Yemeni Dinar',
        'DEM' => 'Deutsche Mark',
        'DJF' => 'Djibouti Franc',
        'DOP' => 'Dominican Peso',
        'NLG' => 'Dutch Guilder',
        'DDM' => 'East German Mark (DDR)',
        'TPE' => 'East Timor Escudo',
        'ECS' => 'Ecuador Sucre',
        'EGP' => 'Egyptian Pound',
        'SVC' => 'El Salvador Colon',
        'ETB' => 'Ethiopian Birr',
        'EUR' => 'Euro',
        'FKP' => 'Falkland Islands Pound',
        'FJD' => 'Fiji Dollar',
        'FIM' => 'Finnish Markka',
        'FRF' => 'French Franc',
        'GMD' => 'Gambian Dalasi',
        'GHC' => 'Ghanaian Cedi',
        'GIP' => 'Gibraltar Pound',
        'GRD' => 'Greek Drachma',
        'GTQ' => 'Guatemalan Quetzal',
        'GNF' => 'Guinea Franc',
        'GWP' => 'Guinea-Bissau Peso',
        'GYD' => 'Guyanan Dollar',
        'HTG' => 'Haitian Gourde',
        'HNL' => 'Honduran Lempira',
        'HKD' => 'Hong Kong Dollar',
        'HUF' => 'Hungarian Forint',
        'ISK' => 'Iceland Krona',
        'INR' => 'Indian Rupee',
        'IDR' => 'Indonesian Rupiah',
        'IRR' => 'Iranian Rial',
        'IQD' => 'Iraqi Dinar',
        'IEP' => 'Irish Punt',
        'ILS' => 'Israeli Shekel',
        'ITL' => 'Italian Lira',
        'JMD' => 'Jamaican Dollar',
        'JPY' => 'Japanese Yen',
        'JOD' => 'Jordanian Dinar',
        'KHR' => 'Kampuchean (Cambodian) Riel',
        'KES' => 'Kenyan Schilling',
        'KWD' => 'Kuwaiti Dinar',
        'LAK' => 'Lao Kip',
        'LBP' => 'Lebanese Pound',
        'LSL' => 'Lesotho Loti',
        'LRD' => 'Liberian Dollar',
        'LYD' => 'Libyan Dinar',
        'LUF' => 'Luxembourg Franc',
        'MOP' => 'Macau Pataca',
        'MGF' => 'Malagasy Franc',
        'MWK' => 'Malawi Kwacha',
        'MYR' => 'Malaysian Ringgit',
        'MVR' => 'Maldive Rufiyaa',
        'MTL' => 'Maltese Lira',
        'MRO' => 'Mauritanian Ouguiya',
        'MUR' => 'Mauritius Rupee',
        'MXP' => 'Mexican Peso',
        'MNT' => 'Mongolian Tugrik',
        'MAD' => 'Moroccan Dirham',
        'MZM' => 'Mozambique Metical',
        'NPR' => 'Nepalese Rupee',
        'ANG' => 'Netherlands Antillian Guilder',
        'YUD' => 'New Yugoslavia Dinar',
        'NZD' => 'New Zealand Dollar',
        'NIC' => 'Nicaraguan Cordoba',
        'NGN' => 'Nigerian Naira',
        'KPW' => 'North Korean Won',
        'NOK' => 'Norwegian Kroner',
        'OMR' => 'Omani Rial',
        'PKR' => 'Pakistan Rupee',
        'PAB' => 'Panamanian Balboa',
        'PGK' => 'Papua New Guinea Kina',
        'PYG' => 'Paraguay Guarani',
        'PEI' => 'Peruvian Inti',
        'PHP' => 'Philippine Peso',
        'PLZ' => 'Polish Zloty',
        'PTE' => 'Portuguese Escudo',
        'QAR' => 'Qatari Rial',
        'ROL' => 'Romanian Leu',
        'RWF' => 'Rwanda Franc',
        'WST' => 'Samoan Tala',
        'STD' => 'Sao Tome and Principe Dobra',
        'SAR' => 'Saudi Arabian Riyal',
        'SCR' => 'Seychelles Rupee',
        'SLL' => 'Sierra Leone Leone',
        'SGD' => 'Singapore Dollar',
        'SBD' => 'Solomon Islands Dollar',
        'SOS' => 'Somali Schilling',
        'ZAR' => 'South African Rand',
        'KRW' => 'South Korean Won',
        'ESP' => 'Spanish Peseta',
        'LKR' => 'Sri Lanka Rupee',
        'SHP' => 'St. Helena Pound',
        'SDP' => 'Sudanese Pound',
        'SRG' => 'Suriname Guilder',
        'SZL' => 'Swaziland Lilangeni',
        'SEK' => 'Swedish Krona',
        'CHF' => 'Swiss Franc',
        'SYP' => 'Syrian Potmd',
        'TWD' => 'Taiwan Dollar',
        'TZS' => 'Tanzanian Schilling',
        'THB' => 'Thai Bhat',
        'TOP' => "Tongan Pa\'anga",
        'TTD' => 'Trinidad and Tobago Dollar',
        'TND' => 'Tunisian Dinar',
        'YTL' => 'Turkish YTL',
        'UGS' => 'Uganda Shilling',
        'AED' => 'United Arab Emirates Dirham',
        'UYP' => 'Uruguayan Peso',
        'USD' => 'US Dollar',
        'SUR' => 'USSR Rouble',
        'VUV' => 'Vanuatu Vatu',
        'VEB' => 'Venezualan Bolivar',
        'VND' => 'Vietnamese Dong',
        'YER' => 'Yemeni Rial',
        'CNY' => 'Yuan (Chinese) Renminbi',
        'ZRZ' => 'Zaire Zaire',
        'ZMK' => 'Zambian Kwacha',
        'ZWD' => 'Zimbabwe Dollar'
    );
    foreach ($currencies as $key => $val) {
        $newcur = new object;
        $newcur->code  = $key;
        $newcur->name  = $val;
        insert_record('currencies', $newcur, false);
    }
}
?>
