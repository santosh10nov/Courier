<!DOCTYPE html>
<html>
<head>
<style>
table {
width: 100%;
    border-collapse: collapse;
}

table, td, th {
border: 1px solid black;
padding: 5px;
}

th {text-align: left;}
</style>
</head>
<body>

<?php
    $q = intval($_GET['q']);
    
    echo "<table>
    <tr>
    <th>Firstname</th>
    <th>Lastname</th>
    <th>Age</th>
    <th>Hometown</th>
    <th>Job</th>
    </tr>";
    while($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . "HI" . "</td>";
        echo "<td>" . "Buddy" . "</td>";
        echo "<td>" . "K". "</td>";
        echo "<td>" . "Dil". "</td>";
        echo "<td>" ."Indian". "</td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>
</body>
</html>