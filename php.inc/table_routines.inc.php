<?php
function print_show_tables(&$connection, $database, $print_sql = TRUE) {
	if($print_sql) {
		echo "<pre><span class='keyword'>SHOW TABLES IN </span><span class='punct'> `$database`;</span></pre>";
	}
	$result = $connection->query("SHOW TABLES IN `$database`;");
	if($result->num_rows > 0) {
		echo <<<TH
    <table>
        <tr>
            <th>Tables_in_$database</th>
        </tr>
TH;
		while($row = $result->fetch_assoc()) {
			echo <<<TR
        <tr>
            <td>{$row["Tables_in_$database"]}</td>
        </tr>
TR;
		}
		echo "    </table>\n";
	} else {
		echo "<span class='error'There's no tables in $database!</span>";
	}
}
function print_describe(&$connection, $table) {
	echo "<pre><span class='keyword'>DESCRIBE</span> $table<span class='punct'>;</span></pre>";
	$result = $connection->query("DESCRIBE $table;");
	if($result->num_rows > 0) {
		echo <<<TH
    <table>
        <tr>
            <th>Field</th>
            <th>Type</th>
            <th>Null</th>
            <th>Key</th>
            <th>Default</th>
            <th>Extra</th>
        </tr>
TH;
		while($row = $result->fetch_assoc()) {
			echo <<<TR
        <tr>
            <td>{$row["Field"]}</td>
            <td>{$row["Type"]}</td>
            <td>{$row["Null"]}</td>
            <td>{$row["Key"]}</td>
            <td>{$row["Default"]}</td>
            <td>{$row["Extra"]}</td>
        </tr>
TR;
		}
		echo "    </table>\n";
	} else {
		echo "<span class='error'There's no schema for $table!</span>";
	}
}
function get_tables(&$connection, $database = NULL) {
	if(is_null($database)) {
		$sql = 'SHOW TABLES;';
	} else {
		$sql = 'SHOW TABLES IN `$database`;';
	}
	$result = $connection->query($sql);
	$tables_array = $result->fetch_array(MYSQLI_NUM);
	return $tables_array;
}
function tcol_class_from_finfo(&$finfo) {
	if(preg_match('/^ID$/i', $finfo->name)) {
		return 'id';
	} elseif(preg_match('/Price|Pay Rate/i', $finfo->name)) {
		return 'dollar';
	} elseif(preg_match('/Name/i', $finfo->name)) {
		return 'name';
	} elseif(preg_match('/Phone/i', $finfo->name)) {
		return 'phone';
	} elseif(preg_match('/Time/i', $finfo->name)) {
		return 'time';
	} elseif (in_array($finfo->type, array(1,2,3,8,13,16))) {
		return 'int';
	} elseif (in_array($finfo->type, array(4,5,246))) {
		return 'decimal';
	} else {
		return 'string';
	}
}
function print_results_as_table(&$result) {
	echo "    <table>\n        <tr>\n";
	$i = 0;
	while($i < mysqli_num_fields($result)) {
		$finfo = mysqli_fetch_field_direct($result,$i);
		echo '<th class="' . tcol_class_from_finfo($finfo) . '">' . $finfo->name . "</th>\n";
		$i++;
	}
	echo "        </tr>\n";
	while($row = $result->fetch_assoc()) {
		$i = 0;
		while($i < mysqli_num_fields($result)) {
			$finfo = mysqli_fetch_field_direct($result,$i);
			echo '            <td class="'. tcol_class_from_finfo($finfo) . '">' . $row[$finfo->name] . "</td>\n";
			$i++;
		}
		echo "        </tr>\n";
	}
	echo "    </table>\n";
}
function print_query_results(&$connection, $sql) {
	$result = $connection->query($sql);
	if($result->num_rows > 0) {
		print_results_as_table($result);
	} else {
		echo "<span class='error'>There's no rows in result for query: `$sql`</span>";
	}
}
function print_table(&$connection, $table, $limit = 0, $fields = array('*')) {
	$disallowed_pattern = '/\s|^(ADD|ALL|ALTER|ANALYZE|AND|AS|ASC|AUTO_INCREMENT|BDB|BERKELEYDB|BETWEEN|BIGINT|BINARY|BLOB|BOTH|BTREE|BY|CASCADE|CASE|CHANGE|CHAR|CHARACTER|CHECK|COLLATE|COLUMN|COLUMNS|CONSTRAINT|CREATE|CROSS|CURRENT_DATE|CURRENT_TIME|CURRENT_TIMESTAMP|DATABASE|DATABASES|DAY_HOUR|DAY_MINUTE|DAY_SECOND|DEC|DECIMAL|DEFAULT|DELAYED|DELETE|DESC|DESCRIBE|DISTINCT|DISTINCTROW|DIV|DOUBLE|DROP|ELSE|ENCLOSED|ERRORS|ESCAPED|EXISTS|EXPLAIN|FALSE|FIELDS|FLOAT|FOR|FORCE|FOREIGN|FROM|FULLTEXT|FUNCTION|GEOMETRY|GRANT|GROUP|HASH|HAVING|HELP|HIGH_PRIORITY|HOUR_MINUTE|HOUR_SECOND|IF|IGNORE|IN|INDEX|INFILE|INNER|INNODB|INSERT|INT|INTEGER|INTERVAL|INTO|IS|JOIN|KEY|KEYS|KILL|LEADING|LEFT|LIKE|LIMIT|LINES|LOAD|LOCALTIME|LOCALTIMESTAMP|LOCK|LONG|LONGBLOB|LONGTEXT|LOW_PRIORITY|MASTER_SERVER_ID|MATCH|MEDIUMBLOB|MEDIUMINT|MEDIUMTEXT|MIDDLEINT|MINUTE_SECOND|MOD|MRG_MYISAM|NATURAL|NOT|NULL|NUMERIC|ON|OPTIMIZE|OPTION|OPTIONALLY|OR|ORDER|OUTER|OUTFILE|PRECISION|PRIMARY|PRIVILEGES|PROCEDURE|PURGE|READ|REAL|REFERENCES|REGEXP|RENAME|REPLACE|REQUIRE|RESTRICT|RETURNS|REVOKE|RIGHT|RLIKE|RTREE|SELECT|SET|SHOW|SMALLINT|SOME|SONAME|SPATIAL|SQL_BIG_RESULT|SQL_CALC_FOUND_ROWS|SQL_SMALL_RESULT|SSL|STARTING|STRAIGHT_JOIN|STRIPED|TABLE|TABLES|TERMINATED|THEN|TINYBLOB|TINYINT|TINYTEXT|TO|TRAILING|TRUE|TYPES|UNION|UNIQUE|UNLOCK|UNSIGNED|UPDATE|USAGE|USE|USER_RESOURCES|USING|VALUES|VARBINARY|VARCHAR|VARCHARACTER|VARYING|WARNINGS|WHEN|WHERE|WITH|WRITE|XOR|YEAR_MONTH|ZEROFILL)$/';
	if(preg_match($disallowed_pattern,$table)) {
		$table = "`$table`";
	}
	foreach($fields as &$field) {
		if(preg_match($disallowed_pattern,$field)) {
			$field = "`$field`";
		}
	}
	echo '<pre><span class="keyword">SELECT</span> ' . implode('<span class="punct">,</span>', $fields) . ' <span class="keyword">FROM</span> ' . $table . '<span class="punct">;</span></pre>';
	if($limit > 0) {
		$sql = 'SELECT ' . implode(',', $fields) . " FROM $table LIMIT $limit;";
	} else {
		$sql = 'SELECT ' . implode(',', $fields) . " FROM $table;";
	}
	$result = $connection->query($sql);
	if($result->num_rows > 0) {
		print_results_as_table($result);
	} else {
		echo "<span class='error'>There's no rows in $table!</span>";
	}
}
?>