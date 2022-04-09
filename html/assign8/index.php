<?php
declare(strict_types = 1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
if(isset($_GET["source"])) {

    $functions = array("default", "html", "keyword", "string", "comment");
    foreach ($functions as $value) {
        ini_set("highlight.$value", "highlight-$value;");
    }
    $content = highlight_file($_SERVER["SCRIPT_FILENAME"], TRUE);
    foreach($functions as $value) {
        $content = preg_replace("/style=\"color: highlight-$value;\"/", "class=\"highlight-$value\"", $content);
    }
    $content = preg_replace(
        "/^<code><span class=\"highlight-html\">(.+)<\/span>\r?\n?<\/code>\$/s",
        "<code>$1</code>",
        $content
    );
    $content = preg_replace(
        "/\&lt;(\w+)\&nbsp;(.+?)(\/?)\&gt;/",
        "<span class=\"highlight-html\">&lt;<span class=\"highlight-tag\">$1</span>&nbsp;<span class=\"highlight-default\">$2</span>$3&gt;</span>",
        $content
    );
    $content = preg_replace(
        "/\&lt;(\/?)(\w+)\&gt;/",
        "<span class=\"highlight-html\">&lt;$1<span class=\"highlight-tag\">$2</span>&gt;</span>",
        $content
    );
    $content = preg_replace(
        "/highlight-default\">(\&lt;\?php|\?\&gt;)/",
        "highlight-tag\">$1",
        $content
    );
    $content = preg_replace(
        "/<span class=\"highlight-default\">(.*?)([a-zA-Z_]+)<\/span>\r?\n?<span class=\"highlight-keyword\">\(/",
        "<span class=\"highlight-default\">$1</span><span class=\"highlight-function\">$2</span><span class=\"highlight-punctuation\">(",
        $content
    );
    $content = preg_replace(
        "/<span class=\"highlight-keyword\">(.*?)((&.{2,4};|[,();=.])+)<\/span>/",
        "<span class=\"highlight-keyword\">$1<span class=\"highlight-punctuation\">$2</span></span>",
        $content
    );
    $content = preg_replace(
        "/<span class=\"highlight-keyword\">(\w*)((&.{2,4};|[,();=.])+)/",
        "<span class=\"highlight-keyword\">$1<span class=\"highlight-punctuation\">$2</span>",
        $content
    );
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noimageindex, nofollow, nosnippet">
    <title>${_SERVER["SCRIPT_FILENAME"]}</title>
    <style>
@media (prefers-color-scheme: dark) {
    body { color: #d4d4d4; background-color: #1e1e1e; }
    .highlight-html { color: #808073; }
    .highlight-tag { color: #569cd6; }
    .highlight-default { color: #9cdcfe; }
    .highlight-function { color: #dcdcaa; }
    .highlight-keyword { color: #c586c0; font-weight: bold; }
    .highlight-punctuation { color: #c9d4d4; font-weight: bold; }
    .highlight-string { color: #ce9178; }
    .highlight-comment { color: #6a9955; }
}
@media (prefers-color-scheme: light) {
    .highlight-html { color: #808073; }
    .highlight-tag { color: #881280; }
    .highlight-default { color: #0000bb; }
    .highlight-function { color: #0000bb; }
    .highlight-keyword { color: #007700; font-weight: bold; }
    .highlight-punctuation { color: #1e1e1e; font-weight: bold; }
    .highlight-string { color: #dd0000; }
    .highlight-comment { color: #ff8000; }
}
    </style>
</head>
<body>
$content
</body>
</html>
HTML;
    exit();
}
function reset_database(&$pdo){
    $sql = <<<SQL
DROP TABLE IF EXISTS SP,S,P;
CREATE TABLE S (
  `S` VARCHAR(10) PRIMARY KEY,
  `SNAME` VARCHAR(255) NOT NULL,
  `STATUS` INT NOT NULL,
  `CITY` VARCHAR(255) NOT NULL
);
CREATE TABLE P (
  `P` VARCHAR(10) PRIMARY KEY,
  `PNAME` VARCHAR(255) NOT NULL,
  `COLOR` VARCHAR(255) NOT NULL,
  `WEIGHT` INT NOT NULL
);
CREATE TABLE SP (
  `S` VARCHAR(10),
  `P` VARCHAR(10),
  `QTY` INT NOT NULL,
  FOREIGN KEY (`S`) REFERENCES S(`S`),
  FOREIGN KEY (`P`) REFERENCES P(`P`),
  PRIMARY KEY (`S`,`P`)
);
INSERT INTO S (`S`,`SNAME`,`STATUS`,`CITY`) VALUES
  ('S1','Smith','20','London'),
  ('S2','Jones','10','Paris'),
  ('S3','Blake','30','Paris'),
  ('S4','Clark','20','London'),
  ('S5','Adams','30','Athens');
INSERT INTO P (`P`,`PNAME`,`COLOR`,`WEIGHT`) VALUES
  ('P1','Nut','Red','12'),
  ('P2','Bolt','Green','17'),
  ('P3','Screw','Blue','17'),
  ('P4','Screw','Red','14'),
  ('P5','Cam','Blue','14'),
  ('P6','Cog','Red','19');
INSERT INTO SP (`S`,`P`,`QTY`) VALUES
  ('S1','P1','300'),
  ('S1','P2','200'),
  ('S1','P3','400'),
  ('S1','P4','200'),
  ('S1','P5','100'),
  ('S1','P6','100'),
  ('S2','P1','300'),
  ('S2','P2','400'),
  ('S3','P2','200'),
  ('S4','P2','200'),
  ('S4','P4','300'),
  ('S4','P5','400');
SQL;
    try {
        $pdo->exec($sql);
        echo "<p>The database was reset</p>\n";
    }
    catch (PDOException $e) {
        echo "<p class=\"error\">There was a problem resetting the database: ${$e->getMessage()}</p>\n";
    }
}
function get_table_num_rows(&$pdo, $table) {
    try {
        return intval($pdo->query("SELECT COUNT(*) FROM $table;")->fetchColumn());
    }
    catch (PDOException $e) {
        return 0;
    }
}
function print_header($title, $pageicon = "") {
    $li = array();
    foreach(['admin','parts','suppliers','buy','inventory'] as $page) {
        if(strpos($_SERVER['REQUEST_URI'], "?page=$page") !== false) {
            $li[$page] = ' class="current"';
        } else {
            $li[$page] = "";
        }
    }
    if(strpos($_SERVER['REQUEST_URI'], "?page=") === false) {
        $li['home'] = ' class="current"';
    } else {
        $li['home'] = "";
    }
    $icons = [
        'admin' => '&#x1F6E0;&#xFE0F;',
        'home' => '&#x1F3E0;',
        'main' => '&#x1F3E0;',
        'parts' => '&#x1F529;',
        'suppliers' => '&#x1F468;&#x200D;&#x1F4BC;',
        'buy' => '&#x1F6D2;',
        'inventory' => '&#x1F4E6;'
    ];
    if(array_key_exists($pageicon, $icons)) {
        $title = $icons[$pageicon] . ' ' . $title;
    }
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title</title>
    <style>
body {
    max-width: 900px;
    margin: auto;
    font-size: larger;
    font-family: "Arial", Gadget, sans-serif;
}
.error {
   color: #dd0000;
   background-color: #ffdddd;
}
table {
    border-collapse: separate;
    border-spacing: 2px;
    border-color: gray;
    margin: 2px;
    text-align: center;
}
th,td {
    padding: 0.3em;
    margin-left: 2em;
    margin-right: 2em;
    margin-top: 0.5em;
    margin-bottom: 0.5em;
}
.menu {
    display: table;
    margin: 0px 0px 10px 0px;
    padding-inline-start: 0px;
    float:left;
    width:100%;
}
.menu li
 {
    display: inline-block;
    border: 2px solid #bbbbbb;
    border-radius: 10px;
    margin: 0.3em;
    background-color: #aaaaaa;
}
.menu li.current
{
    background-color: #666666;
}
.menu li a:hover
 {
    color:#1a1a1a;
    text-decoration: none;
}
.menu li:hover
 {
    border: 2px solid #ffffff;
    border-radius: 10px;
    background-color: #dddddd;
}
.menu li a
 {
    color:#1a1a1a;
    font-size: large;
    font-weight: bold;
    font-family: "Arial Black", Gadget, sans-serif;
    text-decoration: none;
    display: inline-block;
    padding: 0.3em 1em 0.3em 0.5em;
}
.menu li.active a
 {
    color:#1a1a1a;
    text-decoration: none;
}
.menu li.active
 {
    border: 2px solid #1a1a1a;
    border-radius: 10px;
    background-color: #dddddd;
}
button {
    font-size:large; text-align: center; padding: 0.5em; border-width: 2px; margin: 0.5em;
}
img.part {
    float: left; margin: 6px; padding: 6px;
}
img.red {
    -webkit-filter: invert(40%) grayscale(100%) brightness(40%) sepia(100%) hue-rotate(-50deg) saturate(400%) contrast(2);
    filter: grayscale(100%) brightness(40%) sepia(100%) hue-rotate(-50deg) saturate(600%) contrast(0.8);
}
img.green {
    -webkit-filter: grayscale(100%) brightness(40%) sepia(100%) hue-rotate(50deg) saturate(1000%) contrast(0.8);
    filter: grayscale(100%) brightness(40%) sepia(100%) hue-rotate(50deg) saturate(1000%) contrast(0.8);
}
img.blue {
    -webkit-filter: grayscale(100%) brightness(30%) sepia(100%) hue-rotate(-180deg) saturate(700%) contrast(0.8);
    filter: grayscale(100%) brightness(30%) sepia(100%) hue-rotate(-180deg) saturate(700%) contrast(0.8);
}
div.add-part,div.parts-list,div.add-supplier,div.suppliers-list,div.inventory-list,div.add-inventory {
    clear: both;
}
@media (prefers-color-scheme: dark) {
    body{
        background-color: #1a1a1a;
        color: #f9f9f9;
    }
    tr:nth-child(even) {background-color: #2f2f2f;}
    tr:nth-child(odd) {background-color: #222222;}
    th,td {
        color: #cccccc;
    }
    td a {
        color: #aabbff;
    }
    td a:hover {
        color: #ffffff;
    }
    td a:visited {
        color: #bb99ff;
    }
}
@media (prefers-color-scheme: light) {
    body{
        background-color: #eeeeee;
        color: #111111;
    }
    tr:nth-child(even) {background-color: #eeeeee;}
    tr:nth-child(odd) {background-color: #dddddd;}
    th,td {
        color: #111111;
    }
    td a {
        color: darkblue;
    }
    td a:hover {
        color: blue;
    }
    td a:visited {
        color: purple;
    }
}
    </style>
    <script language="javascript">
    // toggle() adapted from example on https://stackoverflow.com/a/386303
    function toggle(source, name) {
        checkboxes = document.getElementsByClassName(name);
        for(var i=0, n=checkboxes.length;i<n;i++) {
            checkboxes[i].checked = source.checked;
        }
    }
    function toggleBtn(source, name) {
        button = document.getElementById(name);
        if(source.checked) {
            button.disabled = "";
        } else {
            button.disabled = "disabled";
        }
    }
    </script>
</head>
<body>
    <div class="header">
        <ul class="menu">
            <li${li['home']}><a href="?">&#x1F3E0; Home</a></li>
            <li${li['parts']}><a href="?page=parts">&#x1F529; Parts</a></li>
            <li${li['suppliers']}><a href="?page=suppliers">&#x1F468;&#x200D;&#x1F4BC; Suppliers</a></li>
            <li${li['buy']}><a href="?page=buy">&#x1F6D2; Buy </a></li>
            <li${li['inventory']}><a href="?page=inventory">&#x1F4E6; Inventory</a></li>
            <li${li['admin']}><a href="?page=admin">&#x1F6E0;&#xFE0F; Admin</a></li>
        </ul>
    </div>
    <div class="content">
        <h1>$title</h1>

HTML;
}
function print_main_page(&$pdo) {
    echo <<<HTML
        <p>Welcome to parts DB. Please select a page from the top navigation.</p>
        <p><a href="?source">Click here to view the PHP source code as a highlighted HTML page.</a></p>

HTML;
    print_db_stats($pdo);
}
function print_db_stats(&$pdo) {
    $pcount = get_table_num_rows($pdo, 'P');
    $scount = get_table_num_rows($pdo, 'S');
    $spcount = get_table_num_rows($pdo, 'SP');
    echo <<<HTML
        <b>Database Stats:</b>
        <table>
            <tr>
                <th>Table</th><th>Size</th>
            </tr>
            <tr>
                <td>S</td><td>$scount</td>
            </tr>
            <tr>
                <td>P</td><td>$pcount</td>
            </tr>
            <tr>
                <td>SP</td><td>$spcount</td>
            </tr>
        </table>

HTML;
}
function print_admin_page(&$pdo, $action) {
    switch($action) {
        case "reset":
            reset_database($pdo);
            break;
    }
    print_db_stats($pdo);
    echo <<<HTML
        <form method="POST">
            <button name="action" value="reset">Reset database</button>
        </form>

HTML;
}
function add_supplier(&$pdo, $supplierid, $sname, $status, $city) {
    $sql = "INSERT INTO S (`S`, `SNAME`, `STATUS`, `CITY`) VALUES (:supplierid, :sname, :status, :city);";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':supplierid', $supplierid, PDO::PARAM_STR, 10);
        $stmt->bindParam(':sname', $sname, PDO::PARAM_STR, 255);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT, 255);
        $stmt->bindParam(':city', $city, PDO::PARAM_STR, 255);
        if($stmt->execute()) {
            echo "            <p>Supplier was successfully added to the database. <a href=\"?page=suppliers\">[OK]</a></p>\n";
            print_supplier_details($supplierid, $sname, $status, $city);
        } else {
            echo "            <p class=\"warning\">Supplier could not be added to the database. <a href=\"javascript:history.back()\">[Go back]</a></p>\n";
        }
    }
    catch (PDOException $e) {
        echo "            <p class=\"error\">There was a problem adding the supplier: ${$e->getMessage()}<br /><a href=\"javascript:history.back()\">[Go back]</a></p>\n";
    }
}
function add_part(&$pdo, $partid, $pname, $color, $weight) {
    $sql = "INSERT INTO P (`P`, `PNAME`, `COLOR`, `WEIGHT`) VALUES (:partid, :pname, :color, :weight);";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':partid', $partid, PDO::PARAM_STR, 10);
        $stmt->bindParam(':pname', $pname, PDO::PARAM_STR, 255);
        $stmt->bindParam(':color', $color, PDO::PARAM_STR, 255);
        $stmt->bindParam(':weight', $weight, PDO::PARAM_STR, 255);
        if($stmt->execute()) {
            echo "            <p>Part was successfully added to the database. <a href=\"?page=parts\">[OK]</a></p>\n";
            print_part_details($partid, $pname, $color, $weight);
        } else {
            echo "            <p class=\"warning\">Part could not be added to the database. <a href=\"javascript:history.back()\">[Go back]</a></p>\n";
        }
    }
    catch (PDOException $e) {
        echo "            <p class=\"error\">There was a problem adding the part: ${$e->getMessage()}<br /><a href=\"javascript:history.back()\">[Go back]</a></p>\n";
    }
}
function inventory_exists(&$pdo, $supplierid, $partid) {
    $sql = <<<SQL
    SELECT COUNT(*) FROM SP
    WHERE S=:supplierid AND P=:partid;
    SQL;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':supplierid', $supplierid, PDO::PARAM_STR, 10);
        $stmt->bindParam(':partid', $partid, PDO::PARAM_STR, 10);
        return ($stmt->execute() && $stmt->rowCount() != 0 && (int)$stmt->fetchColumn() != 0);
    }
    catch (PDOException $e) {
        echo "            <p class=\"error\">Unable to check if $supplierid carries $partid. PDOException: ${$e->getMessage()}<br /></p>\n";
    }
    return false;
}
function add_inventory(&$pdo, $supplierid, $partid, $qty) {
    if ($qty < 1) {
        echo "            <p>Sorry, you can't add quantity less than 1. <a href=\"javascript:history.back()\">[Go back]</a></p>\n";
        return;
    }
    if(inventory_exists($pdo, $supplierid, $partid)) {
        $sql = <<<SQL
        UPDATE SP
        SET QTY = QTY + :qty
        WHERE S=:supplierid AND P=:partid;
        SQL;
    } else {
        $sql = "INSERT INTO SP (`S`, `P`, `QTY`) VALUES (:supplierid, :partid, :qty);";
    }
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':supplierid', $supplierid, PDO::PARAM_STR, 10);
        $stmt->bindParam(':partid', $partid, PDO::PARAM_STR, 10);
        $stmt->bindParam(':qty', $qty, PDO::PARAM_STR, 255);
        if($stmt->execute()) {
            echo "            <p>Inventory was successfully added to the database. <a href=\"?page=inventory\">[OK]</a></p>\n";
        } else {
            echo "            <p class=\"warning\">Inventory could not be added to the database. <a href=\"javascript:history.back()\">[Go back]</a></p>\n";
        }
    }
    catch (PDOException $e) {
        echo "            <p class=\"error\">There was a problem adding the inventory: ${$e->getMessage()}<br /><a href=\"javascript:history.back()\">[Go back]</a></p>\n";
    }
}
function get_inventory_availability(&$pdo, $supplierid, $partid) {
    $qty = 0;
    $sql = <<<SQL
    SELECT QTY FROM SP
    WHERE S=:supplierid AND P=:partid;
    SQL;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':supplierid', $supplierid, PDO::PARAM_STR, 10);
        $stmt->bindParam(':partid', $partid, PDO::PARAM_STR, 10);
        if($stmt->execute()) {
            if($stmt->rowCount() == 0) {
                echo "            <p>Sorry. Part $partid is not available from supplier $supplierid.</p>\n";
            } else {
                $qty = (int)$stmt->fetchColumn();
            }
        } else {
            echo "            <p class=\"warning\">Unable to check if $supplierid carries $partid.</p>\n";
        }
    }
    catch (PDOException $e) {
        echo "            <p class=\"error\">Unable to check if $supplierid carries $partid. PDOException: ${$e->getMessage()}<br /></p>\n";
    }
    return $qty;
}
function subtract_inventory(&$pdo, $supplierid, $partid, $qty) {
    if($qty <= 0) {
        echo "            <p>Sorry, your order of ($qty) of part $partid from supplier $supplierid could not be completed because quantity must be greater than 0.<a href=\"?page=buy\">[OK]</a></p>\n";
    } elseif(get_inventory_availability($pdo, $supplierid, $partid) >= (int)$qty) {
        $sql = <<<SQL
        UPDATE SP
        SET QTY = QTY - :qty
        WHERE S=:supplierid AND P=:partid;
        SQL;
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':supplierid', $supplierid, PDO::PARAM_STR, 10);
            $stmt->bindParam(':partid', $partid, PDO::PARAM_STR, 10);
            $stmt->bindParam(':qty', $qty, PDO::PARAM_STR, 255);
            if($stmt->execute()) {
                if($stmt->rowCount() > 0) {
                    echo "            <p>You successfully purchased ($qty) of part $partid from supplier $supplierid. <a href=\"?page=buy\">[OK]</a></p>\n";
                } else {
                    echo "            <p>Your purchase of ($qty) of part $partid from supplier $supplierid could not be completed.<a href=\"javascript:history.back()\">[Go back]</a></p>\n";
                }
            } else {
                echo "            <p class=\"warning\">Your purchase of ($qty) of part $partid from supplier $supplierid could not be completed.<a href=\"javascript:history.back()\">[Go back]</a></p>\n";
            }
        }
        catch (PDOException $e) {
            echo "            <p class=\"error\">There was a problem and your purchase of ($qty) of part $partid from supplier $supplierid could not be completed. PDOException: ${$e->getMessage()}<br /><a href=\"javascript:history.back()\">[Go back]</a></p>\n";
        }
    } else {
        echo "            <p>Sorry, there is not enough of inventory to fulfill your order of ($qty) of part $partid from supplier $supplierid. <a href=\"javascript:history.back()\">[Go back]</a></p>\n";
    }
}
function print_parts_table(&$rows) {
    echo <<<HTML
            <h2>Part Inventory</h2>
            <table>
                <tr>
                    <th>Supplier Name</th><th>City</th><th>Quantity</th><th>Status</th>
                </tr>

HTML;
    foreach($rows as $row) {
        echo <<<HTML
                <tr>
                    <th>${row['SNAME']}</th><th>${row['CITY']}</th><th>${row['QTY']}</th><th>${row['STATUS']}</th>
                </tr>

HTML;
    }
    echo "            </table>\n";
}

function print_stock_table(&$rows) {
    echo <<<HTML
            <h2>Part Inventory</h2>
            <table>
                <tr>
                    <th>Part Name</th><th>Color</th><th>Weight</th><th>Quantity</th>
                </tr>

HTML;
    foreach($rows as $row) {
        echo <<<HTML
                <tr>
                    <th>${row['PNAME']}</th><th>${row['COLOR']}</th><th>${row['WEIGHT']}</th><th>${row['QTY']}</th>
                </tr>

HTML;
    }
    echo "            </table>\n";
}
function print_suppliers_list(&$pdo) {
    echo <<<HTML
        <div class="suppliers-list">
            <p>Click on a supplier ID for more details about the supplier. Check boxes to select suppliers to remove. </p>
            <form method="POST">
                <table style="float: left; margin-left: 1em;">
                    <tr>
                        <th><input type="checkbox" onClick="toggle(this, 'supplierid')" /></th><th>Supplier&nbsp;ID</th><th>&nbsp;Name&nbsp;</th><th>&nbsp;Status&nbsp;</th><th>&nbsp;City&nbsp;</th>
                    </tr>

HTML;
    $sql = "SELECT * FROM S ORDER BY CAST(SUBSTR(S,2) AS INT) ASC;";
    $stmt = $pdo->query($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    while($row = $stmt->fetch()) {
        echo <<<HTML
                    <tr>
                        <td><input type="checkbox" class="supplierid" name="supplierid[]" value="${row['S']}" /></td><td><a href="?page=suppliers&action=show&supplierid=${row['S']}">${row['S']}</a></td><td>${row['SNAME']}</td><td>${row['STATUS']}</td><td>${row['CITY']}</td>
                    </tr>
HTML;
    }
    echo <<<HTML
                </table>
                <button type="submit" name="action" value="remove" style="float: left; margin: 1em;">Remove selected suppliers</button>
            </form>
        </div>

HTML;
}
function print_parts_list(&$pdo) {
    echo <<<HTML
        <div class="parts-list">
            <p>Click on a part ID for more details about the part. Check boxes to select parts to remove. </p>
            <form method="POST">
                <table style="float: left; margin-left: 1em;">
                    <tr>
                        <th><input type="checkbox" onClick="toggle(this, 'partid')" /></th><th>Part&nbsp;ID</th><th>&nbsp;Name&nbsp;</th><th>&nbsp;Color&nbsp;</th><th>&nbsp;Weight&nbsp;</th>
                    </tr>

HTML;
    $sql = "SELECT * FROM P ORDER BY CAST(SUBSTR(P,2) AS INT) ASC;";
    $stmt = $pdo->query($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    while($row = $stmt->fetch()) {
        echo <<<HTML
                    <tr>
                        <td><input type="checkbox" class="partid" name="partid[]" value="${row['P']}" /></td><td><a href="?page=parts&action=show&partid=${row['P']}">${row['P']}</a></td><td>${row['PNAME']}</td><td>${row['COLOR']}</td><td>${row['WEIGHT']}</td>
                    </tr>
HTML;
    }
    echo <<<HTML
                </table>
                <button type="submit" name="action" value="remove" style="float: left; margin: 1em;">Remove selected parts</button>
            </form>
        </div>

HTML;
}
function print_inventory_list(&$pdo) {
    echo <<<HTML
        <div class="inventory-list">
            <form method="POST">
                <table style="float: left; margin-left: 1em;">
                    <tr>
                        <!--<th>&nbsp;</th>-->
                        <th>Supplier&nbsp;ID</th>
                        <th>Supplier&nbsp;Name</th>
                        <th>&nbsp;Status&nbsp;</th>
                        <th>&nbsp;City&nbsp;</th>
                        <th>Part&nbsp;ID</th>
                        <th>Part&nbsp;Name</th>
                        <th>&nbsp;Color&nbsp;</th>
                        <th>&nbsp;Weight&nbsp;</th>
                        <th>&nbsp;Quantity&nbsp;</th>
                    </tr>

HTML;
    $sql = <<<SQL
SELECT * FROM SP
INNER JOIN P USING(P)
INNER JOIN S USING(S)
ORDER BY CAST(SUBSTR(S,2) AS INT),CAST(SUBSTR(P,2) AS INT) ASC;
SQL;
    $stmt = $pdo->query($sql);
    $stmt->setFetchMode(PDO::FETCH_ASSOC);
    while($row = $stmt->fetch()) {
        echo <<<HTML
                    <tr>
                        <!--<td><span style="font-size: 1em; color: transparent; text-shadow: 0 0 0 red;">&#x24E7;</span></td>-->
                        <td><a href="?page=suppliers&action=show&supplierid=${row['S']}">${row['S']}</a></td>
                        <td>${row['SNAME']}</td>
                        <td>${row['STATUS']}</td>
                        <td>${row['CITY']}</td>
                        <td><a href="?page=parts&action=show&partid=${row['P']}">${row['P']}</a></td>
                        <td>${row['PNAME']}</td>
                        <td>${row['COLOR']}</td>
                        <td>${row['WEIGHT']}</td>
                        <td>${row['QTY']}</td>
                    </tr>
HTML;
    }
    echo <<<HTML
                </table>
            </form>
        </div>

HTML;
}
function print_supplier_details($supplierid, $sname, $status, $city) {
    echo <<<HTML
        <div class="details">
            <h2>Supplier Details</h2>
            <table>
                <tr>
                    <th>Supplier&nbsp;ID</th><td>$supplierid</td>
                </tr>
                <tr>
                    <th>Name</th><td>$sname</td>
                </tr>
                <tr>
                    <th>Status</th><td>$status</td>
                </tr>
                <tr>
                    <th>City</th><td>$city</td>
                </tr>
            </table>
        </div>
HTML;
}
function print_part_details($partid, $pname, $color, $weight) {
    $_color = strtolower($color);
    $_pname = strtolower($pname);
    $imgs = array(
        'nut' => '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',
        'bolt' => 'iVBORw0KGgoAAAANSUhEUgAAAJUAAACECAMAAACu9plDAAABklBMVEUAAAAoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgoKCgpKSkoKCgoKCgoKCgoKCgoKCg6OjpRUVEoKChlZWXy8vLs7Ozo6Ojm5ubq6urg4ODk5OTZ2dnu7u7i4uLX19fc3Nze3t7Q0NDJycmNjY3BwcGWlpbw8PD29vbS0tKCgoK0tLSfn5/6+vq3t7fMzMzExMS+vr6urq6np6crKyuioqL09PSTk5NRUVH4+Ph4eHhhYWE0NDSQkJA8PDwuLi6cnJzGxsaJiYm5ubmzs7Orq6ttbW2pqamYmJiAgIClpaWLi4ukpKSGhoaEhIR2dnZwcHBzc3NeXl44ODiwsLBra2tbW1tnZ2dYWFhVVVUxMTF9fX16enrU1NTOzs5KSkq7u7utra1+fn78/PxNTU1AQEBFRUXDw8NHR0f+/v5CQkLLy8s+Pj5MTEyANy4mAAAAK3RSTlMAAvz38us6I8yjlkUdDdxrXEwS5nOJMxi/CATTVSyOemSsg+/gtLjFnu3cYacNqgAADzZJREFUeNrswQENAAAAwiD7p7bHBwwAAAAACDkxZf6VxhXF8dGoSUxcEpumMUmTmu20l9mBYXEWYNiZgRHZISCKghuJiltc0mj+7943mLT0xNOfevxw5Hy87573vpcHMzT369yLqZfT088eEO4hY2M/I0+oG+QlbLT3gq1yedmmXC63lha2FgF+pm6Ox7+7P/3555+f8I1gK/7zyZW9yVTjqaBiNJPJuk2yuWLoSg4/rvfwnLox7g/LXpFXaYLK8Jxb8PQC0VKkXr7JVI/AHf4x729TN8XsDEBmN63X5Fgsdi7LcvwwlKjVm8V0F+5TN8U9ODiS/f4LX6AnfaPX27/wxzYvn+ED4vnt248fTjyZnR2ihqi/+Z8fGuNwEPEHPIKbY1meUWnN+oa2Bd8ZHr41MjIy+fbR2NXvdmaqLw+nJ/oyNXcV+uff+hc/9OLxVcujh1SfMZRBvg/6cHDMyTT0SaVSfSl8/pB1dLe6B/CdtaibZ1yMGNiDXynCbzD8gEImnsJT+6j7t2B8yK68hpnnRKZhZPqqd7Q/y73hp1eB595iCzL7y51+5fYr3MZmbILsBoGMLHkFEU/9DsOwbsF5kYErUgtGKH5+fng0j0lfkv0X1+EV2e8Rrv5G0ozjSC8o5AVWJvGEJ691GJ4mx3wxYJT0zk46YNRO8xJIBflpB2ZseVOAN0N28vwcvj/bUJVgQ2/Wa7XaUSKxubmZSBwd1cjXfS8FfQrCp2/EcL+H1NCdzSLAu1n8hBK4PkWhVPGax/BqR05IUEx3zAPcGqOG3tWcAHcmMMueH+A1XuvEqIyVWby20Up/hLEzXCIjPJn0vyPD7tES/BflStTpdXOMFo4CwDg1BWIN5RE1fmwBwMh96q4hoDydpe62fIA8eDziI0uTE8/yLs7+QGcnnU6Ut0PUC8NKAfkqjK+w/RHeyiijGPinJj2Cn9k7Q1VXO5vx8xLi70P0PB7Rz8CmSIe/0wNkbkThK0QeDXvCGcC5p76qHCBT05e8m8jru7vh8DbK3cmI6bZ7p1vhfs/tV0w4i/Ly3pnLAmT6QdcySS9F/aGGP2O4GVlVpfxxTm/WNqt/k0gaynEBCNtm+G8EIHQsVbZlORzu2HIYFgF5NRKiBbARY9YCIB/MhnlKZNQnmSnyc55ZDtPHgNzaDNtD3Xrll9kNlHsTNY3ujFFPhiWXi/+6sqI30rngQjubOTnbgQG2qrH9gIQXyDK0xgFBj/gubHFGmVVACkyEBsKZWNMWiZRDcatMZGPNY2WJ7ARFKw+EUMJM28KFzHZ/upKVIcF/laJW8Bn1HDiGOSozg/CcKCAngKTcKm1qmmbZuMBGt5yAZCIxq2in6kjWKZH1VbWfId+wLL2fwbJ2bSlZ1ntbYqpVt7dOc1YLCEXLWgNksaJZuSnqwQeGFy59/I9gDgBZ0AYAwjqtCf0TNa1ui1/TPtri5TX7dhzq1VKG1rQ0kS842yoQBE0L2SJpWtEWzukq2xtr+6IxR73cZdkm/v0IPg9IVRsgs/cZayYnArKNB1XtGU3e7Nhi6NoyESV5qG0SSTMBTbdTnR+ZChBqafbczmDyWhIIjrhmL102qmZinHqhcOJBieM49ur1D9gdQHrmAO8TbBm25hkV9neh65FdcjFygnfhM1dLsW2AhGnqxRye6DXN+FoWYGeFNeuNDglDm838CRmUN32ApCKmGTmS82RMcwUIftMM3aXGm6LcFf8B9+2FDgSeHmC3XeOULEebp4dqA95zdHSZ5to7Ek0r7yW+BW6aPlLoSmoJe0sKk4YFlMOkqsOJSdOROtMCEGja6fCfwoZK07ElRjqFEE1XE4kCgEjT8TfU3apbSbgR8er1DwRAPtODzB+yxraB8iEWi6Q8NB04qXvES9xfr4TKCkkVSeXEww62+CDJG22UGBicAdgjr3HqEThpWoCkdNDFpQtYaCxDE5N3mN5Hkur8F+pNXNjuCT/GA0hbHUQ/rbDJNMpaNh5dd6qq58wZbQGvqvV1iTdSXlWVHWxEX8cWKeVMLm2g+B3ORBfcKF09zTqiqsqklsu1DOlZ5MV4Fjc8h11R+CioavQOdec8sOW9Bicgu65B6s1edX0VpRz3HOX3XS7uoGFcnHpcrkojVIwu4ePPn2/2pEVs8ULSUwHW5ZIgW71IeVC+eJ3FRRnX8opzDZzYU9A7ciXockWDHkNvY4/vKfW0VFE81xAApOUapHoa8QbaKMrXmHshho/gDCfqEHC55A7DFcHvcvW6DH7vWYZhwSt3wMkwni4bDYIPJZVLBIIhhnFlQ4kyGAzD7fj4w68dFyNlSh6/o8cwvklqsrRSc15DFJAcM0j8shLZO0Axcr4GxFHywX2m6GeYUtaoxdaw4iwYPmFHwKXF+R6/tc8wIgSdTE5GSQXcVYeBS7sVRgKMx+8EW4lmBgPDSn2pUGKYwGtqxj9/KF2DH5A0P4h/XeS5DZbnE2tufiGEla4sNdYrPO9TeMFIYcXr4ANKSsKlj5XQ3ucYz7MFX3L1JIKVDV0JrSgoy935BaigfAiw++kCywtQZ/lcnOd7M9TwxYIcuIYYIMvsIFHYcXwEN8tWoLC1uIKVvUxFSNdZVkqt1ivbRyzLwXKkUvDhUmdvM3QWQcmkJTFXQ/kQkeLdFkpD8bjXQqQHDtoOEFg2BZnjPDb3RinwO2Tfv9nvv2RAdrlBAkBwclwckCJWWgCLX3SO8wDBwMpnIiWUVSJJlDbARr7Rl/xBGyUJ4PhKlo6BEOC4zFVzYIQaLmXk6DXY53bEQfpn+0Sxf79YUYi0RNENhAZWtonEUXJEdJQ9IkGUIJEMStUeyu4hnIuiAxBFFAO3qOHYWdx/DRVAtt2DCAVAom63D5B5rBhEtlDOiORQ2kQOUXQ7MMo8kTZKmsgiSsXOiVIEwqbbvQbIEm6MqeL5SOwaQoCkPMIgl4D4BUECZBULNSIFpyDYw2a9ghAkUv22lMGKfXQeKw0g4HM7Zo+MlSYQFEEoA3IiCPvDfzFeZr9qAlEYJ12Spm360se+dEvbZHgkQUBIrAbcCEZUFEVBcZfW5dar5naJbf/vwjkzXTHp7+Hy3TnjzHcWYuSI3uqpF1gRMCD9iU1i1roArbyKF3oE+iRJc8giK0vQFPdnaGdIJg6PJFVAeJJkgChnpDIIW5A0ELpkxK5W52PpAh794J9Aof2MkGnFz2m80IU65IzMCcxkchk4/yMLkU73g4dmMhnMtNL9sIcvWTOf0wmkJ2SgnnVZUAh3a3AddhH9b7C2WYEhisnfAMY0FjYcJgifofKxaII9RRBMcMVCZC8IOibIRC8+DEr+QRBKsDIRBCijLQqd2JW51vIXwLJ/rGumabY1y7Ka7tQVR1ArURSTmT53RbEDc6WIIs7MpihCZb7naIh8FcUDiJMooods/HF4N+aGiM6rPXGFoX2RcLfb/LicztEngJ77kGOo7w1oj1Xy5eRtj4aTEvyuWI4tmb5NQxzIhe7mDVyxeiqI5lEugrBdNbeAaczq6Hy5KuZBmFP1FnfHms0rF7AIEMi/qEzkArxXSkdNitYox4tVuFFRsOP1jqzDlm4ccqBEe1mB0FRWUOy+9uQoEdFneQ8rTmWP9RzKpdvcXffqXeECI4Js6qe3k5hgN1tW6bTdrAw0PVf315h/8UiA7eCAwg6NGxA103CwPQYTinECcVaNFgFGKrbS7N7mXu6CpXaBtwT5ZFa8coxXMftkNCB0EHwUdeUdCmtFkJlKKB2bbla/oQiVa7pHcVG4yg2h84jPef4O92g2JuNsOnWCOOahqHQAjUQ9ggQFfH7pRPSiPEHWxSpVpT4VK+r8HfNJDlmacocniOpghct3uSf221b0Np0Z+cX2fFPbbOJEP7K7FxXqWb2iyeoEaRQbVOV3VJgR88nE0Gd7Zkx8x5wGj7nX63qtVv+HaQImgTlW8t3DQV8Ntw67+5tHhR5QMyVCKW2oOLIh8KesaKwBzQIzzFaGNh3CR9yz6/7CifqpYNURZx3VT0HQj02w9gwI4k2YvS3LmrWpMmYeTuxq5jMwqdBYyI/Qlf+Eu/+N56sbPpUNSaN8ZleyZJvQvyrxvrEt71koS4XL7PkuFdGQGWZJZafYweY97vmS57+0+FRqJIXpKlluxPcOnORfm7QtErPTtsManDonFTjfnjho+GZOArQ3rTZHIObLNSRVdUgWV3iyw6LZb19xTx2eX5D1f7vaHr2ksV+85s5rJYd5owK4agw1c5GI88AdXkH1PG0ArpYDrYmutOykCWLXXg/gOO2a+nT5KTZ3XH/DPSDx9R/Ti/WFpGCGPBwyDENwHYShBYF+GNp4bBheMYEeAiaisICiVTBDJxGzNu1pUKCt9Pnn3EOySAbI2TYay4TWb5A0Ru2IZttOivb4xaRtsQi4evTJb4OrJy2/jSVa+nRPtQkr4Kb9gx2zaVEbCOP4kxdNzMsm0dFEN4m7dbUL40fYk+dAZGPJ4qEgS0tXWXWlIJQqUuj37jwzaS82e+ilOezvMn+eyZifzyQE5lBUhNX44UE8e+k+BEndotXfyWdn0E26QRkn/5TihoVuzjdF7YyOH9Aq0JTn9B6l7IssxTsqRNmkKCN7l9OU35oYyTzFVrf1oqLnd6noVUYtgGDKdrDEanl3xm73Ga1cME5zJqFLMFzhr/XBH2xRsw4kWTzxUJfvccoBJ/n2yMItaOrzguKqXvB9zqxqdqR8nFAWmsOXR5xS3Qt+sn0aZ7SE7WLyhwVnslOPQsZuZUzCBLAGP1nv8DidMgIAKTiwvR0CQLx64tdKw+NiRhULmDGas1V+DV8mAtDPv1AWmPDmKw92EwC8FZsuYXba7/FD87LdrtfrZZaNl0p0k+DZPkCYnGggYeD9YDYj3iHmWWPBY6GrJ1QWlfWKxoDG3JzhJQd60wXovd+xZ5EFJ9lQrHBsJftBSwjC0L1ySP9dOzYM47ZxPQz6oLXjDiAGVSIeiMztwG/Ra0A8mTZ40FrMBTGVRPFBGMtiOaGyKSriL0gNiqEgmOa0hAa8hhRfFSmyxdgZ+SIQvSuCRnoimLV2cc0lBsQNxVhXDUB6sUrgNySn5Vb/SvOs0u1BGZpUBBwLLPqK1f9Dr6SVU0mr5qCKVuDKVbSCjqG35MpZIVLXtpu+ZWlaFHXqpum5TjuEN9741R4ckAAAAAAI+v+6H6ECAAAAwE3wQEGyQpPI1AAAAABJRU5ErkJggg==',
        'screw' => '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',
        'cam' => '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',
        'cog' => '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',
        'other' => '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'
    );
    if(in_array($_pname,array_keys($imgs))){
        $img = $imgs[$_pname];
    } else {
        $img = $imgs['other'];
    }
    echo <<<HTML
        <div class="details">
            <h2>Part Details</h2>
            <img alt="$partid" class="$_color part" height=132 width="150" src="data:image/png;base64,$img" />
            <table>
                <tr>
                    <th>Part ID</th><td>$partid</td>
                </tr>
                <tr>
                    <th>Name</th><td>$pname</td>
                </tr>
                <tr>
                    <th>Color</th><td>$color</td>
                </tr>
                <tr>
                    <th>Weight</th><td>$weight</td>
                </tr>
            </table>
        </div>
HTML;
}

function show_supplier(&$pdo, $supplierid) {
    $sql = "SELECT * FROM S WHERE S = ? LIMIT 1;";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$supplierid]);
        $row = $stmt->fetch();
    }
    catch (PDOException $e) {
        echo "        <p class=\"error\">There was an error retrieving the supplier from the database.</p>\n";
        return;
    }
    print_supplier_details($row['S'], $row['SNAME'], $row['STATUS'], $row['CITY']);
    echo "        <div class=\"inventory\">\n";
    $sql = <<<SQL
SELECT PNAME,COLOR,WEIGHT,QTY FROM SP
INNER JOIN P USING(P)
WHERE S = ?;
SQL;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$supplierid]);
        $rows = $stmt->fetchAll();
    }
    catch (PDOException $e) {
        echo "            <p class=\"error\">There was an error retrieving the supplier from the database.</p>\n";
        return;
    }
    if(count($rows) == 0) {
        echo "            <p>This supplier has no inventory.</p>";
    } else {
        print_stock_table($rows);
    }
    echo "        </div>\n";
}
function show_part(&$pdo, $partid) {
    $sql = "SELECT * FROM P WHERE P = ? LIMIT 1;";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$partid]);
        $row = $stmt->fetch();
    }
    catch (PDOException $e) {
        echo "        <p class=\"error\">There was an error retrieving the part from the database.</p>\n";
        return;
    }
    print_part_details($row['P'], $row['PNAME'], $row['COLOR'], $row['WEIGHT']);
    echo "        <div class=\"inventory\">\n";
    $sql = <<<SQL
SELECT SNAME,CITY,QTY,STATUS FROM SP
INNER JOIN S USING(S)
WHERE P = ?;
SQL;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$_GET['partid']]);
        $rows = $stmt->fetchAll();
    }
    catch (PDOException $e) {
        echo "            <p class=\"error\">There was an error retrieving the part from the database.</p>\n";
        return;
    }
    if(count($rows) == 0) {
        echo "            <p>Out of stock.</p>";
    } else {
        print_parts_table($rows);
    }
    echo "        </div>\n";
}
function delete_multiple(&$pdo, $table, $column, array $values) {
    // delete multiple using str_repeat, adapted from example on https://stackoverflow.com/a/40432912
    $sql = "DELETE FROM `$table` WHERE `$column` IN (" . str_repeat("?, ", count($values) - 1) . "?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
    }
    catch (PDOException $e) {
        echo "<p class=\"error\">There was a problem deleting rows: ${$e->getMessage()}</p>";
    }
    return $stmt->rowCount();
}
function delete_supplier(&$pdo, array $supplierid) {
    // if force, delete all the inventory of supplier
    if(isset($_POST['force'])) {
        if(delete_multiple($pdo, 'SP', 'S', $supplierid) && delete_multiple($pdo, 'S', 'S', $supplierid)) {
            echo "<p>Suppliers and their inventory were deleted from the database.<br />\n<a href=\"?page=suppliers\">[OK]</a></p>\n";
        } else {
            echo "<p>No suppliers were deleted from the database.<br />\n<a href=\"?page=suppliers\" class=\"ok\">[OK]</a></p>\n";
        }
        return;
    }
    // check if deleting suppliers would cause a conflict
    $sql = "SELECT COUNT(*) FROM SP WHERE S IN (" . str_repeat("?, ", count($supplierid) - 1) . "?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($supplierid);
        $count = intval($stmt->fetchColumn());
    }
    catch (PDOException $e) {
        echo "<p class=\"error\">There was a problem searching the database: ${$e->getMessage()}</p>";
        return;
    }
    if($count > 0) {
        // if yes, print out a form that allows force, then return early
        echo <<<HTML
            <p class="warning">Warning: These suppliers have parts in their inventory. Are you sure you want to remove them?</p>
            <form method="POST">

HTML;
        foreach($supplierid as $s) {
            echo '                <input type="hidden" name="supplierid[]" value="' . htmlspecialchars($s) . "\" />\n";
        }
        echo <<<HTML
                <input type="checkbox" id="force" name="force" onchange="toggleBtn(this, 'submitButton')" /><label for="force">Force</label><br />
                <button type="submit" id="submitButton" name="action" value="remove" disabled>Yes, remove these suppliers</button>
            </form>
HTML;
    } else {
        // if no, delete the suppliers
        if(delete_multiple($pdo, 'S', 'S', $supplierid)) {
            echo "<p>Suppliers were deleted from the database.<br />\n<a href=\"?page=suppliers\">[OK]</a></p>\n";
        } else {
            echo "<p>No suppliers were deleted from the database.<br />\n<a href=\"?page=suppliers\" class=\"ok\">[OK]</a></p>\n";
        }
    }
}
function delete_part(&$pdo, array $partid) {
    // if force, delete all the inventory of part
    if(isset($_POST['force'])) {
        if(delete_multiple($pdo, 'SP', 'P', $partid) && delete_multiple($pdo, 'P', 'P', $partid)) {
            echo "<p>Parts and their inventory were deleted from the database.<br />\n<a href=\"?page=parts\">[OK]</a></p>\n";
        } else {
            echo "<p>No parts were deleted from the database.<br />\n<a href=\"?page=parts\" class=\"ok\">[OK]</a></p>\n";
        }
        return;
    }
    // check if deleting parts would cause a conflict
    $sql = "SELECT COUNT(*) FROM SP WHERE P IN (" . str_repeat("?, ", count($partid) - 1) . "?)";
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($partid);
        $count = intval($stmt->fetchColumn());
    }
    catch (PDOException $e) {
        echo "<p class=\"error\">There was a problem searching the database: ${$e->getMessage()}</p>";
        return;
    }
    if($count > 0) {
        // if yes, print out a form that allows force, then return early
        echo <<<HTML
            <p class="warning">Warning: There active suppliers for these parts. Are you sure you want to remove them?</p>
            <form method="POST">

HTML;
        foreach($partid as $p) {
            echo '                <input type="hidden" name="partid[]" value="' . htmlspecialchars($p) . "\" />\n";
        }
        echo <<<HTML
                <input type="checkbox" id="force" name="force" onchange="toggleBtn(this, 'submitButton')" /><label for="force">Force</label><br />
                <button type="submit" id="submitButton" name="action" value="remove" disabled>Yes, remove these parts</button>
            </form>
HTML;
    } else {
        // if no, delete the parts
        if(delete_multiple($pdo, 'P', 'P', $partid)) {
            echo "<p>Parts were deleted from the database.<br />\n<a href=\"?page=parts\">[OK]</a></p>\n";
        } else {
            echo "<p>No parts were deleted from the database.<br />\n<a href=\"?page=parts\" class=\"ok\">[OK]</a></p>\n";
        }
    }
}
function get_ids(&$pdo, $table) {
    $sql = "SELECT DISTINCT($table) FROM $table;";
    $ids = $pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    return $ids;
}
function get_next_id(&$pdo, $table) {
    // if the table is empty, next ID is the table name + the number 1, eg P1, S1.
    // otherwise, check what is the biggest ID and then add 1
    $sql = "SELECT COUNT(*) FROM $table;";
    $rows = $pdo->query($sql)->fetchColumn();
    if($rows == 0) {
        $nextid = 1;
    } else {
        $sql = "SELECT CAST(SUBSTR($table,2) AS INT) AS ID FROM $table ORDER BY ID DESC LIMIT 1;";
        $nextid = intval($pdo->query($sql)->fetchColumn()) + 1;
    }
    return $table . $nextid;
}
function print_add_supplier_form($nextsupplierid){
    echo <<<HTML
            <div class="add-supplier">
                <form method="POST">
                    <table style="float: left; margin-left: 1em;">
                        <tr>
                            <th>&nbsp;</th>
                            <th>Supplier&nbsp;ID</th>
                            <th>&nbsp;Name&nbsp;</th>
                            <th>&nbsp;Status&nbsp;</th>
                            <th>&nbsp;City&nbsp;</th>
                        <tr>
                            <td><span style="font-size: 0.75em; color: transparent; text-shadow: 0 0 0 green;">&#x2795;</span></td>
                            <td><input type="text" name="supplierid" value="$nextsupplierid" size=2 /></td>
                            <td><input type="text" name="sname" size=2 /></td>
                            <td><input type="number" name="status" value="0" style="width: 4em;" /></td>
                            <td><input type="text" name="city" size=4 /></td>
                        </tr>
                    </table>
                    <button type="submit" name="action" value="add" style="float: left; margin: 1em;">Add supplier</button>
                </form>
            </div>
HTML;
}
function print_add_part_form($nextpartid){
    echo <<<HTML
            <div class="add-part">
                <form method="POST">
                    <table style="float: left; margin-left: 1em;">
                        <tr>
                            <th>&nbsp;</th>
                            <th>Part&nbsp;ID</th>
                            <th>&nbsp;Name&nbsp;</th>
                            <th>&nbsp;Color&nbsp;</th>
                            <th>&nbsp;Weight&nbsp;</th>
                        <tr>
                            <td><span style="font-size: 0.75em; color: transparent; text-shadow: 0 0 0 green;">&#x2795;</span></td>
                            <td><input type="text" name="partid" value="$nextpartid" size=2 /></td>
                            <td><select name="pname"><option value="Bolt">Bolt</option><option value="Cam">Cam</option><option value="Cog">Cog</option><option value="Nut">Nut</option><option value="Screw">Screw</option></select></td>
                            <td><select name="color"><option value="Red">Red</option><option value="Green">Green</option><option value="Blue">Blue</option></select></td>
                            <td><input type="number" name="weight" value="0" style="width: 4em;" /></td>
                        </tr>
                    </table>
                    <button type="submit" name="action" value="add" style="float: left; margin: 1em;">Add part</button>
                </form>
            </div>
HTML;
}
function print_add_inventory_form(array $supplier_ids, array $part_ids){
    echo <<<HTML
            <div class="add-inventory">
                <form method="POST">
                    <table style="float: left; margin-left: 1em;">
                        <tr>
                            <td>&nbsp;</td>
                            <th>Supplier&nbsp;ID</th>
                            <th>Part&nbsp;ID</th>
                            <th>&nbsp;Quantity&nbsp;</th>
                        </tr>
                            <td><span style="font-size: 0.75em; color: transparent; text-shadow: 0 0 0 green;">&#x2795;</span></td>
                            <td><select name="supplierid">
HTML;
    foreach($supplier_ids as $supplier_id) {
        echo "<option value=\"$supplier_id\">$supplier_id</option>";
    }
    echo "</select></td>\n                            <td><select name=\"partid\">";
    foreach($part_ids as $part_id) {
        echo "<option value=\"$part_id\">$part_id</option>";
    }
    echo <<<HTML
                            <td><input type="number" name="qty" value="1" min="1" style="width: 4em;" /></td>
                        </tr>
                    </table>
                    <button type="submit" name="action" value="add" style="float: left; margin: 1em;">Add Inventory</button>
                </form>
            </div>
HTML;
}

function print_buy_form(array $supplier_ids, array $part_ids){
    echo <<<HTML
            <div class="buy-inventory">
                <form method="POST">
                    <table style="float: left; margin-left: 1em;">
                        <tr>
                            <td>&nbsp;</td>
                            <th>Supplier&nbsp;ID</th>
                            <th>Part&nbsp;ID</th>
                            <th>&nbsp;Quantity&nbsp;</th>
                        </tr>
                            <td><span style="font-size: 0.75em;">&#x1F6D2;</span></td>
                            <td><select name="supplierid">
HTML;
    foreach($supplier_ids as $supplier_id) {
        echo "<option value=\"$supplier_id\">$supplier_id</option>";
    }
    echo "</select></td>\n                            <td><select name=\"partid\">";
    foreach($part_ids as $part_id) {
        echo "<option value=\"$part_id\">$part_id</option>";
    }
    echo <<<HTML
                            <td><input type="number" name="qty" value="1" min="1" style="width: 4em;" /></td>
                        </tr>
                    </table>
                    <button type="submit" name="action" value="checkout" style="float: left; margin: 1em;">Buy Parts</button>
                </form>
            </div>
HTML;
}
function print_parts_page(&$pdo, $action) {
    switch($action) {
        case "add":
            add_part($pdo, $_POST['partid'], $_POST['pname'], $_POST['color'], $_POST['weight']);
            break;
        case "remove":
            delete_part($pdo,$_POST['partid']);
            break;
        case "none":

        case "list":
            print_parts_list($pdo);
            print_add_part_form(get_next_id($pdo, 'P'));
            break;
        case "show":
            show_part($pdo,$_GET['partid']);
            break;
    }
}
function print_suppliers_page(&$pdo, $action) {
    switch($action) {
        case "add":
            add_supplier($pdo, $_POST['supplierid'], $_POST['sname'], $_POST['status'], $_POST['city']);
            break;
        case "remove":
            delete_supplier($pdo,$_POST['supplierid']);
            break;
        case "none":

        case "list":
            print_suppliers_list($pdo);
            print_add_supplier_form(get_next_id($pdo, 'S'));
            break;
        case "show":
            show_supplier($pdo,$_GET['supplierid']);
            break;
    }
}
function print_buy_page(&$pdo, $action) {
    switch($action) {
        case "checkout":
            subtract_inventory($pdo, $_POST['supplierid'], $_POST['partid'], $_POST['qty']);
            break;
        case "none":
        case "list":
        default:
            print_buy_form(get_ids($pdo,'S'),get_ids($pdo,'P'));
            break;
    }
}
function print_inventory_page(&$pdo, $action) {
    switch($action) {
        case "add":
            add_inventory($pdo, $_POST['supplierid'], $_POST['partid'], $_POST['qty']);
            break;
        case "remove":
            delete_inventory($pdo,$_POST['supplierid'], $_POST['partid']);
            break;
        case "none":

        case "list":
            print_inventory_list($pdo);
            print_add_inventory_form(get_ids($pdo,'S'),get_ids($pdo,'P'));
            break;
    }
}
function print_body(&$pdo,$page,$action) {
    switch($page) {
        case "main":
            print_main_page($pdo);
            break;
        case "admin":
            print_admin_page($pdo, $action);
            break;
        case "buy":
            print_buy_page($pdo, $action);
            break;
        case "parts":
            print_parts_page($pdo, $action);
            break;
        case "suppliers":
            print_suppliers_page($pdo, $action);
            break;
        case "inventory":
            print_inventory_page($pdo, $action);
            break;
    }
}
if(isset($_GET['page']) && in_array($_GET['page'], ['admin', 'parts', 'suppliers', 'buy', 'inventory'])) {
    $page = $_GET['page'];
} else {
    $page = 'main';
}
if(isset($_GET['action']) && in_array($_GET['action'], ['list', 'show'])) {
    $action = $_GET['action'];
    $title = ucwords("$action $page");
} elseif(isset($_POST['action']) && in_array($_POST['action'], ['add', 'remove', 'reset'])) {
    $action = $_POST['action'];
    $title = ucwords("$action $page");
} elseif(isset($_POST['action']) && in_array($_POST['action'], ['checkout'])) {
    $action = $_POST['action'];
    $title = ucwords("$action");
} else {
    $action = 'none';
    $title = ucwords($page);
}
print_header($title, $page);
include '/var/www/php.inc/db.inc.php';
try {
    $dsn = "mysql:host=$servername;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    //echo "        <p><pre>"; print_r($_POST); echo "</pre></p>\n";
    print_body($pdo,$page,$action);
} catch (PDOException $e) {
    echo "<p class=error>Connection to database failed: ${$e->getMessage()}</p>";
}
?>
    </div>
</body>
</html>