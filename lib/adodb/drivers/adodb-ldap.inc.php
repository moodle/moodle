<?php
/*
  V4.51 29 July 2004  (c) 2000-2004 John Lim (jlim#natsoft.com.my). All rights reserved.
   Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence.
  Set tabs to 8.
  
  
  Joshua Eldridge (joshuae74#hotmail.com)
*/ 

// security - hide paths
if (!defined('ADODB_DIR')) die();

class ADODB_ldap extends ADOConnection {
    var $databaseType = 'ldap';
	var $dataProvider = 'ldap';
	
	# Connection information
    var $username = false;
    var $password = false;
    
    # Used during searches
    var $filter;
    var $dn;


	function ADODB_ldap() 
	{		

	}
  		
	// returns true or false
	
	function _connect( $host, $username, $password, $ldapbase )
	{

	   if ( !function_exists( 'ldap_connect' ) ) return null;
	   
	   $conn_info = array( $host );
	   
	   if ( strstr( $host, ':' ) ) {
	       $conn_info = split( ':', $host );
	   } 

	   $this->_connectionID = ldap_connect( $conn_info[0], $conn_info[1] ) 
	       or die( 'Could not connect to ' . $this->_connectionID );
	   if ($username && $password) {
	       $bind = ldap_bind( $this->_connectionID, $username, $password ) 
	           or die( 'Could not bind to ' . $this->_connectionID . ' with $username & $password');
	   } else {
	       $bind = ldap_bind( $this->_connectionID ) 
	           or die( 'Could not bind anonymously to ' . $this->_connectionID );
	   }
	   return $this->_connectionID;
    }
    
	
	/* returns _queryID or false */
	function _query($sql,$inputarr)
	{
	   $rs = ldap_search( $this->_connectionID, $this->database, $sql );
       return $rs; 
		
	}

    /* closes the LDAP connection */
	function _close()
	{
		@ldap_close( $this->_connectionID );
		$this->_connectionID = false;
	}
    
    function ServerInfo()
    {
        if( is_array( $this->version ) ) return $this->version;
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
        ldap_get_option( $this->_connectionID, OPT_ERROR_NUMBER, $version['OPT_ERROR_NUMBER'] ); 
        ldap_get_option( $this->_connectionID, OPT_ERROR_STRING, $version['OPT_ERROR_STRING'] ); 
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
		default:
		case ADODB_FETCH_DEFAULT:
		case ADODB_FETCH_BOTH: 
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
            $this->fields = $this->GetRowNums();
            break;
            
            case LDAP_BOTH:
            default:
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