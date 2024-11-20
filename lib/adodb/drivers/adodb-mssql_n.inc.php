<?php
/**
 * MSSQL Driver with auto-prepended "N" for correct unicode storage of SQL literal strings.
 *
 * Intended to be used with MSSQL drivers that are sending UCS-2 data to MSSQL
 * (FreeTDS and ODBTP) in order to get true cross-db compatibility from the
 * application point of view.
 *
 * This file is part of ADOdb, a Database Abstraction Layer library for PHP.
 *
 * @package ADOdb
 * @link https://adodb.org Project's web site and documentation
 * @link https://github.com/ADOdb/ADOdb Source code and issue tracker
 *
 * The ADOdb Library is dual-licensed, released under both the BSD 3-Clause
 * and the GNU Lesser General Public Licence (LGPL) v2.1 or, at your option,
 * any later version. This means you can use it in proprietary products.
 * See the LICENSE.md file distributed with this source code for details.
 * @license BSD-3-Clause
 * @license LGPL-2.1-or-later
 *
 * @copyright 2000-2013 John Lim
 * @copyright 2014 Damien Regad, Mark Newnham and the ADOdb community
 */

// security - hide paths
if (!defined('ADODB_DIR')) die();

// one useful constant
if (!defined('SINGLEQUOTE')) define('SINGLEQUOTE', "'");

include_once(ADODB_DIR.'/drivers/adodb-mssql.inc.php');

class ADODB_mssql_n extends ADODB_mssql {
	var $databaseType = "mssql_n";

	function _query($sql,$inputarr=false)
	{
        $sql = $this->_appendN($sql);
		return ADODB_mssql::_query($sql,$inputarr);
	}

	/**
     * This function will intercept all the literals used in the SQL, prepending the "N" char to them
     * in order to allow mssql to store properly data sent in the correct UCS-2 encoding (by freeTDS
     * and ODBTP) keeping SQL compatibility at ADOdb level (instead of hacking every project to add
     * the "N" notation when working against MSSQL.
     *
     * The original note indicated that this hack should only be used if ALL the char-based columns
     * in your DB are of type nchar, nvarchar and ntext, but testing seems to indicate that SQL server
     * doesn't seem to care if the statement is used against char etc fields.
     *
     * @todo This function should raise an ADOdb error if one of the transformations fail
     *
     * @param mixed $inboundData Either a string containing an SQL statement
     *                           or an array with resources from prepared statements
     *
     * @return mixed
     */
    function _appendN($inboundData) {

        $inboundIsArray  = false;

        if (is_array($inboundData))
        {
            $inboundIsArray = true;
            $inboundArray   = $inboundData;
        } else
            $inboundArray = (array)$inboundData;

        /*
         * All changes will be placed here
         */
        $outboundArray = $inboundArray;

        foreach($inboundArray as $inboundKey=>$inboundValue)
        {

            if (is_resource($inboundValue))
            {
                /*
                * Prepared statement resource
                */
                if ($this->debug)
                    ADOConnection::outp("{$this->databaseType} index $inboundKey value is resource, continue");

                continue;
            }

            if (strpos($inboundValue, SINGLEQUOTE) === false)
            {
                /*
                * Check we have something to manipulate
                */
                if ($this->debug)
                    ADOConnection::outp("{$this->databaseType} index $inboundKey value $inboundValue has no single quotes, continue");
                continue;
            }

            /*
            * Check we haven't an odd number of single quotes (this can cause problems below
            * and should be considered one wrong SQL). Exit with debug info.
            */
            if ((substr_count($inboundValue, SINGLEQUOTE) & 1))
            {
                if ($this->debug)
                    ADOConnection::outp("{$this->databaseType} internal transformation: not converted. Wrong number of quotes (odd)");

                break;
            }

            /*
            * Check we haven't any backslash + single quote combination. It should mean wrong
            *  backslashes use (bad magic_quotes_sybase?). Exit with debug info.
            */
            $regexp = '/(\\\\' . SINGLEQUOTE . '[^' . SINGLEQUOTE . '])/';
            if (preg_match($regexp, $inboundValue))
            {
                if ($this->debug)
                    ADOConnection::outp("{$this->databaseType} internal transformation: not converted. Found bad use of backslash + single quote");

                break;
            }

            /*
            * Remove pairs of single-quotes
            */
            $pairs = array();
            $regexp = '/(' . SINGLEQUOTE . SINGLEQUOTE . ')/';
            preg_match_all($regexp, $inboundValue, $list_of_pairs);

            if ($list_of_pairs)
            {
                foreach (array_unique($list_of_pairs[0]) as $key=>$value)
                    $pairs['<@#@#@PAIR-'.$key.'@#@#@>'] = $value;


                if (!empty($pairs))
                    $inboundValue = str_replace($pairs, array_keys($pairs), $inboundValue);

            }

            /*
            * Remove the rest of literals present in the query
            */
            $literals = array();
            $regexp = '/(N?' . SINGLEQUOTE . '.*?' . SINGLEQUOTE . ')/is';
            preg_match_all($regexp, $inboundValue, $list_of_literals);

           if ($list_of_literals)
           {
                foreach (array_unique($list_of_literals[0]) as $key=>$value)
                    $literals['<#@#@#LITERAL-'.$key.'#@#@#>'] = $value;


                if (!empty($literals))
                    $inboundValue = str_replace($literals, array_keys($literals), $inboundValue);
            }

            /*
            * Analyse literals to prepend the N char to them if their contents aren't numeric
            */
            if (!empty($literals))
            {
                foreach ($literals as $key=>$value) {
                    if (!is_numeric(trim($value, SINGLEQUOTE)))
                        /*
                        * Non numeric string, prepend our dear N, whilst
                        * Trimming potentially existing previous "N"
                        */
                        $literals[$key] = 'N' . trim($value, 'N');

                }
            }

            /*
            * Re-apply literals to the text
            */
            if (!empty($literals))
                $inboundValue = str_replace(array_keys($literals), $literals, $inboundValue);


            /*
            * Any pairs followed by N' must be switched to N' followed by those pairs
            * (or strings beginning with single quotes will fail)
            */
            $inboundValue = preg_replace("/((<@#@#@PAIR-(\d+)@#@#@>)+)N'/", "N'$1", $inboundValue);

            /*
            * Re-apply pairs of single-quotes to the text
            */
            if (!empty($pairs))
                $inboundValue = str_replace(array_keys($pairs), $pairs, $inboundValue);


            /*
            * Print transformation if debug = on
            */
            if (strcmp($inboundValue,$inboundArray[$inboundKey]) <> 0 && $this->debug)
                ADOConnection::outp("{$this->databaseType} internal transformation: {$inboundArray[$inboundKey]} to {$inboundValue}");

            if (strcmp($inboundValue,$inboundArray[$inboundKey]) <> 0)
                /*
                * Place the transformed value into the outbound array
                */
                $outboundArray[$inboundKey] = $inboundValue;
        }

        /*
         * Any transformations are in the $outboundArray
         */
        if ($inboundIsArray)
            return $outboundArray;

        /*
         * We passed a string in originally
         */
        return $outboundArray[0];

    }

}

class ADORecordset_mssql_n extends ADORecordset_mssql {
	var $databaseType = "mssql_n";
}
