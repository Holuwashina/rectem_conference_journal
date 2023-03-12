<?php
session_start();
if (empty($_SESSION["email"])) {
    header("Location: login.php");
}

if (isset($_REQUEST["logout"])) {
    if ($_REQUEST["logout"] == 1) {
        session_destroy();
        header("Location: index.php");
    }
}

include "./assets/database.php";

$error = [];
$success = [];
$toggle_update_delete_button = 0;
$searched_author;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // inputs checking
    $title = htmlspecialchars(trim($_POST["title"]));
    $author = htmlspecialchars(trim($_POST["author"]));
    $pdf = "";

    if (empty($title)) {
        array_push($error, "Research title can not be empty ❌");
    }

    if (empty($author)) {
        array_push($error, "Research author can not be empty ❌");
    }


    if (!empty($_FILES)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "pdf") {
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            array_push($error, "PDF upload error - exist / not pdf ❌.");
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $pdf = htmlspecialchars(basename($_FILES["fileToUpload"]["name"]));
            } else {
                array_push($error, "Error uploading to server ❌.");
            }
        }

        if (count($error) == 0 && !empty($pdf) && $uploadOk == 1) {
            $sql = "INSERT INTO research_info (research_title, research_author, research_pdf) VALUES ('$title', '$author', '$pdf')";

            if (mysqli_query($conn, $sql)) {
                array_push($success, "Uploaded successfully ✅.");
            } else {
                array_push($error, "Database error ❌.");
                if (file_exists("./uploads/" . $pdf)) {
                    unlink("./uploads/" . $pdf);
                }
            }
            $conn->close();
        }
    }
}

// search author
if (!empty($_REQUEST["search-author"]) && $_REQUEST["search"] == 1) {
    if (empty($_REQUEST["search-author"])) {
        array_push($error, "Search empty 🚫.");
    } else {
        $search_author = $_REQUEST["search-author"];
        $sql = "SELECT * FROM research_info WHERE research_author='$search_author'";
        $res = $conn->query($sql);

        if ($res->num_rows > 0) {
            $toggle_update_delete_button = 1;
            $searched_author = $res->fetch_assoc();
        } else {
            array_push($error, "No data ❌");
        }
        $conn->close();
    }
}


// update data
if (isset($_REQUEST["update"]) && $_REQUEST["update"] == 1) {
    $id = $_REQUEST["research_id"];
    $title = $_REQUEST["research_title"];
    $author = $_REQUEST["research_author"];

    if (empty($title)) {
        array_push($error, "Research title can not be empty ❌");
    }

    if (empty($author)) {
        array_push($error, "Research author can not be empty ❌");
    }

    if (!empty($id) && !empty($title) && !empty($author)) {
        $sql = "UPDATE research_info SET research_author='$author', research_title='$title' WHERE research_id='$id'";

        if ($conn->query($sql) === TRUE) {
            array_push($success, "Updated successfully ✅");
        } else {
            array_push($error, "Update error ❌");
        }
        $conn->close();
    }
}

// delete data
if (isset($_REQUEST["delete"]) && $_REQUEST["delete"] == 1) {
    $id = $_REQUEST["research_id"];

    if (!empty($id)) {
        $sql = "DELETE FROM research_info WHERE research_id='$id'";

        if ($conn->query($sql) === TRUE) {
            $pdf = $_REQUEST["pdf"];
            if (file_exists("./uploads/" . $pdf)) {
                if (unlink("./uploads/" . $pdf)) {
                    array_push($success, "PDF deleted from the server ✅.");
                } else {
                    array_push($error, "PDF deleting error 🚫.");
                }
            } else {
                array_push($error, "PDF not on the server, update by selecting another PDF 🚫.");
            }
            array_push($success, "Data deleted successfully ✅");
        } else {
            array_push($error, "Delete error ❌");
        }
        $conn->close();
    }
}

?>

<?php include "./includes/header.php"
?>
<div class="container text-center mb-5">
    <h5 class="text-uppercase">Manage research</h5>
</div>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST" enctype="multipart/form-data" class="container col-sm-10 col-md-5 col-lg-4">
    <div class="mb-3">
        <label for="title" class="form-label">Research Title</label>
        <textarea class="form-control fst-italic" type="text" name="title" id="title" placeholder="Research title" research-id="<?php !empty($searched_author) ? print $searched_author["research_id"] : null; ?>"><?php !empty($searched_author) ? print $searched_author["research_title"] : null; ?></textarea>
    </div>
    <div class="mb-3">
        <label for="author" class="form-label">Research Author</label>
        <div class="d-flex flex-row">
            <input class="form-control fst-italic rounded-0" type="text" name="author" id="author" placeholder="Research author" value="<?php !empty($searched_author) ? print $searched_author["research_author"] : null; ?>">
            <a href="research.php?search=1&search-author=" onclick="this.href = this.href + document.getElementById('author').value;" id="search-author" name="search-author" class="btn btn-secondary rounded-0"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                </svg></a>
        </div>
    </div>
    <?php
    if ($toggle_update_delete_button) {
    ?>
        <div class="mb-3">
            <label for="author" class="form-label">PDF name</label>
            <div class="d-flex flex-row">
                <input class="form-control fst-italic" type="text" name="author" id="author" placeholder="Research author(s)" value="<?php !empty($searched_author) ? print $searched_author["research_pdf"] : null; ?>" disabled>
            </div>
            <p class="fst-italic fw-light text-danger" style="font-size: 0.8rem;">Please delete all the data, if you need to update the PDF and then re-upload the data.</p>
        </div>
    <?php
    }
    ?>
    <div class="mb-3">
        <input class="form-control fst-italic" type="file" name="fileToUpload" id="fileToUpload">
        <p class="fst-italic fw-light text-danger" style="font-size: 0.8rem;">pdf format only</p>
    </div>
    <?php
    if ($toggle_update_delete_button) {
    ?>
        <div class="hstack gap-2">
            <a class="btn btn-success w-100 mb-2" style="margin-right: 5px;" id="update">UPDATE</a>
            <a class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">DELETE</a>
        </div>
    <?php
    } else {
    ?>
        <button class="btn btn-primary w-100 mb-2" name="upload">UPLOAD</button>
    <?php
    }
    ?>
    <div class="alert-container">
        <?php
        foreach ($error as $err) {
            echo "
            <div class='alert alert-danger'>$err</div>
            ";
        }

        foreach ($success as $msg) {
            echo "
            <div class='alert alert-success'>$msg</div>
            ";
        }

        ?>
    </div>
</form>

<!-- Modal -->
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="fst-italic">Are you really sure you want to delete the data?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CLOSE</button>
                <a href="research.php?delete=1&research_id=<?php !empty($searched_author) ? print $searched_author["research_id"] : null; ?>&pdf=<?php !empty($searched_author) ? print $searched_author["research_pdf"] : null; ?>" type="button" class="btn btn-danger">DELETE</a>
            </div>
        </div>
    </div>
</div>
<?php include "./includes/footer.php" ?>