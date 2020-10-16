<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(isset($_GET["highlight"])) {
    highlight_file('index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noimageindex, nofollow, nosnippet">
    <title>Assignment 5</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.min.css">
    <link rel="stylesheet" href="/~z1871157/csci466/sql.css">
</head>
<body>
    <h1>Assignment 5</h1>
    <h2>Schema</h2>
<?php
// Connect to default database z1871157
include '/home/turing/z1871157/php.inc/db.inc.php';
$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) die("<p class=error>Connection failed" . $connection_connect_error . "</p>");
?>
    <h3>Dogs</h3>
    <p>
<pre><span class='keyword'>CREATE TABLE IF NOT EXIST</span> dogs <span class='punct'>(
   `</span>dog id<span class='punct'>`</span> <span class='type'>INT</span> <span class='extra'>AUTO_INCREMENT</span> <span class='keyword''>PRIMARY KEY</span><span class='punct'>,
   `</span>breed</span><span class='punct'>`</span> <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>255</span><span class='punct'>),
   `</span>name<span class='punct'>`</span> <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>30</span><span class='punct'>)
);</span>
<span class='keyword'>DESCRIBE</span> dogs<span class='punct'>;</span>
</pre>
    </p>
    <p>
<?php
$sql = "DESCRIBE dogs;";
$result = $connection->query($sql);
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
    echo "There's no schema for dogs!";
}
?>
    </p><h3>Visits</h3>
    <p>
<pre><span class='keyword'>CREATE TABLE IF NOT EXIST</span> dogs <span class='punct'>(
   `</span>visit id<span class='punct'>`</span> <span class='type'>INT</span> <span class='extra'>AUTO_INCREMENT</span> <span class='keyword''>PRIMARY KEY</span><span class='punct'>,
   `</span>dog id<span class='punct'>`</span> <span class='type'>INT</span><span class='punct'>,
   `</span>date</span><span class='punct'>`</span> <span class='type'>DATE</span><span class='punct'>,
   `</span>time<span class='punct'>`</span> <span class='type'>TIME</span><span class='punct'>,
   <span class='keyword'>FOREIGN KEY</span> <span class='punct'>(`</span>dog id<span class='punct'>`)</span> <span class='keyword'>REFERENCES</span> dogs <span class='punct'>(`</span>dog id<span class='punct'>`)
);</span>
<span class='keyword'>DESCRIBE</span> dogs<span class='punct'>;</span>
</pre>
   </p>
    <p>
<?php
$sql = "DESCRIBE visits;";
$result = $connection->query($sql);
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
    echo "There's no schema for dogs!";
}
?>
    </p>
    <h2>Instance Data</h2>
    <h3>Dogs</h3>
    <p>
<pre><span class='keyword'>SELECT</span> * <span class='keyword'>FROM</span> dogs<span class='punct'>;</span></pre>
    </p>
<?php
$sql = "SELECT * FROM dogs;";
$result = $connection->query($sql);
if($result->num_rows > 0) {
    echo <<<TH
    <table>
        <tr>
            <th>dog id</th>
            <th>name</th>
            <th>breed</th>
        </tr>
TH;
    while($row = $result->fetch_assoc()) {
        echo <<<TR
        <tr>
            <td>{$row["dog id"]}</td>
            <td>{$row["name"]}</td>
            <td>{$row["breed"]}</td>
        </tr>
TR;
    }
    echo "    </table>\n";
} else {
    echo "There aren't any dogs!";
}
?>
    <h3>Dog Visits</h3>
    <p>
<pre><span class='keyword'>SELECT</span> * <span class='keyword'>FROM</span> dogs<span class='punct'>;</span></pre>
    </p>
<?php
$sql = <<<SQL
SELECT * FROM visits;
SQL;
$result = $connection->query($sql);
if($result->num_rows > 0) {
    echo <<<TH
    <table>
        <tr>
            <th>visit id</th>
            <th>dog id</th>
            <th>date</th>
            <th>time</th>
        </tr>
TH;
    while($row = $result->fetch_assoc()) {
        echo <<<TR
        <tr>
            <td>{$row["visit id"]}</td>
            <td>{$row["dog id"]}</td>
            <td>{$row["date"]}</td>
            <td>{$row["time"]}</td>
        </tr>
TR;
    }
    echo "    </table>\n";
} else {
    echo "There aren't any dog visits!";
}
?>
    <h2>Views</h2>
    <h3>Dog Visits</h3>
    <p>
<pre><span class='keyword'>CREATE VIEW</span> <span class='punct'>`</span>Dog Visits<span class='punct'>`,`</span>date<span class='punct'>`,`</span>time<span class='punct'>`</span> <span class='keyword'>AS</span>
<span class='keyword'>SELECT</span> <span class='punct'>`</span>name<span class='punct'>`,`</span>date<span class='punct'>`,`</span>time<span class='punct'>`</span> <span class='keyword'>FROM</span> visits
<span class='keyword'>INNER JOIN</span> dogs <span class='keyword'>USING</span><span class='punct'>(`</span>dog id<span class='punct'>`);</span>
<span class='keyword'>SELECT</span> * <span class='keyword'>FROM</span> <span class='punct'>`</span>Dog Visits<span class='punct'>`;</span></pre></pre>
    </p>
<?php
$sql = <<<SQL
SELECT `name`,`date`,`time` FROM visits
INNER JOIN dogs USING(`dog id`);
SQL;
$result = $connection->query($sql);
if($result->num_rows > 0) {
    echo <<<TH
    <table>
        <tr>
            <th>name</th>
            <th>date</th>
            <th>time</th>
        </tr>
TH;
    while($row = $result->fetch_assoc()) {
        echo <<<TR
        <tr>
            <td>{$row["name"]}</td>
            <td>{$row["date"]}</td>
            <td>{$row["time"]}</td>
        </tr>
TR;
    }
    echo "    </table>\n";
} else {
    echo "There aren't any dog visits!";
}
?>
</body>
</html>