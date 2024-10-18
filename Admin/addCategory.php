<?php
require('../includes/config.php');

if (isset($_POST['addCategory'])) {
    // print_r($_POST);

    $category_name = $_POST['category_name'];

    $sql = "INSERT INTO `categories`(`category_name`) VALUES ('$category_name')";

    $result = mysqli_query($conn, $sql);
    if ($result) {
        echo "success";
    }
}





$sql = "SELECT * FROM `categories`";
$result = $conn->query($sql);
// $result = mysqli_query($conn, $sql);
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
</head>

<body>
    <?php include './adminHeader.php' ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-4">
                <div class="card">
                    <div class="card-header">
                        <h3>Add Category</h3>
                    </div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="cat" class="form-label">Category Name</label>
                                <input type="text" name="category_name" class="form-control" id="cat" aria-describedby="emailHelp">
                            </div>
                            <input class="btn btn-primary" type="submit" name="addCategory" value="Add">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <table class="table table-responsive">
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                    </tr>
                    <?php
                    $counter = 0;
                    if (mysqli_num_rows($result) > 0):
                        // if ($result->num_rows > 0): 
                        while ($row = mysqli_fetch_assoc($result)):
                            // while ($row = $result->fetch_assoc()):
                            // echo "<tr>";
                            // echo "<td></td>";
                            // echo "</tr>";

                    ?>
                            <tr>
                                <td><?php ++$counter ?></td>
                                <td><?php echo $row['category_name']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No categories found.</p>
            <?php endif; ?>

            </div>
        </div>
    </div>




</body>

</html>