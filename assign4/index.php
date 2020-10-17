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
    <title>Assignment 4 - Day Spa</title>
    <link rel="icon" type="image/ico" href="/~z1871157/csci466/favicon.ico" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/water.min.css" />
    <link rel="stylesheet" href="/~z1871157/csci466/sql.css" />
    <style>
@media (prefers-color-scheme: light) {
    p.erd { background-color: #eeeeee; }
}
@media (prefers-color-scheme: dark) {
    p.erd { background-color: #667788; }
}
.primarykey {
    text-decoration: underline;
}

.foreignkey {
  position: relative;
  display: inline-block;
}

.foreignkey .referencedkey {
  visibility: hidden;
  width: 180px;
  background-color: black;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 5px 0;

  /* Position the foreignkey */
  position: absolute;
  z-index: 1;
}

.foreignkey:hover .referencedkey {
  visibility: visible;
}
body {
    max-width: 1110px !important;
}
</style>
</style>
    </style>
</head>
<body>
    <h1>Assignment 4 - Day Spa</h1>
    <ol>
        <li><a href="#requirements">Requirements</a></li>
        <li><a href="#erd">Entity Relationship Diagram</a></li>
        <li><a href="#3nf">3NF Relations</a>
        <ol>
            <li><a href="#3nf_entities">Entities</a></li>
            <li><a href="#3nf_relationships">More-than-binary Relationsips</a></li>
        </ol>
        </li>
        <li><a href="#schema">SQL Schema</a>
        <ol>
            <li><a href="#schema_entities">Entities</a>
            <ol>
                <li><a href="#schema_service">Service</a></li>
                <li><a href="#schema_person">Person</a></li>
                <li><a href="#schema_client">Client</a></li>
                <li><a href="#schema_employee">Employee</a></li>
            </ol>
            </li>
            <li><a href="#schema_relationships">More-than-binary Relationsips</a>
            <ol>
                <li><a href="#schema_appointment">Appointment</a></li>
                <li><a href="#schema_preferredfor">PreferredFor</a></li>
            </ol>
            </li>
        </ol>
        </li>
        <li><a href="#instance">Instance Data</a>
        <ol>
            <li><a href="#instance_entities">Entities</a>
            <ol>
                <li><a href="#instance_service">Service</a></li>
                <li><a href="#instance_person">Person</a></li>
                <li><a href="#instance_client">Client</a></li>
                <li><a href="#instance_employee">Employee</a></li>
            </ol>
            </li>
            <li><a href="#instance_relationships">More-than-binary Relationsips</a>
            <ol>
                <li><a href="#instance_appointment">Appointment</a></li>
                <li><a href="#instance_preferredfor">PreferredFor</a></li>
            </ol>
            </li>
        </ol>
        <li><a href="#views">Views</a>
        <ol>
            <li><a href="#view_services">Service List</a></li>
            <li><a href="#view_clients">Client List</a></li>
            <li><a href="#view_employees">Employee List</a></li>
            <li><a href="#view_client_preferences">Client Preferences</a></li>
            <li><a href="#view_clients_today">Clients Scheduled Today</a></li>
            <li><a href="#view_clients_week">Clients Appointments This Week</a></li>
            <li><a href="#view_employees_week">Employees Scheduled This Week</a></li>
            <li><a href="#view_reminders">Appointment Reminders</a></li>
            <li><a href="#view_billing">Billing</a></li>
        </ol>
        </li>
    </ol>
    <h2 id="requirements">Requirements</h2>
    <p>Serenity Springs Day Spa is a full-service spa that provides hair, makeup, manicures, massages, and waxes. They would like to update their ﬁling sytem, which has been entirely paper-based up until now. They would like to be able to automatically bill clients, remind clients of upcoming appointments, print lists of employees, print employees’ work schedules, and list the clients scheduled for a particular day. </p>
    <p>They need to store basic information like name, phone number, physical address, and email, for both clients and employees. Employees have a job title, a specialty, and a pay rate that also need to be stored. Clients can choose a preferred employee for each of the services offered by the spa.</p>
    <p>Although each employee has a specialty, they should be able to perform any of the services offered by the spa. The spa would like to beable to automatically generate a complete list of services and their prices from their new database.</p>
    <h2 id="erd">Entity Relationship Diagram</h2>
    <p align=center class="erd"><img src="dayspa.png" width=510 height=820></p>
    <h2 id="3nf">3NF Relations</h2>
    <h3 id="3nf_entities">Entities</h3>
    <ul>
        <li><b>Services</b>(<span class="primarykey">Service Name, Price)</li>
        <li><b>Persons</b>(<span class="primarykey">ID</span>, Name, Phone Number, Physical Address, Email)</li>
        <li><b>Employees</b>(<span class="primarykey foreignkey">Employee ID†<span class="referencedkey">Person(ID)</span></span>, Job Title, <span class="foreignkey">Service Name†<span class="referencedkey">Service(Name)</span></span>, Pay Rate)</li>
        <li><b>Clients</b>(<span class="primarykey foreignkey">Client ID†<span class="referencedkey">Person(ID)</span></span>)</li>
    </ul>
    <h3 id="3nf_relationships">More-than-binary Relationships</h3>
    <ul>
        <li><b>PreferredFor</b>(<span class="primarykey foreignkey">Client ID†<span class="referencedkey">Client(Client ID)</span></span>, <span class="primarykey foreignkey">Employee ID†<span class="referencedkey">Employee(Employee ID)</span></span>, <span class="primarykey foreignkey">Service Name†<span class="referencedkey">Service(Name)</span></span>)</li>
        <li><b>Appointments</b>(<span class="primarykey foreignkey">Service Name†<span class="referencedkey">Service(Name)</span></span>, <span class="primarykey foreignkey">Client ID†<span class="referencedkey">Client(Client ID)</span></span>, <span class="primarykey foreignkey">Employee ID†<span class="referencedkey">Employee(Employee ID)</span></span>, <span class="primarykey">Date</span>)</li>
    </ul>
    <h2 id="schema">SQL Schema</h2>
<?php
// Connect to default database z1871157
include '/home/data/www/z1871157/php.inc/db.inc.php';
include '../table_routines.inc.php';
$connection = new mysqli($servername, $username, $password, $dbname);
if ($connection->connect_error) die("<p class=error>Connection failed" . $connection_connect_error . "</p>");
?>
    <h3 id="schema_entities">Entities</h3>
    <h4 id="schema_service">Service</h4>
    <p>
<pre><span class='keyword'>CREATE TABLE</span> Service<span class='punct'>(</span>
   Name <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>20</span><span class='punct'>)</span> <span class='keyword'>PRIMARY KEY</span><span class='punct'>,</span>
   Price <span class='type'>DECIMAL</span><span class='punct'>(</span><span class='number'>5</span><span class='punct'>,</span><span class='number'>2</span><span class='punct'>) NOT NULL
);</span>
</pre>
    </p>
    <p><?php print_describe($connection, 'Service'); ?></p>
    <h4 id="schema_person">Person</h4>
    <p>
<pre><span class='keyword'>CREATE TABLE</span> Person<span class='punct'>(</span>
   ID <span class='type'>INT</span> <span class='extra'>AUTO_INCREMENT</span> <span class='keyword'>PRIMARY KEY</span><span class='punct'>,</span>
   Name <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>255</span><span class='punct'>) NOT NULL,</span>
   <span class='comment'>-- Phone numbers are normally in format "(000) 000-0000" but might be as long as "+000 00 0000 0000, 000000"</span>
  `Phone Number` <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>25</span><span class='punct'>),</span>
  `Physical Address` <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>255</span><span class='punct'>),</span>
  `Email` <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>255</span><span class='punct'>)</span>
<span class='punct'>);</span></pre>
    </p>
    <p><?php print_describe($connection, 'Person'); ?></p>
    <h4 id="schema_employee">Employee</h4>
    <p>
<pre><span class='keyword'>CREATE TABLE</span> Employee<span class='punct'>(</span>
   ID <span class='type'>INT</span> <span class='keyword'>PRIMARY KEY</span><span class='punct'>,</span>
   `Job Title` <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>255</span><span class='punct'>) NOT NULL,</span>
   `Pay Rate` <span class='type'>DECIMAL</span><span class='punct'>(</span><span class='number'>5</span><span class='punct'>,</span><span class='number'>2</span><span class='punct'>) NOT NULL,</span>
   Specialty <span class='type'>VARCHAR</span><span class='punct'>(</span><span class='number'>20</span><span class='punct'>) NOT NULL ,</span>
   <span class='keyword'>FOREIGN KEY</span> <span class='punct'>(</span>ID<span class='punct'>)</span> <span class='keyword'>REFERENCES</span> Person<span class='punct'>(</span>ID<span class='punct'>),</span>
   <span class='keyword'>FOREIGN KEY</span> <span class='punct'>(</span>Specialty<span class='punct'>)</span> <span class='keyword'>REFERENCES</span> Service<span class='punct'>(</span>Name<span class='punct'>)
);</span></pre>
    </p>
    <p><?php print_describe($connection, 'Employee'); ?></p>
    <h4 id="schema_client">Client</h4>
    <p>
<pre><span class='keyword'>CREATE TABLE</span> Client<span class='Clone Hunch'>(</span>
   ID <span class='type'>INT</span> <span class='keyword'>PRIMARY KEY</span><span class='punct'>,</span>
<span class='punct'>);</span></pre>
    </p>
    <p><?php print_describe($connection, 'Client'); ?></p>
    <h3 id="schema_relationships">More-than-binary relationships</h3>
    <h4 id="schema_appointment">Appointment</h4>
    <p>
<pre><span class='keyword'>CREATE TABLE</span> Appointment<span class='punct'>(</span>
   `Service Name` <span class="type">VARCHAR</span><span class="punct">(</span><span class="number">20</span><span class="punct">),</span>
   `Client ID` <span class="type">INT</span><span class="punct">,</span>
   `Employee ID` <span class="type">INT</span><span class="punct">,</span>
   `Date` <span class="type">DATETIME</span><span class="punct">,</span>
   <span class="keyword">PRIMARY KEY</span> <span class="punct">(</span>`Service Name`<span class="punct">,</span> `Client ID`<span class="punct">,</span> `Employee ID`<span class="punct">,</span> `Date`<span class="punct">),</span>
   <span class="keyword">FOREIGN KEY</span> <span class="punct">(</span>`Service Name`<span class="punct">)</span> <span class="keyword">REFERENCES</span> Service<span class="punct">(</span>Name<span class="punct">),</span>
   <span class="keyword">FOREIGN KEY</span> <span class="punct">(</span>`Client ID`<span class="punct">)</span> <span class="keyword">REFERENCES</span> Client<span class="punct">(</span>ID<span class="punct">),</span>
   <span class="keyword">FOREIGN KEY</span> <span class="punct">(</span>`Employee ID`<span class="punct">)</span> <span class="keyword">REFERENCES</span> Client<span class="punct">(</span>ID<span class="punct">)
);</span></pre>
    </p>
    <p><?php print_describe($connection, 'Appointment'); ?></p>
    <h4 id="schema_preferredfor">PreferredFor</h4>
    <p>
<pre><span class='keyword'>CREATE TABLE</span> PreferredFor<span class='punct'>(</span>
   `Service Name` <span class="type">VARCHAR</span><span class="punct">(</span><span class="number">20</span><span class="punct">),</span>
   `Client ID` <span class="type">INT</span><span class="punct">,</span>
   `Employee ID` <span class="type">INT</span><span class="punct">,</span>
   <span class="keyword">PRIMARY KEY</span> <span class="punct">(</span>`Service Name`<span class="punct">,</span> `Client ID`<span class="punct">,</span> `Employee ID`<span class="punct">),</span>
   <span class="keyword">FOREIGN KEY</span> <span class="punct">(</span>`Service Name`<span class="punct">)</span> <span class="keyword">REFERENCES</span> Service<span class="punct">(</span>Name<span class="punct">),</span>
   <span class="keyword">FOREIGN KEY</span> <span class="punct">(</span>`Client ID`<span class="punct">)</span> <span class="keyword">REFERENCES</span> Client<span class="punct">(</span>ID<span class="punct">),</span>
   <span class="keyword">FOREIGN KEY</span> <span class="punct">(</span>`Employee ID`<span class="punct">)</span> <span class="keyword">REFERENCES</span> Client<span class="punct">(</span>ID<span class="punct">)
);</span></pre>
    </p>
    <p><?php print_describe($connection, 'PreferredFor'); ?></p>
    <h2 id="instance">Instance Data</h2>
    <h3 id="instance_entities">Entities</h3>
    <h4 id="instance_service">Service</h4>
    <p><?php print_table($connection, 'Service', 5); ?></p>
    <h4 id="instance_person">Person</h4>
    <p><?php print_table($connection, 'Person', 5); ?></p>
    <h4 id="instance_client">Client</h4>
    <p><?php print_table($connection, 'Client', 5); ?></p>
    <h4 id="instance_employee">Employee</h4>
    <p><?php print_table($connection, 'Employee', 5); ?></p>
    <h3 id="instance_relationships">More-than-binary Relationships</h3>
    <h4 id="instance_appointment">Appointment</h4>
    <p><?php print_table($connection, 'Appointment', 5); ?></p>
    <h4 id="instance_preferredfor">PreferredFor</h4>
    <p><?php print_table($connection, 'PreferredFor', 5); ?></p>
    <h2 id="views">Views</h2>
    <h3 id="view_services">Service List</h3>
    <p>
<pre><span class="keyword">CREATE VIEW</span> `Services List` <span class="keyword">AS</span>
<span class="keyword">SELECT</span> <span class="punct">`</span>Name<span class="punct">`</span> <span class="keyword">AS</span> <span class="string">'Service'</span><span class="punct">,</span><span class="function">CONCAT</span><span class="punct">(</span><span class="string">'$'</span><span class="punct">,</span>Price<span class="punct">)</span> <span class="keyword">AS</span> Price <span class="keyword">FROM</span> Service<span class="punct">;</span>
</pre>
    </p>
    <p><?php print_table($connection, 'Services List'); ?></p>
    <h3 id="view_clients">Client List</h3>
    <p>
    <pre><span class="keyword">CREATE VIEW</span> `Client List` <span class="keyword">AS</span>
<span class="keyword">SELECT</span> <span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> `Phone Number`<span class="punct">,</span> `Physical Address`<span class="punct">,</span> <span class="punct">`</span>Email<span class="punct">`</span>
<span class="keyword">FROM</span> <span class="punct">`</span>Client<span class="punct">`</span> <span class="keyword">INNER</span> <span class="keyword">JOIN</span> <span class="punct">`</span>Person<span class="punct">`</span> <span class="keyword">USING</span><span class="punct">(</span><span class="punct">`</span>ID<span class="punct">`</span><span class="punct">)</span><span class="punct">;</span>
</pre>
    </p>
    <p><?php print_table($connection, 'Client List'); ?></p>
    <h3 id="view_employees">Employee List</h3>
    <p>
<pre><span class="keyword">CREATE VIEW</span> `Employee List` <span class="keyword">AS</span>
<span class="keyword">SELECT</span> <span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> `Job Title`<span class="punct">,</span> <span class="punct">`</span>Specialty<span class="punct">`</span><span class="punct">,</span> `Pay Rate`<span class="punct">,</span> `Phone Number`<span class="punct">,</span> `Physical Address`<span class="punct">,</span> <span class="punct">`</span>Email<span class="punct">`</span>
<span class="keyword">FROM</span> <span class="punct">`</span>Employee<span class="punct">`</span> <span class="keyword">INNER</span> <span class="keyword">JOIN</span> <span class="punct">`</span>Person<span class="punct">`</span> <span class="keyword">USING</span><span class="punct">(</span><span class="punct">`</span>ID<span class="punct">`</span><span class="punct">)</span><span class="punct">;</span>
</pre>
    </p>
    <p><?php print_table($connection, 'Employee List'); ?></p>
    <h3 id="view_client_preferences">Client Preferences</h3>
    <p>
    <pre><span class="keyword">CREATE VIEW</span> `Client Preferences Matrix` <span class="keyword">AS</span>
<span class="keyword">SELECT</span> Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> <span class="keyword">AS</span> <span class="string">'Client'</span><span class="punct">,</span>
    <span class="function">MAX</span><span class="punct">(</span><span class="keyword">case</span> <span class="keyword">WHEN</span> `Service Name` <span class="punct">=</span> <span class="string">'Hair'</span> <span class="keyword">THEN</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> <span class="keyword">ELSE</span> <span class="string">''</span> <span class="keyword">END</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Hair'</span><span class="punct">,</span>
    <span class="function">MAX</span><span class="punct">(</span><span class="keyword">case</span> <span class="keyword">WHEN</span> `Service Name` <span class="punct">=</span> <span class="string">'Makeup'</span> <span class="keyword">THEN</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> <span class="keyword">ELSE</span> <span class="string">''</span> <span class="keyword">END</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Makeup'</span><span class="punct">,</span>
    <span class="function">MAX</span><span class="punct">(</span><span class="keyword">case</span> <span class="keyword">WHEN</span> `Service Name` <span class="punct">=</span> <span class="string">'Manicure'</span> <span class="keyword">THEN</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> <span class="keyword">ELSE</span> <span class="string">''</span> <span class="keyword">END</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Manicure'</span><span class="punct">,</span>
    <span class="function">MAX</span><span class="punct">(</span><span class="keyword">case</span> <span class="keyword">WHEN</span> `Service Name` <span class="punct">=</span> <span class="string">'Massage'</span> <span class="keyword">THEN</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> <span class="keyword">ELSE</span> <span class="string">''</span> <span class="keyword">END</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Massage'</span><span class="punct">,</span>
    <span class="function">MAX</span><span class="punct">(</span><span class="keyword">case</span> <span class="keyword">WHEN</span> `Service Name` <span class="punct">=</span> <span class="string">'Wax'</span> <span class="keyword">THEN</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> <span class="keyword">ELSE</span> <span class="string">''</span> <span class="keyword">END</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Wax'</span>
<span class="keyword">FROM</span> PreferredFor
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> Person <span class="keyword">AS</span> Client <span class="keyword">ON</span> Client<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span> PreferredFor<span class="punct">.</span>`Client ID`
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> Person <span class="keyword">AS</span> Employee <span class="keyword">ON</span> Employee<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span> PreferredFor<span class="punct">.</span>`Employee ID`
<span class="keyword">GROUP</span> <span class="keyword">BY</span> Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">;</span>
</pre>
    </p>
    <p><?php print_table($connection, 'Client Preferences Matrix'); ?></p>
    <h3 id="view_clients_today">Clients Scheduled Today</h3>
    <p>
<pre><span class="keyword">CREATE VIEW</span> `Clients Scheduled Today` <span class="keyword">AS</span>
<span class="keyword">SELECT</span> <span class="type">DATE_FORMAT</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">,</span><span class="string"> </span><span class="string">"</span><span class="format">%h</span><span class="string"> </span><span class="format">%i</span><span class="string"> </span><span class="format">%p</span><span class="string">"</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Time'</span><span class="punct">,</span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> <span class="keyword">AS</span> <span class="string">'Client'</span><span class="punct">,</span> Appointment<span class="punct">.</span>`Service Name` <span class="keyword">AS</span> <span class="string">'Service'</span><span class="punct">,</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> <span class="keyword">AS</span> <span class="string">'Employee'</span>
<span class="keyword">FROM</span> Appointment
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> Person <span class="keyword">AS</span> Client <span class="keyword">ON</span> Client<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span>  Appointment<span class="punct">.</span>`Client ID`
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> Person <span class="keyword">AS</span> Employee <span class="keyword">ON</span> Employee<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span>  Appointment<span class="punct">.</span>`Employee ID`
<span class="keyword">WHERE</span> <span class="function">DATE</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span class="type">CURDATE</span><span class="punct">(</span><span class="punct">)</span>
<span class="keyword">GROUP</span> <span class="keyword">BY</span> <span class="function">TIME</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span><span class="punct">,</span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">;</span></pre>
    </p>
    <p><?php print_table($connection, 'Clients Scheduled Today'); ?></p>
    <h3 id="view_clients_week">Clients Appointments This Week</h3>
    <p>
<pre><span class="keyword">CREATE VIEW</span> `Client Appointments <span class="keyword">By</span> <span class="keyword">Day</span>` <span class="keyword">AS</span>
<span class="keyword">SELECT</span> <span class="keyword">DISTINCT</span> <span class="type">DATE_FORMAT</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">,</span><span class="string"> </span><span class="string">"</span><span class="format">%h</span><span class="string"> </span><span class="format">%i</span><span class="string"> </span><span class="format">%p</span><span class="string">"</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Time'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>6</span><span class="punct">,</span><span class="type">CONCAT</span><span class="punct">(</span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> <span class="string">'-'</span><span class="punct">,</span> Appointment<span class="punct">.</span>`Service Name`<span class="punct">)</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Available]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Sunday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>0</span><span class="punct">,</span><span class="type">CONCAT</span><span class="punct">(</span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> <span class="string">'-'</span><span class="punct">,</span> Appointment<span class="punct">.</span>`Service Name`<span class="punct">)</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Available]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Monday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>1</span><span class="punct">,</span><span class="type">CONCAT</span><span class="punct">(</span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> <span class="string">'-'</span><span class="punct">,</span> Appointment<span class="punct">.</span>`Service Name`<span class="punct">)</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Available]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Tuesday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>2</span><span class="punct">,</span><span class="type">CONCAT</span><span class="punct">(</span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> <span class="string">'-'</span><span class="punct">,</span> Appointment<span class="punct">.</span>`Service Name`<span class="punct">)</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Available]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Wednesday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>3</span><span class="punct">,</span><span class="type">CONCAT</span><span class="punct">(</span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> <span class="string">'-'</span><span class="punct">,</span> Appointment<span class="punct">.</span>`Service Name`<span class="punct">)</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Available]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Thursday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>4</span><span class="punct">,</span><span class="type">CONCAT</span><span class="punct">(</span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> <span class="string">'-'</span><span class="punct">,</span> Appointment<span class="punct">.</span>`Service Name`<span class="punct">)</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Available]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Friday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>5</span><span class="punct">,</span><span class="type">CONCAT</span><span class="punct">(</span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> <span class="string">'-'</span><span class="punct">,</span> Appointment<span class="punct">.</span>`Service Name`<span class="punct">)</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Available]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Saturday'</span>
<span class="keyword">FROM</span> Appointment
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> Person <span class="keyword">AS</span> Client <span class="keyword">ON</span> Client<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span>  Appointment<span class="punct">.</span>`Client ID`
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> Person <span class="keyword">AS</span> Employee <span class="keyword">ON</span> Employee<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span>  Appointment<span class="punct">.</span>`Employee ID`
<span class="keyword">WHERE</span> <span class="punct">`</span>Date<span class="punct">`</span> <span class="keyword">BETWEEN</span> <span class="type">DATE_ADD</span><span class="punct">(</span><span class="type">NOW</span><span class="punct">(</span><span class="punct">)</span><span class="punct">,</span><span class="type">INTERVAL</span> <span class="punct">-</span><span style='color:#008c00; '>1</span> <span class="keyword">DAY</span><span class="punct">)</span> <span class="punct">AND</span> <span class="type">DATE_ADD</span><span class="punct">(</span><span class="type">NOW</span><span class="punct">(</span><span class="punct">)</span><span class="punct">,</span><span class="type">INTERVAL</span> <span style='color:#008c00; '>6</span> <span class="keyword">DAY</span><span class="punct">)</span>
<span class="keyword">GROUP</span> <span class="keyword">BY</span> <span class="function">TIME</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span><span class="punct">;</span></pre>
    </p>
    <p><?php print_table($connection, 'Client Appointments By Day'); ?></p>
    <h3 id="view_employees_week">Employees Scheduled This Week</h3>
    <p>
<pre><span class="keyword">CREATE VIEW</span> `Employee Schedules <span class="keyword">By</span> <span class="keyword">Day</span>` <span class="keyword">AS</span>
<span class="keyword">SELECT</span> <span class="keyword">DISTINCT</span> <span class="type">DATE_FORMAT</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">,</span><span class="string"> </span><span class="string">"</span><span class="format">%h</span><span class="string"> </span><span class="format">%i</span><span class="string"> </span><span class="format">%p</span><span class="string">"</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Time'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>6</span><span class="punct">,</span>Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> <span class="keyword">ORDER</span> <span class="keyword">BY</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Unscheduled]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Sunday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>0</span><span class="punct">,</span>Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> <span class="keyword">ORDER</span> <span class="keyword">BY</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Unscheduled]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Monday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>1</span><span class="punct">,</span>Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> <span class="keyword">ORDER</span> <span class="keyword">BY</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Unscheduled]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Tuesday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>2</span><span class="punct">,</span>Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> <span class="keyword">ORDER</span> <span class="keyword">BY</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Unscheduled]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Wednesday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>3</span><span class="punct">,</span>Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> <span class="keyword">ORDER</span> <span class="keyword">BY</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Unscheduled]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Thursday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>4</span><span class="punct">,</span>Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> <span class="keyword">ORDER</span> <span class="keyword">BY</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Unscheduled]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Friday'</span><span class="punct">,</span>
   <span class="type">IFNULL</span><span class="punct">(</span><span class="function">GROUP_CONCAT</span><span class="punct">(</span><span class="keyword">IF</span><span class="punct">(</span><span class="function">WEEKDAY</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span> <span class="punct">=</span> <span style='color:#008c00; '>5</span><span class="punct">,</span>Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span><span class="punct">NULL</span><span class="punct">)</span> <span class="keyword">ORDER</span> <span class="keyword">BY</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> SEPARATOR <span class="string">', '</span><span class="punct">)</span><span class="punct">,</span><span class="string">'[Unscheduled]'</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Saturday'</span>
<span class="keyword">FROM</span> Appointment
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> Person <span class="keyword">AS</span> Employee <span class="keyword">ON</span> Employee<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span>  Appointment<span class="punct">.</span>`Employee ID`
<span class="keyword">WHERE</span> <span class="punct">`</span>Date<span class="punct">`</span> <span class="keyword">BETWEEN</span> <span class="type">DATE_ADD</span><span class="punct">(</span><span class="type">NOW</span><span class="punct">(</span><span class="punct">)</span><span class="punct">,</span><span class="type">INTERVAL</span> <span class="punct">-</span><span style='color:#008c00; '>1</span> <span class="keyword">DAY</span><span class="punct">)</span> <span class="punct">AND</span> <span class="type">DATE_ADD</span><span class="punct">(</span><span class="type">NOW</span><span class="punct">(</span><span class="punct">)</span><span class="punct">,</span><span class="type">INTERVAL</span> <span style='color:#008c00; '>6</span> <span class="keyword">DAY</span><span class="punct">)</span>
<span class="keyword">GROUP</span> <span class="keyword">BY</span> <span class="function">TIME</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">)</span><span class="punct">;</span></pre>
    </p>
    <p><?php print_table($connection, 'Employee Schedules By Day'); ?></p>
    <h3 id="view_reminders">Appointment Reminders</h3>
    <p>
<pre><span class="keyword">CREATE VIEW</span> `Appointment Reminders` <span class="keyword">AS</span>
<span class="keyword">SELECT</span> Client<span class="punct">.</span>`Phone Number`<span class="punct">,</span> <span class="type">CONCAT</span><span class="punct">(
    </span>Client<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> <span class="string">' has a '</span><span class="punct">,</span> `Service Name`<span class="punct">,</span> <span class="string">' appointment with '</span><span class="punct">,</span> Employee<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span> <span class="type">DATE_FORMAT</span><span class="punct">(</span><span class="punct">`</span>Date<span class="punct">`</span><span class="punct">,</span><span class="string"> </span><span class="string">"</span><span class="string"> on </span><span class="format">%b</span><span class="string"> </span><span class="format">%d</span><span class="string">, at </span><span class="format">%h</span><span class="string">:</span><span class="format">%i</span><span class="string"> </span><span class="format">%p</span><span class="string">.</span><span class="string">"</span><span class="punct">)</span><span class="punct">
)</span> <span class="keyword">AS</span> <span class="string">'Message'</span>
<span class="keyword">FROM</span> Appointment
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> Person <span class="keyword">AS</span> Client <span class="keyword">ON</span> Client<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span> Appointment<span class="punct">.</span>`Client ID`
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> Person <span class="keyword">AS</span> Employee <span class="keyword">ON</span> Employee<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span> Appointment<span class="punct">.</span>`Employee ID`
<span class="keyword">WHERE</span> <span class="punct">`</span>Date<span class="punct">`</span> <span class="keyword">BETWEEN</span> <span class="type">DATE_ADD</span><span class="punct">(</span><span class="type">NOW</span><span class="punct">(</span><span class="punct">)</span><span class="punct">,</span><span class="type">INTERVAL</span> <span style='color:#008c00; '>1</span> <span class="keyword">DAY</span><span class="punct">)</span> <span class="punct">AND</span> <span class="type">DATE_ADD</span><span class="punct">(</span><span class="type">NOW</span><span class="punct">(</span><span class="punct">)</span><span class="punct">,</span><span class="type">INTERVAL</span> <span style='color:#008c00; '>3</span> <span class="keyword">DAY</span><span class="punct">)</span>
<span class="keyword">ORDER</span> <span class="keyword">BY</span> <span class="punct">`</span>Date<span class="punct">`</span> <span class="keyword">ASC</span> <span class="keyword">LIMIT</span> <span style='color:#008c00; '>20</span><span class="punct">;</span></pre>
    </p>
    <p><?php print_table($connection, 'Appointment Reminders'); ?></p>
    <h3 id="view_billing">Billing</h3>
    <p>
<pre><span class="keyword">CREATE VIEW</span> <span class="punct">`</span>Billing<span class="punct">`</span> <span class="keyword">AS</span>
<span class="keyword">SELECT</span> <span class="keyword">DISTINCT</span> `Client ID`<span class="punct">,</span>Person<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span><span class="punct">,</span><span class="type">SUM</span><span class="punct">(</span><span class="punct">`</span>Price<span class="punct">`</span><span class="punct">)</span> <span class="keyword">AS</span> <span class="string">'Total Billed'</span>
<span class="keyword">FROM</span> <span class="punct">`</span>Appointment<span class="punct">`</span> <span class="keyword">INNER</span> <span class="keyword">JOIN</span> <span class="punct">`</span>Service<span class="punct">`</span> <span class="keyword">ON</span> Service<span class="punct">.</span><span class="punct">`</span>Name<span class="punct">`</span> <span class="punct">=</span> Appointment<span class="punct">.</span>`Service Name`
<span class="keyword">INNER</span> <span class="keyword">JOIN</span> <span class="punct">`</span>Person<span class="punct">`</span> <span class="keyword">ON</span> Person<span class="punct">.</span><span class="punct">`</span>ID<span class="punct">`</span> <span class="punct">=</span> Appointment<span class="punct">.</span>`Client ID`
<span class="keyword">GROUP</span> <span class="keyword">BY</span> `Client ID`<span class="punct">;</span></pre>
    </p>
    <p><?php print_table($connection, 'Billing'); ?></p>
</body>
</html>