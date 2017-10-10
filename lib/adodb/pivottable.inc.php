<?php
/**
 * @version   v5.20.9  21-Dec-2016
 * @copyright (c) 2000-2013 John Lim (jlim#natsoft.com). All rights reserved.
 * @copyright (c) 2014      Damien Regad, Mark Newnham and the ADOdb community
 * Released under both BSD license and Lesser GPL library license.
 * Whenever there is any discrepancy between the two licenses,
 * the BSD license will take precedence.
 *
 * Set tabs to 4 for best viewing.
 *
*/

/*
 * Concept from daniel.lucazeau@ajornet.com.
 *
 * @param db		Adodb database connection
 * @param tables	List of tables to join
 * @rowfields		List of fields to display on each row
 * @colfield		Pivot field to slice and display in columns, if we want to calculate
 *						ranges, we pass in an array (see example2)
 * @where			Where clause. Optional.
 * @aggfield		This is the field to sum. Optional.
 *						Since 2.3.1, if you can use your own aggregate function
 *						instead of SUM, eg. $aggfield = 'fieldname'; $aggfn = 'AVG';
 * @sumlabel		Prefix to display in sum columns. Optional.
 * @aggfn			Aggregate function to use (could be AVG, SUM, COUNT)
 * @showcount		Show count of records
 *
 * @returns			Sql generated
 */

 function PivotTableSQL(&$db,$tables,$rowfields,$colfield, $where=false,
 	$aggfield = false,$sumlabel='Sum ',$aggfn ='SUM', $showcount = true)
 {
	if ($aggfield) $hidecnt = true;
	else $hidecnt = false;

	$iif = strpos($db->databaseType,'access') !== false;
		// note - vfp 6 still doesn' work even with IIF enabled || $db->databaseType == 'vfp';

	//$hidecnt = false;

 	if ($where) $where = "\nWHERE $where";
	if (!is_array($colfield)) $colarr = $db->GetCol("select distinct $colfield from $tables $where order by 1");
	if (!$aggfield) $hidecnt = false;

	$sel = "$rowfields, ";
	if (is_array($colfield)) {
		foreach ($colfield as $k => $v) {
			$k = trim($k);
			if (!$hidecnt) {
				$sel .= $iif ?
					"\n\t$aggfn(IIF($v,1,0)) AS \"$k\", "
					:
					"\n\t$aggfn(CASE WHEN $v THEN 1 ELSE 0 END) AS \"$k\", ";
			}
			if ($aggfield) {
				$sel .= $iif ?
					"\n\t$aggfn(IIF($v,$aggfield,0)) AS \"$sumlabel$k\", "
					:
					"\n\t$aggfn(CASE WHEN $v THEN $aggfield ELSE 0 END) AS \"$sumlabel$k\", ";
			}
		}
	} else {
		foreach ($colarr as $v) {
			if (!is_numeric($v)) $vq = $db->qstr($v);
			else $vq = $v;
			$v = trim($v);
			if (strlen($v) == 0	) $v = 'null';
			if (!$hidecnt) {
				$sel .= $iif ?
					"\n\t$aggfn(IIF($colfield=$vq,1,0)) AS \"$v\", "
					:
					"\n\t$aggfn(CASE WHEN $colfield=$vq THEN 1 ELSE 0 END) AS \"$v\", ";
			}
			if ($aggfield) {
				if ($hidecnt) $label = $v;
				else $label = "{$v}_$aggfield";
				$sel .= $iif ?
					"\n\t$aggfn(IIF($colfield=$vq,$aggfield,0)) AS \"$label\", "
					:
					"\n\t$aggfn(CASE WHEN $colfield=$vq THEN $aggfield ELSE 0 END) AS \"$label\", ";
			}
		}
	}
	if ($aggfield && $aggfield != '1'){
		$agg = "$aggfn($aggfield)";
		$sel .= "\n\t$agg as \"$sumlabel$aggfield\", ";
	}

	if ($showcount)
		$sel .= "\n\tSUM(1) as Total";
	else
		$sel = substr($sel,0,strlen($sel)-2);


	// Strip aliases
	$rowfields = preg_replace('/ AS (\w+)/i', '', $rowfields);

	$sql = "SELECT $sel \nFROM $tables $where \nGROUP BY $rowfields";

	return $sql;
 }

/* EXAMPLES USING MS NORTHWIND DATABASE */
if (0) {

# example1
#
# Query the main "product" table
# Set the rows to CompanyName and QuantityPerUnit
# and the columns to the Categories
# and define the joins to link to lookup tables
# "categories" and "suppliers"
#

 $sql = PivotTableSQL(
 	$gDB,  											# adodb connection
 	'products p ,categories c ,suppliers s',  		# tables
	'CompanyName,QuantityPerUnit',					# row fields
	'CategoryName',									# column fields
	'p.CategoryID = c.CategoryID and s.SupplierID= p.SupplierID' # joins/where
);
 print "<pre>$sql";
 $rs = $gDB->Execute($sql);
 rs2html($rs);

/*
Generated SQL:

SELECT CompanyName,QuantityPerUnit,
	SUM(CASE WHEN CategoryName='Beverages' THEN 1 ELSE 0 END) AS "Beverages",
	SUM(CASE WHEN CategoryName='Condiments' THEN 1 ELSE 0 END) AS "Condiments",
	SUM(CASE WHEN CategoryName='Confections' THEN 1 ELSE 0 END) AS "Confections",
	SUM(CASE WHEN CategoryName='Dairy Products' THEN 1 ELSE 0 END) AS "Dairy Products",
	SUM(CASE WHEN CategoryName='Grains/Cereals' THEN 1 ELSE 0 END) AS "Grains/Cereals",
	SUM(CASE WHEN CategoryName='Meat/Poultry' THEN 1 ELSE 0 END) AS "Meat/Poultry",
	SUM(CASE WHEN CategoryName='Produce' THEN 1 ELSE 0 END) AS "Produce",
	SUM(CASE WHEN CategoryName='Seafood' THEN 1 ELSE 0 END) AS "Seafood",
	SUM(1) as Total
FROM products p ,categories c ,suppliers s  WHERE p.CategoryID = c.CategoryID and s.SupplierID= p.SupplierID
GROUP BY CompanyName,QuantityPerUnit
*/
//=====================================================================

# example2
#
# Query the main "product" table
# Set the rows to CompanyName and QuantityPerUnit
# and the columns to the UnitsInStock for diiferent ranges
# and define the joins to link to lookup tables
# "categories" and "suppliers"
#
 $sql = PivotTableSQL(
 	$gDB,										# adodb connection
 	'products p ,categories c ,suppliers s',	# tables
	'CompanyName,QuantityPerUnit',				# row fields
												# column ranges
array(
' 0 ' => 'UnitsInStock <= 0',
"1 to 5" => '0 < UnitsInStock and UnitsInStock <= 5',
"6 to 10" => '5 < UnitsInStock and UnitsInStock <= 10',
"11 to 15"  => '10 < UnitsInStock and UnitsInStock <= 15',
"16+" =>'15 < UnitsInStock'
),
	' p.CategoryID = c.CategoryID and s.SupplierID= p.SupplierID', # joins/where
	'UnitsInStock', 							# sum this field
	'Sum'										# sum label prefix
);
 print "<pre>$sql";
 $rs = $gDB->Execute($sql);
 rs2html($rs);
 /*
 Generated SQL:

SELECT CompanyName,QuantityPerUnit,
	SUM(CASE WHEN UnitsInStock <= 0 THEN UnitsInStock ELSE 0 END) AS "Sum  0 ",
	SUM(CASE WHEN 0 < UnitsInStock and UnitsInStock <= 5 THEN UnitsInStock ELSE 0 END) AS "Sum 1 to 5",
	SUM(CASE WHEN 5 < UnitsInStock and UnitsInStock <= 10 THEN UnitsInStock ELSE 0 END) AS "Sum 6 to 10",
	SUM(CASE WHEN 10 < UnitsInStock and UnitsInStock <= 15 THEN UnitsInStock ELSE 0 END) AS "Sum 11 to 15",
	SUM(CASE WHEN 15 < UnitsInStock THEN UnitsInStock ELSE 0 END) AS "Sum 16+",
	SUM(UnitsInStock) AS "Sum UnitsInStock",
	SUM(1) as Total
FROM products p ,categories c ,suppliers s  WHERE  p.CategoryID = c.CategoryID and s.SupplierID= p.SupplierID
GROUP BY CompanyName,QuantityPerUnit
 */
}
