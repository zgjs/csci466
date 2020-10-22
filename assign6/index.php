<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if(isset($_GET["source"])) {
    highlight_file('index.php');
    exit();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noimageindex, nofollow, nosnippet">
    <title>Assignment 6 - SQL DQL - Single Table</title>
    <link rel="icon" type="image/ico" href="/~z1871157/csci466/favicon.ico" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.min.css" />
    <link rel="stylesheet" href="/~z1871157/csci466/sql.css" />
</head>
<body>
    <h1>Assignment 6 - SQL DQL - Single Table</h1>
    <h2>Introduction</h2>
    <p>Test each of the SQL statements, and then put them into the script, making sure to comment each one.</p>
    <p>This is a fairly large database, so it is recommended that you add LIMIT 50 to your SQL statements when testing.</p>
    <h2>SQL Statements</h2>
    <p>
        Large results are limited to 10 records on this page.
    </p>
    <ol>
        <li>
            <p>Select the BabyName database.</p>
            <p>
<pre><span class="keyword">USE</span> BabyName<span class="punct">;</span></pre>
            </p>
            <?php
include '/home/data/www/z1871157/php.inc/db.inc.php';
include '../table_routines.inc.php';
// Connect to BabyName database
$database = 'BabyName';
$connection = new mysqli($servername, $username, $password, $database);
if ($connection->connect_error) {
    die("<p class=error>Connection failed" . $connection_connect_error . "</p>\n");
} else {
    echo "<p><pre>Database changed</pre></p>\n";
}
?>
        </li>
        <li>
            <p>Show all of the tables in the database.</p>
            <p>
<pre><span class="keyword">SHOW</span> <span class="keyword">TABLES</span><span class="punct">;</span></pre>
            </p>
            <p><?php print_show_tables($connection, $database, FALSE); ?></p>
        </li>
        <li>
            <p>Show all of the columns and their types for each table in the database.</p>
            <p><?php
$tables = get_tables($connection);
foreach($tables as $table) {
    print_describe($connection, $table);
}
            ?></p>
        </li>
        <li>
            <p>
                Show all of the years (once only) where your first name appears. 
                Some people's names may not be present in the databse. 
                If your name is one of those, then use 'Chad' if you are male, or 'Stacy' if you are female. 
                If you don't feel you fit in to one of those, feel free to use 'Pat'.
            </p>
            <p>
<pre><span class="keyword">SELECT</span> <span class="keyword">DISTINCT</span> year <span class="keyword">FROM</span> BabyName <span class="keyword">WHERE</span> name <span class="punct">=</span> <span class="string">'Pat'</span><span class="punct">;</span></pre>
            </p>
            <p><?php print_query_results($connection, "SELECT DISTINCT year FROM BabyName WHERE name = 'Pat' LIMIT 10;"); ?></p>
        </li>
        <li>
            <p>Show all of the names from your birth year. (Showing each name only once.)</p>
            <p>
<pre><span class="keyword">SELECT</span> <span class="keyword">DISTINCT</span> name <span class="keyword">FROM</span> BabyName <span class="keyword">WHERE</span> year <span class="punct">=</span> <span class="number">2000</span><span class="punct">;</span></pre>
            </p>
            <p><?php print_query_results($connection, "SELECT DISTINCT name FROM BabyName WHERE year = 2000 LIMIT 10;"); ?></p>
        </li>
        <li>
            <p>Display the most popular male and female names from the year of your birth.</p>
            <p>
<pre><span class="punct">(</span>
    <span class="keyword">SELECT</span> name,gender
    <span class="keyword">FROM</span> BabyName
    <span class="keyword">WHERE</span> gender <span class="punct">=</span> <span class="string">'M'</span> <span class="punct">AND</span> year <span class="punct">=</span> <span class="number">2000</span>
    <span class="keyword">ORDER BY</span> count <span class="keyword">DESC</span>
    <span class="keyword">LIMIT</span> <span class="number">1</span>
<span class="punct">)</span> UNION <span class="punct">(</span>
    <span class="keyword">SELECT</span> name,gender
    <span class="keyword">FROM</span> BabyName
    <span class="keyword">WHERE</span> gender <span class="punct">=</span> <span class="string">'F'</span> <span class="punct">AND</span> year <span class="punct">=</span> <span class="number">2000</span>
    <span class="keyword">ORDER BY</span> count <span class="keyword">DESC</span>
    <span class="keyword">LIMIT</span> <span class="number">1</span>
<span class="punct">);</span></pre>
            </p>
            <p><?php print_query_results($connection, "(SELECT name,gender FROM BabyName WHERE gender = 'M' AND year = 2000 ORDER BY count DESC LIMIT 1) UNION (SELECT name,gender FROM BabyName WHERE gender = 'F' AND year = 2000 ORDER BY count DESC LIMIT 1);"); ?></p>
        </li>
        <li>
            <p>Show all the information available about names similar to your name (or the one you adopted from above), sorted in alphabetical order by name, then within that, by count, and finally, by the year.</p>
            <p>
<pre><span class="keyword">SELECT</span> * <span class="keyword">FROM</span> BabyName <span class="keyword">WHERE</span> name <span class="keyword">LIKE</span> <span class="string">'%Pat%'</span> <span class="keyword">ORDER BY</span> name,count,year<span class=punct>;</span></pre>
            </p>
            <p><?php print_query_results($connection, "SELECT * FROM BabyName WHERE name LIKE '%Pat%' ORDER BY name,count,year LIMIT 10;"); ?></p>
        </li>
        <li>
            <p>Show how many rows there are in the table.</p>
            <p>
<pre><span class="keyword">SELECT</span> <span class="function">COUNT</span><span class="punct">(</span>*<span class="punct">)</span> <span class="keyword">FROM</span> BabyName<span class="punct">;</span></pre>
            </p>
            <p><?php print_query_results($connection, "SELECT COUNT(*) FROM BabyName;"); ?></p>
        </li>
        <li>
            <p>Show how many names there were in the year 1972.</p>
            <p>
<pre><span class="keyword">SELECT</span> <span class="function">COUNT</span><span class="punct">(</span><span class="keyword">DISTINCT</span> name<span class="punct">)</span> <span class="keyword">FROM</span> BabyName <span class="keyword">WHERE</span> year <span class="punct">=</span> <span class="number">1972</span><span class="punct">;</span></pre>
            </p>
            <p><?php print_query_results($connection, "SELECT COUNT(DISTINCT name) FROM BabyName WHERE year = 1972;"); ?></p>
        </li>
        <li>
            <p>Show how many names are in the table for each sex from the year 1953.</p>
            <p>
<pre><span class="keyword">SELECT</span> <span class="function">COUNT</span><span class="punct">(</span><span class="keyword">DISTINCT</span> name<span class="punct">)</span> <span class="keyword">AS</span> count<span class="punct">,</span> gender <span class="keyword">FROM</span> BabyName <span class="keyword">WHERE</span> year <span class="punct">=</span> <span class="number">1953</span> <span class="keyword">GROUP BY</span> gender<span class="punct">;</span></pre>
            </p>
            <p><?php print_query_results($connection, "SELECT COUNT(DISTINCT name) AS count, gender FROM BabyName WHERE year = 1953 GROUP BY gender;"); ?></p>
        </li>
        <li>
            <p>List how many different names there are for each place.</p>
            <p>
<pre><span class="keyword">SELECT</span> <span class="function">COUNT</span><span class="punct">(</span><span class="keyword">DISTINCT</span> name<span class="punct">)</span> <span class="keyword">AS</span> count<span class="punct">,</span> place <span class="keyword">FROM</span> BabyName <span class="keyword">GROUP BY</span> place<span class="punct">;</span></pre>
            </p>
            <p><?php print_query_results($connection, "SELECT COUNT(DISTINCT name) AS count, place FROM BabyName GROUP BY place LIMIT 10;"); ?></p>
        </li>
    </ol>
</body>
</html>
