<?php

namespace SimpleSAML\Module\ldap\Auth\Process;

use SimpleSAML\Module\ldap\Auth\Ldap;

/**
 * Filter to add attributes to the identity by executing a query against an LDAP directory
 *
 * Original Author: Steve Moitozo II <steve_moitozo@jaars.org>
 * Created: 20100513
 * Updated: 20100920 Steve Moitozo II
 *          - incorporated feedback from Olav Morken to prep code for inclusion in SimpleSAMLphp distro
 *          - moved call to ldap_set_options() inside test for $ds
 *          - added the output of ldap_error() to the exceptions
 *          - reduced some of the nested ifs
 *          - added support for multiple values
 *          - added support for anonymous binds
 *          - added escaping of search filter and attribute
 * Updated: 20111118 Ryan Panning
 *          - Updated the class to use BaseFilter which reuses LDAP connection features
 *          - Added conversion of original filter option names for backwards-compatibility
 *          - Updated the constructor to use the new config method
 *          - Updated the process method to use the new config variable names
 * Updated: 20131119 Yørn de Jong / Jaime Perez
 *          - Added support for retrieving multiple values at once from LDAP
 *          - Don't crash but fail silently on LDAP errors; the plugin is to complement attributes
 * Updated: 20161223 Remy Blom <remy.blom@hku.nl>
 *          - Adjusted the silent fail so it does show a warning in log when $this->getLdap() fails
 *
 * @author Yørn de Jong
 * @author Jaime Perez
 * @author Steve Moitozo
 * @author JAARS, Inc.
 * @author Ryan Panning
 * @author Remy Blom <remy.blom@hku.nl>
 * @package SimpleSAMLphp
 */
class AttributeAddFromLDAP extends BaseFilter
{
    /**
     * LDAP attributes to add to the request attributes
     *
     * @var array
     */
    protected $search_attributes;


    /**
     * LDAP attributes to base64 encode
     *
     * @var array
     */
    protected $binary_attributes;

    /**
     * LDAP search filter to use in the LDAP query
     *
     * @var string
     */
    protected $search_filter;


    /**
     * What to do with attributes when the target already exists. Either replace, merge or add.
     *
     * @var string
     */
    protected $attr_policy;


    /**
     * Initialize this filter.
     *
     * @param array $config Configuration information about this filter.
     * @param mixed $reserved For future use.
     */
    public function __construct($config, $reserved)
    {
        /*
         * For backwards compatibility, check for old config names
         * @TODO Remove after 2.0
         */
        if (isset($config['ldap_host'])) {
            $config['ldap.hostname'] = $config['ldap_host'];
        }
        if (isset($config['ldap_port'])) {
            $config['ldap.port'] = $config['ldap_port'];
        }
        if (isset($config['ldap_bind_user'])) {
            $config['ldap.username'] = $config['ldap_bind_user'];
        }
        if (isset($config['ldap_bind_pwd'])) {
            $config['ldap.password'] = $config['ldap_bind_pwd'];
        }
        if (isset($config['userid_attribute'])) {
            $config['attribute.username'] = $config['userid_attribute'];
        }
        if (isset($config['ldap_search_base_dn'])) {
            $config['ldap.basedn'] = $config['ldap_search_base_dn'];
        }
        if (isset($config['ldap_search_filter'])) {
            $config['search.filter'] = $config['ldap_search_filter'];
        }
        if (isset($config['ldap_search_attribute'])) {
            $config['search.attribute'] = $config['ldap_search_attribute'];
        }
        if (isset($config['new_attribute_name'])) {
            $config['attribute.new'] = $config['new_attribute_name'];
        }

        /*
         * Remove the old config names
         * @TODO Remove after 2.0
         */
        unset(
            $config['ldap_host'],
            $config['ldap_port'],
            $config['ldap_bind_user'],
            $config['ldap_bind_pwd'],
            $config['userid_attribute'],
            $config['ldap_search_base_dn'],
            $config['ldap_search_filter'],
            $config['ldap_search_attribute'],
            $config['new_attribute_name']
        );

        // Now that we checked for BC, run the parent constructor
        parent::__construct($config, $reserved);

        // Get filter specific config options
        $this->binary_attributes = $this->config->getArray('attributes.binary', []);
        $this->search_attributes = $this->config->getArrayize('attributes', []);
        if (empty($this->search_attributes)) {
            $new_attribute = $this->config->getString('attribute.new', '');
            $this->search_attributes[$new_attribute] = $this->config->getString('search.attribute');
        }
        $this->search_filter = $this->config->getString('search.filter');

        // get the attribute policy
        $this->attr_policy = $this->config->getString('attribute.policy', 'merge');
    }


    /**
     * Add attributes from an LDAP server.
     *
     * @param array &$request The current request
     * @return void
     */
    public function process(&$request)
    {
        assert(is_array($request));
        assert(array_key_exists('Attributes', $request));

        $attributes = &$request['Attributes'];

        // perform a merge on the ldap_search_filter
        // loop over the attributes and build the search and replace arrays
        $arrSearch = [];
        $arrReplace = [];
        foreach ($attributes as $attr => $val) {
            $arrSearch[] = '%'.$attr.'%';

            if (strlen($val[0]) > 0) {
                $arrReplace[] = Ldap::escape_filter_value($val[0]);
            } else {
                $arrReplace[] = '';
            }
        }

        // merge the attributes into the ldap_search_filter
        $filter = str_replace($arrSearch, $arrReplace, $this->search_filter);

        if (strpos($filter, '%') !== false) {
            \SimpleSAML\Logger::info('AttributeAddFromLDAP: There are non-existing attributes in the search filter. ('.
                $this->search_filter.')');
            return;
        }

        if (!in_array($this->attr_policy, ['merge', 'replace', 'add'], true)) {
            \SimpleSAML\Logger::warning("AttributeAddFromLDAP: 'attribute.policy' must be one of 'merge',".
                "'replace' or 'add'.");
            return;
        }

        // getLdap
        try {
            $ldap = $this->getLdap();
        } catch (\Exception $e) {
            // Added this warning in case $this->getLdap() fails
            \SimpleSAML\Logger::warning("AttributeAddFromLDAP: exception = ".$e);
            return;
        }
        // search for matching entries
        try {
            $entries = $ldap->searchformultiple(
                $this->base_dn,
                $filter,
                array_values($this->search_attributes),
                $this->binary_attributes,
                true,
                false
            );
        } catch (\Exception $e) {
            return; // silent fail, error is still logged by LDAP search
        }

        // handle [multiple] values
        foreach ($entries as $entry) {
            foreach ($this->search_attributes as $target => $name) {
                if (is_numeric($target)) {
                    $target = $name;
                }

                if (isset($attributes[$target]) && $this->attr_policy === 'replace') {
                    unset($attributes[$target]);
                }
                $name = strtolower($name);
                if (isset($entry[$name])) {
                    unset($entry[$name]['count']);
                    if (isset($attributes[$target])) {
                        foreach (array_values($entry[$name]) as $value) {
                            if ($this->attr_policy === 'merge') {
                                if (!in_array($value, $attributes[$target], true)) {
                                    $attributes[$target][] = $value;
                                }
                            } else {
                                $attributes[$target][] = $value;
                            }
                        }
                    } else {
                        $attributes[$target] = array_values($entry[$name]);
                    }
                }
            }
        }
    }
}
