<?php
include "./assets/database.php";
$researchs;
$searches;
$search__error;
$no_search__error;

// print_r($_SERVER);

$sql = "SELECT * FROM research_info ORDER BY research_id DESC";
$res = $conn->query($sql);

if ($res->num_rows > 0) {
    $researchs = $res->fetch_all();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_REQUEST["search"])) {
        $search = htmlspecialchars(trim($_REQUEST["search"]));

        if (empty($search)) {
            $search__error = "Search is empty";
        } else {
            $sql = "SELECT * FROM research_info WHERE research_author LIKE '%$search%' OR research_title LIKE '%$search%'";
            $res = $conn->query($sql);

            if ($res->num_rows > 0) {
                $searches = $res->fetch_all();
            } else {
                $no_search__error = "No such file";
            }
        }
    }
    $conn->close();
}

// preview pdf
if (!empty($_REQUEST["preview"]) && $_REQUEST["preview"] == 1) {
    $pdf = $_REQUEST["pdf"];
    header("Catch-Control: public");
    header("Content-Type-Encoding: binary");
    header("content-Type: application/pdf");
    if (file_exists("uploads/" . $pdf)) {
        if (!is_readable("uploads/" . $pdf)) {
            header("Content-Disposition: attachment; filename=$pdf");
            readfile("uploads/" . $pdf);
        } else {
            readfile("uploads/" . $pdf);
        }
    }
}
?>

<?php include "includes/header.php" ?>
<div class="container text-center mb-5">
    <h5 class="text-uppercase">Reedemers college of science and management</h5>
    <h5 class="text-uppercase">Conference journals</h5>
</div>
<form method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" class="container pb-2">
    <div class="hstack">
        <input class="form-control fst-italic rounded-0" style="min-width: 200px; max-width: 250px;" type="text" name="search" id="search" placeholder="Search by author / title">
        <button id="search-author" class="btn btn-primary rounded-0" style="min-height:38px"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
            </svg></button>
    </div>
    <p class="fst-italic fw-light text-danger" style="font-size: 0.9rem;">
        <?php
        if (!empty($search__error)) {
            echo $search__error;
        }
        if (!empty($no_search__error)) {
            echo $no_search__error;
        }
        ?>
    </p>
</form>
<div class="container">
    <?php
    if (!empty($searches)) {
        echo "<a href='index.php' class='btn btn-outline-secondary' style='width: 100px;'>All</a>
        ";
    }
    ?>
    <div class="row">
        <?php
        if (!empty($searches)) {
            foreach ($searches as $research) {
        ?>
                <div class="col-sm-12 col-md-6 col-lg-4 py-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="hstack gap-2">
                                <img src="./images/pdf-1.png" width="60px" alt="pdf logo">
                                <div class="d-flex flex-column" style="padding-left: 5px">
                                    <p class="m-0 fst-italic text-danger" style="font-size: 0.6rem;">Research Title</p>
                                    <p class="m-0" style="font-size: 0.8rem;"><?php strlen($research[1]) < 65 ? print $research[1] : print ucfirst(substr($research[1], -65)) . "..."; ?></p>
                                </div>
                            </div>
                            <hr class="mb-2">
                            <div class="hstack gap-2" style="padding-left: 10px;">
                                <img src="./images/user.png" alt="">
                                <p class="m-0 text-capitalize" style="font-size: 0.8rem; padding-left: 5px"><?php strlen($research[2]) < 30 ? print $research[2] : print ucfirst(substr($research[2], -30)) . "..."; ?></p>
                            </div>
                            <p style="text-align: right; font-size: 0.7rem;"><span class="fst-italic text-danger text-capitalize">Uploaded on: </span> <?php echo $research[4]; ?></p>
                            <a href="index.php?preview=1&pdf=<?php echo $research[3]; ?>" class="btn btn-sm btn-primary w-100">Preview</a>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            if (!empty($researchs)) {
                foreach ($researchs as $research) {
                ?>
                    <div class="col-sm-12 col-md-6 col-lg-4 py-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="hstack gap-2">
                                    <img src="./images/pdf-1.png" width="60px" alt="pdf logo">
                                    <div class="d-flex flex-column" style="padding-left: 5px">
                                        <p class="m-0 fst-italic text-danger" style="font-size: 0.6rem;">Research Title</p>
                                        <p class="m-0" style="font-size: 0.8rem; min-height: 70px"><?php strlen($research[1]) < 65 ? print $research[1] : print ucfirst(substr($research[1], -65)) . "..."; ?></p>
                                    </div>
                                </div>
                                <hr class="mb-2">
                                <div class="hstack gap-2" style="padding-left: 10px;">
                                    <img src="./images/user.png" alt="">
                                    <p class="m-0 text-capitalize" style="font-size: 0.8rem; padding-left: 5px"><?php strlen($research[2]) < 30 ? print $research[2] : print ucfirst(substr($research[2], -30)) . "..."; ?></p>
                                </div>
                                <p style="text-align: right; font-size: 0.7rem;"><span class="fst-italic text-danger text-capitalize">Uploaded on: </span> <?php echo $research[4]; ?></p>
                                <a href="index.php?preview=1&pdf=<?php echo $research[3]; ?>" class="btn btn-sm btn-primary w-100">Preview</a>
                            </div>
                        </div>
                    </div>
        <?php
                }
            }
        }
        ?>
    </div>
</div>


<?php include "includes/footer.php" ?>