<?php
/**
 * Created by PhpStorm.
 * User: brad
 * Date: 1/28/18
 * Time: 6:10 PM
 */


$config = parse_ini_file('C:\\inetpub\\sradzone.ini');
$mysqli = mysqli_connect("localhost",$config['username'],$config['password'],$config['db']);
if(!$mysqli){
    die("Failed to connect to Database");
}
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}


$sql = "SELECT FirstName, LastName, Attending, PlusOne, Comment, Replied FROM `srad`.`gu1_guest`";
$result = $mysqli->query($sql);
$rows = array();
$totalAttending = 0;
while($row = $result->fetch_assoc()) {
    $rows[] = $row;
    if($row['Attending'] == "YES") {
        $totalAttending++;
        if($row['PlusOne'] == "YES") {
            $totalAttending++;
        }
    }
}
?>

<html>
    <head>
        <style>
            body {
                font: normal medium/1.4 sans-serif;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                padding: 0.25rem;
                text-align: left;
                border: 1px solid #ccc;
            }
            tbody tr:hover {
                background: yellow;
            }
        </style>
    </head>
    <body>
    <section>
        <header>
            <h3>
<?php

echo $totalAttending . " total guests"

?>
            </h3>
        </header>
        <article>
            Search: <input type="search" class="light-table-filter" data-table="order-table" placeholder="Filter">
            <table class="order-table table zebra">
                <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last name</th>
                    <th>Attending</th>
                    <th>Plus One</th>
                    <th>Comment</th>
                    <th>Reply Date</th>
                </tr>
                </thead>
                <tbody>
<?php

foreach($rows as $row) {
    echo "<tr>";
        echo "<td>".ucfirst(strtolower($row['FirstName']))."</td>";
        echo "<td>".ucfirst(strtolower($row['LastName']))."</td>";
        echo "<td>".ucfirst(strtolower($row['Attending']))."</td>";
        echo "<td>".ucfirst(strtolower($row['PlusOne']))."</td>";
        echo "<td>".$row['Comment']."</td>";
        echo "<td>".$row['Replied']."</td>";
    echo "</tr>";
}
?>
                </tbody>
            </table>
        </article>
    </section>
    </body>
<script>

    (function(document) {
        'use strict';

        var LightTableFilter = (function(Arr) {

            var _input;

            function _onInputEvent(e) {
                _input = e.target;
                var tables = document.getElementsByClassName(_input.getAttribute('data-table'));
                Arr.forEach.call(tables, function(table) {
                    Arr.forEach.call(table.tBodies, function(tbody) {
                        Arr.forEach.call(tbody.rows, _filter);
                    });
                });
            }

            function _filter(row) {
                var text = row.textContent.toLowerCase(), val = _input.value.toLowerCase();
                row.style.display = text.indexOf(val) === -1 ? 'none' : 'table-row';
            }

            return {
                init: function() {
                    var inputs = document.getElementsByClassName('light-table-filter');
                    Arr.forEach.call(inputs, function(input) {
                        input.oninput = _onInputEvent;
                    });
                }
            };
        })(Array.prototype);

        document.addEventListener('readystatechange', function() {
            if (document.readyState === 'complete') {
                LightTableFilter.init();
            }
        });

    })(document);

</script>
</html>