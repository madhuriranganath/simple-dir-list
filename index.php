<?php
$db = mysqli_connect("localhost", "test", "", "task");
// If upload button is clicked ...
if (isset($_POST['upload'])) {
    $response = [];
    $filename = $_FILES["uploadfile"]["name"];
    $tempname = $_FILES["uploadfile"]["tmp_name"];
    $folder = "image/" . $filename;
    $file_extension = pathinfo($_FILES["uploadfile"]["name"], PATHINFO_EXTENSION);
    $allowed_image_extension = array("png", "jpg", "jpeg", "txt", "doc", "docx", "pdf", "gif",);
    // Validate file input to check if is not empty
    if (!file_exists($_FILES["uploadfile"]["tmp_name"])) {
        $response = array("type" => "danger", "message" => "Choose file to upload.");
    } // Validate file input to check if is with valid extension
    else if (!in_array($file_extension, $allowed_image_extension)) {
        $response = array("type" => "danger", "message" => "Upload valid file. Only txt,doc,docx,pdf,png,jpeg,jpg,gif are allowed.");
    } // Validate image file size
    else if (($_FILES["uploadfile"]["size"] > 2000000)) {
        $response = array("type" => "danger", "message" => "File size exceeds 2MB");
    } else {
        if (move_uploaded_file($tempname, $folder)) {
            // Get all the submitted data from the form
            $sql = "INSERT INTO image (filename) VALUES ('$filename')";
            // echo $sql;exit();
            // Execute query
            mysqli_query($db, $sql);
            $response = array("type" => "success", "message" => "Image uploaded successfully.");
        } else {
            $response = array("type" => "danger", "message" => "Problem in uploading image files.");
        }
    }
} else {
    $response = array();
}
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
        $date = date_create($row["uploaded_at"]);
        $array[$row["id"]]['id'] = $row["id"];
        $array[$row["id"]]['filename'] = $row["filename"];
        $array[$row["id"]]['uploaded_at'] = date_format($date, "Y/m/d H:i:s");
    }
}
// Deleting the uploaded file
if (isset($_POST['delete'])) {
    $filedetails = $_POST['deletefile'];
    $sql2 = "INSERT INTO deleted_file (filename) VALUES ('$filedetails')";
    // echo "<pre>";print_r($sql2);exit();
    mysqli_query($db, $sql2);
    $sql1 = "DELETE FROM image WHERE id=" . $_POST['id'];
    mysqli_query($db, $sql1);
    header("Refresh:0");
}
?> 

<!DOCTYPE html> 
<html> 
  
<head> 
    <title>List Files</title> 
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
</head> 
  
<body> 
<?php include 'navbar.php'; ?>
<div class="container mt-5">
    <div id="content"> 
        <?php if (!empty($response)) { ?>
            <div class="alert alert-<?php echo $response["type"]; ?> alert-dismissible fade show" role="alert" >
                <?php echo $response["message"]; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php
} ?>
        <form method="POST" action="" enctype="multipart/form-data"> 
        <div class="form-group">
                <input type="file" id="file" name="uploadfile" value="" accept=".txt,.doc,.docx,.pdf,.png,.jpeg,.jpg,.gif" class="form-control-file" /> 
            </div>
            <div class="form-group"> 
                <button type="submit" name="upload" class="btn btn-primary"> 
                  UPLOAD 
                </button> 
            </div> 
        </form>
        
    </div> 

    <div class="row">
    <div class="col-md-3" >
    <div class="input-group mb-3" style="float:right;">
  <input type="text" class="form-control" placeholder="Search file" aria-label="Recipient's username" aria-describedby="button-addon2">
  <div class="input-group-append">
    <button class="btn btn-outline-secondary" type="button" id="button-addon2">Search</button>
  </div>
  </div>
</div></div>

    <table class="table">
      <tr>
        <th>Filename</th>
        <th>Uploaded At</th>
        <th>Action</th>
      </tr>
      <?php
if (!empty($array)) {
    foreach ($array as $key => $value) { ?>

            <tr>
                <td><?php echo $value['filename']; ?></td>
                <td><?php echo $value['uploaded_at']; ?></td>
            <td>
            

            <form method="POST" action=""> 
                <input type="hidden" name="deletefile" value="<?php echo $value['filename']; ?>" /> 
                <input type="hidden" name="id" value="<?php echo $value['id']; ?>" /> 
    
                <div> 
                    <button type="submit" class="btn btn-danger" name="delete">Delete</button>
                </div> 
            </form> 

            </td>
        </tr>
      <?php
    }
} else { ?>
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
        echo '<li class="page-item active"><a class="page-link" href="index.php?page=' . $pageNumber . '">' . $pageNumber . '</a></li>';
    } else {
        echo '<li class="page-item"><a class="page-link" href="index.php?page=' . $pageNumber . '">' . $pageNumber . '</a></li>';
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
