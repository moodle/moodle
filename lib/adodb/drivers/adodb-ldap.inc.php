<?php
/*
  V4.98 13 Feb 2008  (c) 2000-2008 John Lim (jlim#natsoft.com.my). All rights reserved.
   Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
  
  Revision 1: (02/25/2005) Updated codebase to include the _inject_bind_options function. This allows
  users to access the options in the ldap_set_option function appropriately. Most importantly
  LDAP Version 3 is now supported. See the examples for more information. Also fixed some minor
  bugs that surfaced when PHP error levels were set high.
  
  Joshua Eldridge (joshuae74#hotmail.com)
*/ 

// security - hide paths
if (!defined('ADODB_DIR')) die();

if (!defined('LDAP_ASSOC')) {
	 define('LDAP_ASSOC',ADODB_FETCH_ASSOC);
	 define('LDAP_NUM',ADODB_FETCH_NUM);
	 define('LDAP_BOTH',ADODB_FETCH_BOTH);
}

class ADODB_ldap extends ADOConnection {
    var $databaseType = 'ldap';
	var $dataProvider = 'ldap';
	
	# Connection information
    var $username = false;
    var $password = false;
    
    # Used during searches
    var $filter;
    var $dn;
	var $version;
	var $port = 389;

	# Options configuration information
	var $LDAP_CONNECT_OPTIONS;
	
	# error on binding, eg. "Binding: invalid credentials"
	var $_bind_errmsg = "Binding: %s";

	function ADODB_ldap() 
	{		
	}
  		
	// returns true or false
	function _connect( $host, $username, $password, $ldapbase)
	{
	global $LDAP_CONNECT_OPTIONS;
		
		if ( !function_exists( 'ldap_connect' ) ) return null;
		
		$conn_info = array( $host,$this->port);
		
		if ( strstr( $host, ':' ) ) {
		    $conn_info = split( ':', $host );
		} 
		
		$this->_connectionID = @ldap_connect( $conn_info[0], $conn_info[1] );
		if (!$this->_connectionID) {
			$e = 'Could not connect to ' . $conn_info[0];
			$this->_errorMsg = $e;
			if ($this->debug) ADOConnection::outp($e);
			return false;
		}
		if( count( $LDAP_CONNECT_OPTIONS ) > 0 ) {
			$this->_inject_bind_options( $LDAP_CONNECT_OPTIONS );
		}
		
		if ($username) {
		    $bind = @ldap_bind( $this->_connectionID, $username, $password );
		} else {
			$username = 'anonymous';
		    $bind = @ldap_bind( $this->_connectionID );		
		}
		
		if (!$bind) {
			$e = sprintf($this->_bind_errmsg,ldap_error($this->_connectionID));;
			$this->_errorMsg = $e;
			if ($this->debug) ADOConnection::outp($e);
			return false;
		}
		$this->_errorMsg = '';
		$this->database = $ldapbase;
		return $this->_connectionID;
	}
    
/*
	Valid Domain Values for LDAP Options:

	LDAP_OPT_DEREF (integer)
	LDAP_OPT_SIZELIMIT (integer)
	LDAP_OPT_TIMELIMIT (integer)
	LDAP_OPT_PROTOCOL_VERSION (integer)
	LDAP_OPT_ERROR_NUMBER (integer)
	LDAP_OPT_REFERRALS (boolean)
	LDAP_OPT_RESTART (boolean)
	LDAP_OPT_HOST_NAME (string)
	LDAP_OPT_ERROR_STRING (string)
	LDAP_OPT_MATCHED_DN (string)
	LDAP_OPT_SERVER_CONTROLS (array)
	LDAP_OPT_CLIENT_CONTROLS (array)

	Make sure to set this BEFORE calling Connect()

	Example:

	$LDAP_CONNECT_OPTIONS = Array(
		Array (
			"OPTION_NAME"=>LDAP_OPT_DEREF,
			"OPTION_VALUE"=>2
		),
		Array (
			"OPTION_NAME"=>LDAP_OPT_SIZELIMIT,
			"OPTION_VALUE"=>100
		),
		Array (
			"OPTION_NAME"=>LDAP_OPT_TIMELIMIT,
			"OPTION_VALUE"=>30
		),
		Array (
			"OPTION_NAME"=>LDAP_OPT_PROTOCOL_VERSION,
			"OPTION_VALUE"=>3
		),
		Array (
			"OPTION_NAME"=>LDAP_OPT_ERROR_NUMBER,
			"OPTION_VALUE"=>13
		),
		Array (
			"OPTION_NAME"=>LDAP_OPT_REFERRALS,
			"OPTION_VALUE"=>FALSE
		),
		Array (
			"OPTION_NAME"=>LDAP_OPT_RESTART,
			"OPTION_VALUE"=>FALSE
		)
	);
*/

	function _inject_bind_options( $options ) {
		foreach( $options as $option ) {
			ldap_set_option( $this->_connectionID, $option["OPTION_NAME"], $option["OPTION_VALUE"] )
				or die( "Unable to set server option: " . $option["OPTION_NAME"] );
		}
	}
	
	/* returns _queryID or false */
	function _query($sql,$inputarr)
	{
		$rs = @ldap_search( $this->_connectionID, $this->database, $sql );
		$this->_errorMsg = ($rs) ? '' : 'Search error on '.$sql.': '. ldap_error($this->_connectionID);
		return $rs; 
	}
	
	function ErrorMsg()
	{
		return $this->_errorMsg;
	}
	
	function ErrorNo()
	{
		return @ldap_errno($this->_connectionID);
	}

    /* closes the LDAP connection */
	function _close()
	{
		@ldap_close( $this->_connectionID );
		$this->_connectionID = false;
	}
    
	function SelectDB($db) {
		$this->database = $db;
		return true;
	} // SelectDB

    function ServerInfo()
    {
        if( !empty( $this->version ) ) return $this->version;
        $version = array();
        /*
        Determines how aliases are handled during search. 
        LDAP_DEREF_NEVER (0x00)
        LDAP_DEREF_SEARCHING (0x01)
        LDAP_DEREF_FINDING (0x02)
        LDAP_DEREF_ALWAYS (0x03)
        The LDAP_DEREF_SEARCHING value means aliases are dereferenced during the search but 
        not when locating the base object of the search. The LDAP_DEREF_FINDING value means 
        aliases are dereferenced when locating the base object but not during the search.  
        Default: LDAP_DEREF_NEVER
        */
        ldap_get_option( $this->_connectionID, LDAP_OPT_DEREF, $version['LDAP_OPT_DEREF'] ) ;
        switch ( $version['LDAP_OPT_DEREF'] ) {
          case 0:
            $version['LDAP_OPT_DEREF'] = 'LDAP_DEREF_NEVER';
          case 1:
            $version['LDAP_OPT_DEREF'] = 'LDAP_DEREF_SEARCHING';
          case 2:
            $version['LDAP_OPT_DEREF'] = 'LDAP_DEREF_FINDING';
          case 3:
            $version['LDAP_OPT_DEREF'] = 'LDAP_DEREF_ALWAYS';
        }
        
        /* 
        A limit on the number of entries to return from a search. 
        LDAP_NO_LIMIT (0) means no limit.
        Default: LDAP_NO_LIMIT
        */
        ldap_get_option( $this->_connectionID, LDAP_OPT_SIZELIMIT, $version['LDAP_OPT_SIZELIMIT'] );
        if ( $version['LDAP_OPT_SIZELIMIT'] == 0 ) {
           $version['LDAP_OPT_SIZELIMIT'] = 'LDAP_NO_LIMIT';
        }
        
        /*
        A limit on the number of seconds to spend on a search. 
        LDAP_NO_LIMIT (0) means no limit.
        Default: LDAP_NO_LIMIT
        */
        ldap_get_option( $this->_connectionID, LDAP_OPT_TIMELIMIT, $version['LDAP_OPT_TIMELIMIT'] );
        if ( $version['LDAP_OPT_TIMELIMIT'] == 0 ) {
           $version['LDAP_OPT_TIMELIMIT'] = 'LDAP_NO_LIMIT';
        }
        
        /*
        Determines whether the LDAP library automatically follows referrals returned by LDAP servers or not. 
        LDAP_OPT_ON
        LDAP_OPT_OFF
        Default: ON
        */
        ldap_get_option( $this->_connectionID, LDAP_OPT_REFERRALS, $version['LDAP_OPT_REFERRALS'] );
        if ( $version['LDAP_OPT_REFERRALS'] == 0 ) {
           $version['LDAP_OPT_REFERRALS'] = 'LDAP_OPT_OFF';
        } else {
           $version['LDAP_OPT_REFERRALS'] = 'LDAP_OPT_ON';
        
        }
        /*
        Determines whether LDAP I/O operations are automatically restarted if they abort prematurely. 
        LDAP_OPT_ON
        LDAP_OPT_OFF
        Default: OFF
        */
        ldap_get_option( $this->_connectionID, LDAP_OPT_RESTART, $version['LDAP_OPT_RESTART'] );
        if ( $version['LDAP_OPT_RESTART'] == 0 ) {
           $version['LDAP_OPT_RESTART'] = 'LDAP_OPT_OFF';
        } else {
           $version['LDAP_OPT_RESTART'] = 'LDAP_OPT_ON';
        
        }
        /*
        This option indicates the version of the LDAP protocol used when communicating with the primary LDAP server.
        LDAP_VERSION2 (2)
        LDAP_VERSION3 (3)
        Default: LDAP_VERSION2 (2)
        */
        ldap_get_option( $this->_connectionID, LDAP_OPT_PROTOCOL_VERSION, $version['LDAP_OPT_PROTOCOL_VERSION'] );
        if ( $version['LDAP_OPT_PROTOCOL_VERSION'] == 2 ) {
           $version['LDAP_OPT_PROTOCOL_VERSION'] = 'LDAP_VERSION2';
        } else {
           $version['LDAP_OPT_PROTOCOL_VERSION'] = 'LDAP_VERSION3';
        
        }
        /* The host name (or list of hosts) for the primary LDAP server. */
        ldap_get_option( $this->_connectionID, LDAP_OPT_HOST_NAME, $version['LDAP_OPT_HOST_NAME'] ); 
        ldap_get_option( $this->_connectionID, LDAP_OPT_ERROR_NUMBER, $version['LDAP_OPT_ERROR_NUMBER'] ); 
        ldap_get_option( $this->_connectionID, LDAP_OPT_ERROR_STRING, $version['LDAP_OPT_ERROR_STRING'] ); 
        ldap_get_option( $this->_connectionID, LDAP_OPT_MATCHED_DN, $version['LDAP_OPT_MATCHED_DN'] ); 
        
        return $this->version = $version;
    
    }
}
	
/*--------------------------------------------------------------------------------------
	 Class Name: Recordset
--------------------------------------------------------------------------------------*/

class ADORecordSet_ldap extends ADORecordSet{	
	
	var $databaseType = "ldap";
	var $canSeek = false;
	var $_entryID; /* keeps track of the entry resource identifier */
	
	function ADORecordSet_ldap($queryID,$mode=false) 
	{
		if ($mode === false) { 
			global $ADODB_FETCH_MODE;
			$mode = $ADODB_FETCH_MODE;
		}
		switch ($mode)
		{
		case ADODB_FETCH_NUM: 
		  $this->fetchMode = LDAP_NUM; 
		break;
		case ADODB_FETCH_ASSOC: 
		  $this->fetchMode = LDAP_ASSOC; 
		break;
		case ADODB_FETCH_DEFAULT:
		case ADODB_FETCH_BOTH: 
		default:
		  $this->fetchMode = LDAP_BOTH; 
		break;
		}
	
		$this->ADORecordSet($queryID);	
	}
	
	function _initrs()
	{
	   /* 
	   This could be teaked to respect the $COUNTRECS directive from ADODB
	   It's currently being used in the _fetch() function and the
	   GetAssoc() function
       */
	    $this->_numOfRows = ldap_count_entries( $this->connection->_connectionID, $this->_queryID );

	}

    /*
    Return whole recordset as a multi-dimensional associative array
	*/
	function &GetAssoc($force_array = false, $first2cols = false) 
	{
		$records = $this->_numOfRows;
        $results = array();
            for ( $i=0; $i < $records; $i++ ) {
                foreach ( $this->fields as $k=>$v ) {
                    if ( is_array( $v ) ) {
                        if ( $v['count'] == 1 ) {
                            $results[$i][$k] = $v[0];
                        } else {
                            array_shift( $v );
                            $results[$i][$k] = $v;
                        } 
                    }
                }
            }
        
		return $results; 
	}
    
    function &GetRowAssoc()
	{
        $results = array();
        foreach ( $this->fields as $k=>$v ) {
            if ( is_array( $v ) ) {
                if ( $v['count'] == 1 ) {
                    $results[$k] = $v[0];
                } else {
                    array_shift( $v );
                    $results[$k] = $v;
                } 
            }
        }
 
		return $results; 
	}
		
    function GetRowNums()
    {
        $results = array();
        foreach ( $this->fields as $k=>$v ) {
        static $i = 0;
            if (is_array( $v )) {
                if ( $v['count'] == 1 ) {
                    $results[$i] = $v[0];
                } else {
                    array_shift( $v );
                    $results[$i] = $v;
                } 
            $i++;
            }
        }
        return $results;
    }
	
	function _fetch()
	{		
		if ( $this->_currentRow >= $this->_numOfRows && $this->_numOfRows >= 0 )
        	return false;
        	
        if ( $this->_currentRow == 0 ) {
		  $this->_entryID = ldap_first_entry( $this->connection->_connectionID, $this->_queryID );
        } else {
          $this->_entryID = ldap_next_entry( $this->connection->_connectionID, $this->_entryID );
        }
	    
	    $this->fields = ldap_get_attributes( $this->connection->_connectionID, $this->_entryID );
	    $this->_numOfFields = $this->fields['count'];	
	    switch ( $this->fetchMode ) {
            
            case LDAP_ASSOC:
            $this->fields = $this->GetRowAssoc();
            break;
            
            case LDAP_NUM:
			$this->fields = array_merge($this->GetRowNums(),$this->GetRowAssoc());
            break;
            
            case LDAP_BOTH:
            default:
			$this->fields = $this->GetRowNums();
            break;
        }
        return ( is_array( $this->fields ) );        
	}
	
	function _close() {
		@ldap_free_result( $this->_queryID );	
		$this->_queryID = false;
	}
	
}
?>