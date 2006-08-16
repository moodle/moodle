<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-3001 Martin Dougiamas        http://dougiamas.com  //
//           (C) 2001-3001 Eloy Lafuente (stronk7) http://contiento.com  //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/// This class generate SQL code to be used against PostgreSQL
/// It extends XMLDBgenerator so everything can be
/// overriden as needed to generate correct SQL.

class XMLDBpostgres7 extends XMLDBgenerator {

/// Only set values that are different from the defaults present in XMLDBgenerator

    var $number_type = 'NUMERIC';    // Proper type for NUMBER(x) in this DB

    var $unsigned_allowed = false;    // To define in the generator must handle unsigned information
    var $default_for_char = '';      // To define the default to set for NOT NULLs CHARs without default (null=do nothing)

    var $foreign_keys = false; // Does the generator build foreign keys

    var $primary_index = false;// Does the generator need to build one index for primary keys
    var $unique_index = false;  // Does the generator need to build one index for unique keys
    var $foreign_index = true; // Does the generator need to build one index for foreign keys

    var $sequence_extra_code = false; //Does the generator need to add extra code to generate the sequence fields
    var $sequence_name = 'BIGSERIAL'; //Particular name for inline sequences in this generator
    var $sequence_only = true; //To avoid to output the rest of the field specs, leaving only the name and the sequence_name variable

    var $enum_inline_code = false; //Does the generator need to add inline code in the column definition

    /**
     * Creates one new XMLDBpostgres7
     */
    function XMLDBpostgres7() {
        parent::XMLDBgenerator();
        $this->prefix = '';
        $this->reserved_words = $this->getReservedWords();
    }

    /**
     * Given one XMLDB Type, lenght and decimals, returns the DB proper SQL type
     */
    function getTypeSQL ($xmldb_type, $xmldb_length=null, $xmldb_decimals=null) {

        switch ($xmldb_type) {
            case XMLDB_TYPE_INTEGER:    // From http://www.postgresql.org/docs/7.4/interactive/datatype.html
                if (empty($xmldb_length)) {
                    $xmldb_length = 10;
                }
                if ($xmldb_length > 9) {
                    $dbtype = 'BIGINT';
                } else if ($xmldb_length > 4) {
                    $dbtype = 'INTEGER';
                } else {
                    $dbtype = 'SMALLINT';
                }
                break;
            case XMLDB_TYPE_NUMBER:
                $dbtype = $this->number_type;
                if (!empty($xmldb_length)) {
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    }
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_FLOAT:
                $dbtype = 'REAL';
                if (!empty($xmldb_length)) {
                    $dbtype .= '(' . $xmldb_length;
                    if (!empty($xmldb_decimals)) {
                        $dbtype .= ',' . $xmldb_decimals;
                    }
                    $dbtype .= ')';
                }
                break;
            case XMLDB_TYPE_CHAR:
                $dbtype = 'VARCHAR';
                if (!empty($xmldb_length)) {
                    $xmldb_length='255';
                }
                $dbtype .= '(' . $xmldb_length . ')';
                break;
            case XMLDB_TYPE_TEXT:
                $dbtype = 'TEXT';
                break;
            case XMLDB_TYPE_BINARY:
                $dbtype = 'BYTEA';
                break;
            case XMLDB_TYPE_DATETIME:
                $dbtype = 'TIMESTAMP';
                break;
        }
        return $dbtype;
    }

    /**         
     * Returns the code needed to create one enum for the xmldb_table and xmldb_field passes
     */     
    function getEnumExtraSQL ($xmldb_table, $xmldb_field) {
        
        $sql = 'CONSTRAINT ' . $this->getNameForObject($xmldb_table->getName(), $xmldb_field->getName(), 'ck');
        $sql.= ' CHECK (' . $this->getEncQuoted($xmldb_field->getName()) . ' IN (' . implode(', ', $xmldb_field->getEnumValues()) . ')),';

        return $sql;
    }

     /**
      * Returns the code needed to add one comment to the table
      */
     function getCommentSQL ($xmldb_table) {

         $comment = ";\n\nCOMMENT ON TABLE " . $this->getEncQuoted($this->prefix . $xmldb_table->getName());
         $comment.= " IS '" . substr($xmldb_table->getComment(), 0, 250) . "'";

         return $comment;
     }

    /**
     * Returns an array of reserved words (lowercase) for this DB
     */
    function getReservedWords() {
    /// This file contains the reserved words for PostgreSQL databases
    /// from http://www.postgresql.org/docs/7.3/static/sql-keywords-appendix.html
        $reserved_words = array (
            'abort', 'abs', 'absolute', 'access', 'action', 'ada',
            'add', 'admin', 'after', 'aggregate', 'alias', 'all',
            'allocate', 'alter', 'analyse', 'analyze', 'and', 'any',
            'are', 'array', 'as', 'asc', 'asensitive', 'assertion',
            'assignment', 'asymmetric', 'at', 'atomic',
            'authorization', 'avg', 'backward', 'before', 'begin',
            'between', 'bigint', 'binary', 'bit', 'bitvar',
            'bit_length', 'blob', 'boolean', 'both', 'breadth', 'by',
            'c', 'cache', 'call', 'called', 'cardinality', 'cascade',
            'cascaded', 'case', 'cast', 'catalog', 'catalog_name',
            'chain', 'char', 'character', 'characteristics',
            'character_length', 'character_set_catalog',
            'character_set_name', 'character_set_schema',
            'char_length', 'check', 'checked', 'checkpoint', 'class',
            'class_origin', 'clob', 'close', 'cluster', 'coalesce',
            'cobol', 'collate', 'collation', 'collation_catalog',
            'collation_name', 'collation_schema', 'column',
            'column_name', 'command_function',
            'command_function_code', 'comment', 'commit', 'committed',
            'completion', 'condition_number', 'connect', 'connection',
            'connection_name', 'constraint', 'constraints',
            'constraint_catalog', 'constraint_name',
            'constraint_schema', 'constructor', 'contains', 'continue',
            'conversion', 'convert', 'copy', 'corresponding', 'count',
            'create', 'createdb', 'createuser', 'cross', 'cube', 'current',
            'current_date', 'current_path', 'current_role',
            'current_time', 'current_timestamp', 'current_user',
            'cursor', 'cursor_name', 'cycle', 'data', 'database', 'date',
            'datetime_interval_code', 'datetime_interval_precision',
            'day', 'deallocate', 'dec', 'decimal', 'declare', 'default',
            'deferrable', 'deferred', 'defined', 'definer', 'delete',
            'delimiter', 'delimiters', 'depth', 'deref', 'desc', 'describe',
            'descriptor', 'destroy', 'destructor', 'deterministic',
            'diagnostics', 'dictionary', 'disconnect', 'dispatch',
            'distinct', 'do', 'domain', 'double', 'drop', 'dynamic',
            'dynamic_function', 'dynamic_function_code', 'each', 'else',
            'encoding', 'encrypted', 'end', 'end-exec', 'equals', 'escape',
            'every', 'except', 'exception', 'exclusive', 'exec', 'execute',
            'existing', 'exists', 'explain', 'external', 'extract', 'false',
            'fetch', 'final', 'first', 'float', 'for', 'force', 'foreign',
            'fortran', 'forward', 'found', 'free', 'freeze', 'from', 'full',
            'function', 'g', 'general', 'generated', 'get', 'global', 'go',
            'goto', 'grant', 'granted', 'group', 'grouping', 'handler',
            'having', 'hierarchy', 'hold', 'host', 'hour', 'identity',
            'ignore', 'ilike', 'immediate', 'immutable', 'implementation',
            'implicit', 'in', 'increment', 'index', 'indicator', 'infix',
            'inherits', 'initialize', 'initially', 'inner', 'inout',
            'input', 'insensitive', 'insert', 'instance', 'instantiable',
            'instead', 'int', 'integer', 'intersect', 'interval', 'into',
            'invoker', 'is', 'isnull', 'isolation', 'iterate', 'join', 'k',
            'key', 'key_member', 'key_type', 'lancompiler', 'language',
            'large', 'last', 'lateral', 'leading', 'left', 'length', 'less',
            'level', 'like', 'limit', 'listen', 'load', 'local', 'localtime',
            'localtimestamp', 'location', 'locator', 'lock', 'lower', 'm',
            'map', 'match', 'max', 'maxvalue', 'message_length',
            'message_octet_length', 'message_text', 'method', 'min',
            'minute', 'minvalue', 'mod', 'mode', 'modifies', 'modify',
            'module', 'month', 'more', 'move', 'mumps', 'name', 'names',
            'national', 'natural', 'nchar', 'nclob', 'new', 'next', 'no',
            'nocreatedb', 'nocreateuser', 'none', 'not', 'nothing',
            'notify', 'notnull', 'null', 'nullable', 'nullif', 'number',
            'numeric', 'object', 'octet_length', 'of', 'off', 'offset', 'oids',
            'old', 'on', 'only', 'open', 'operation', 'operator', 'option',
            'options', 'or', 'order', 'ordinality', 'out', 'outer', 'output',
            'overlaps', 'overlay', 'overriding', 'owner', 'pad',
            'parameter', 'parameters', 'parameter_mode',
            'parameter_name', 'parameter_ordinal_position',
            'parameter_specific_catalog', 'parameter_specific_name',
            'parameter_specific_schema', 'partial', 'pascal',
            'password', 'path', 'pendant', 'placing', 'pli', 'position',
            'postfix', 'precision', 'prefix', 'preorder', 'prepare',
            'preserve', 'primary', 'prior', 'privileges', 'procedural',
            'procedure', 'public', 'read', 'reads', 'real', 'recheck',
            'recursive', 'ref', 'references', 'referencing', 'reindex',
            'relative', 'rename', 'repeatable', 'replace', 'reset',
            'restrict', 'result', 'return', 'returned_length',
            'returned_octet_length', 'returned_sqlstate', 'returns',
            'revoke', 'right', 'role', 'rollback', 'rollup', 'routine',
            'routine_catalog', 'routine_name', 'routine_schema', 'row',
            'rows', 'row_count', 'rule', 'savepoint', 'scale', 'schema',
            'schema_name', 'scope', 'scroll', 'search', 'second', 'section',
            'security', 'select', 'self', 'sensitive', 'sequence',
            'serializable', 'server_name', 'session', 'session_user',
            'set', 'setof', 'sets', 'share', 'show', 'similar', 'simple', 'size',
            'smallint', 'some', 'source', 'space', 'specific',
            'specifictype', 'specific_name', 'sql', 'sqlcode', 'sqlerror',
            'sqlexception', 'sqlstate', 'sqlwarning', 'stable', 'start',
            'state', 'statement', 'static', 'statistics', 'stdin', 'stdout',
            'storage', 'strict', 'structure', 'style', 'subclass_origin',
            'sublist', 'substring', 'sum', 'symmetric', 'sysid', 'system',
            'system_user', 'table', 'table_name', 'temp', 'template',
            'temporary', 'terminate', 'than', 'then', 'time', 'timestamp',
            'timezone_hour', 'timezone_minute', 'to', 'toast', 'trailing',
            'transaction', 'transactions_committed',
            'transactions_rolled_back', 'transaction_active',
            'transform', 'transforms', 'translate', 'translation',
            'treat', 'trigger', 'trigger_catalog', 'trigger_name',
            'trigger_schema', 'trim', 'true', 'truncate', 'trusted', 'type',
            'uncommitted', 'under', 'unencrypted', 'union', 'unique',
            'unknown', 'unlisten', 'unnamed', 'unnest', 'until', 'update',
            'upper', 'usage', 'user', 'user_defined_type_catalog',
            'user_defined_type_name', 'user_defined_type_schema',
            'using', 'vacuum', 'valid', 'validator', 'value', 'values',
            'varchar', 'variable', 'varying', 'verbose', 'version', 'view',
            'volatile', 'when', 'whenever', 'where', 'with', 'without', 'work',
            'write', 'year', 'zone'
        );  
        return $reserved_words;
    }
}

?>
