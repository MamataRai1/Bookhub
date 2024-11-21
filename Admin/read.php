<?php
$conn = new mysqli('localhost', 'root', '', 'water_management');
$result = $conn->query("SELECT * FROM water_records");
while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['customer_name']}</td>
        <td>{$row['book_name']}</td>
        <td>{$row['price']}</td>
        <td>{$row['delivery_place']}</td>
        <td>{$row['delivery_date']}</td>
        <td>{$row['status']}</td>
        <td>
            <a href='edit.php?id={$row['id']}' class='btn btn-warning'>Edit</a>
            <a href='delete.php?id={$row['id']}' class='btn btn-danger'>Delete</a>
        </td>
    </tr>";
}
$conn->close();
?>
