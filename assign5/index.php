<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(isset($_GET["source"])) {
    highlight_file('index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noimageindex, nofollow, nosnippet">
    <title>Assignment 5 - Dog Visits</title>
    <link rel="icon" type="image/ico" href="/~z1871157/csci466/favicon.ico" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.min.css" />
    <link rel="stylesheet" href="/~z1871157/csci466/sql.css" />
</head>
<body>
    <h1>Assignment 5 - Dog Visits</h1>
    <h2>Schema</h2>
<?php
// Connect to default database z1871157
include '/home/data/www/z1871157/php.inc/db.inc.php';
include '../table_routines.inc.php';
$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) die("<p class=error>Connection failed" . $connection_connect_error . "</p>");
?>
    <h3>Dogs</h3>
    <p>
<pre><span class='keyword'>CREATE TABLE IF NOT EXIST</span> dogs <span class='punct'>(
   `</span>dog id<span class='punct'>`</span> <span class='type'>INT</span> <span class='extra'>AUTO_INCREMENT</span> <span class='keyword''>PRIMARY KEY</span><span class='punct'>,
   `</span>breed</span><span class='punct'>`</span> <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>255</span><span class='punct'>),
   `</span>name<span class='punct'>`</span> <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>30</span><span class='punct'>)
);</span></pre>
    </p>
    <p><?php print_describe($connection, 'dogs'); ?></p>
    </p><h3>Visits</h3>
    <p>
<pre><span class='keyword'>CREATE TABLE IF NOT EXIST</span> visits <span class='punct'>(
   `</span>visit id<span class='punct'>`</span> <span class='type'>INT</span> <span class='extra'>AUTO_INCREMENT</span> <span class='keyword''>PRIMARY KEY</span><span class='punct'>,
   `</span>dog id<span class='punct'>`</span> <span class='type'>INT</span><span class='punct'>,
   `</span>date</span><span class='punct'>`</span> <span class='type'>DATE</span><span class='punct'>,
   `</span>time<span class='punct'>`</span> <span class='type'>TIME</span><span class='punct'>,</span>
   <span class='keyword'>FOREIGN KEY</span> <span class='punct'>(`</span>dog id<span class='punct'>`)</span> <span class='keyword'>REFERENCES</span> dogs <span class='punct'>(`</span>dog id<span class='punct'>`)
);</span></pre>
   </p>
   <p><?php print_describe($connection, 'visits'); ?></p>
    <h2>Instance Data</h2>
    <h3>Dogs</h3>
    <p><?php print_table($connection, 'dogs'); ?></p>
    <h3>Visits</h3>
    <p><?php print_table($connection, 'visits'); ?></p>
    <h2>Views</h2>
    <h3>Dog Visits</h3>
    <p>
<pre><span class='keyword'>CREATE OR REPLACE VIEW</span> <span class='punct'>`</span>Dog Visits<span class='punct'>`</span> <span class='keyword'>AS</span>
<span class='keyword'>SELECT</span> <span class='punct'>`</span>name<span class='punct'>`,`</span>date<span class='punct'>`,`</span>time<span class='punct'>`</span> <span class='keyword'>FROM</span> visits
<span class='keyword'>INNER JOIN</span> dogs <span class='keyword'>USING</span><span class='punct'>(`</span>dog id<span class='punct'>`);</span></pre>
    </p>
    <p><?php print_table($connection, 'Dog Visits'); ?></p>
</body>
</html>