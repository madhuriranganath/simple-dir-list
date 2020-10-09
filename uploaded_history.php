<?php
//require_once "config.php";
$db = mysqli_connect("localhost", "test", "", "task");
//define total number of results you want per page
$results_per_page = 10;
//find the total number of results stored in the database
$query = "SELECT * FROM image";
$result = mysqli_query($db, $query);
$number_of_result = mysqli_num_rows($result);
//determine the total number of pages available
$number_of_page = ceil($number_of_result / $results_per_page);
//determine which page number visitor is currently on
if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = $_GET['page'];
}
//determine the sql LIMIT starting number for the results on the displaying page
$page_first_result = ($page - 1) * $results_per_page;
//retrieve the selected results from database
$query = "SELECT * FROM image LIMIT " . $page_first_result . ',' . $results_per_page;
$result = mysqli_query($db, $query);
$array = [];
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $array[$row["id"]]['id'] = $row["id"];
        $array[$row["id"]]['filename'] = $row["filename"];
        $array[$row["id"]]['uploaded_at'] = $row["uploaded_at"];
    }
}
?> 
<html>
<head>
  <title>Uploaded History</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head>
<body>
<?php include 'navbar.php';?>
<div class="container mt-5">
	  	<h3>Uploaded File</h3>
		<br/>
		<table class="table" >
		<tr>
			<th>Filename</th>
			<th>Uploaded At</th>

		</tr>
		<?php if (!empty($array)) {
			foreach ($array as $key => $value) { ?>
					<tr>
						<td><?php echo $value['filename']; ?></td>
						<td><?php echo $value['uploaded_at']; ?></td>
					</tr>
					<?php
			} }else { ?>
                <tr>
                <td colspan="2" class="text-center">No Records</td> </tr>
             <?php
} ?>
		</table>
		<nav>
  <ul class="pagination">
    <?php
//display the link of the pages in URL
for ($pageNumber = 1;$pageNumber <= $number_of_page;$pageNumber++) {
    if ($page == $pageNumber) {
        echo '<li class="page-item active"><a class="page-link" href="uploaded_history.php?page=' . $pageNumber . '">' . $pageNumber . '</a></li>';
    } else {
        echo '<li class="page-item"><a class="page-link" href="uploaded_history.php?page=' . $pageNumber . '">' . $pageNumber . '</a></li>';
    }
} ?>

  </ul>
</nav>
		</div>

	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>

