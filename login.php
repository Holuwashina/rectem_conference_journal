<?php
session_start();

if (!empty($_SESSION["email"])) {
    header("Location: research.php");
}

include "./assets/database.php";

$error = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars(trim($_POST["email"]));
    $password = htmlspecialchars(trim($_POST["password"]));

    if (empty($email)) {
        array_push($error, "Email can not be empty");
    }

    if (empty($password)) {
        array_push($error, "Password can not be empty");
    }


    if (count($error) === 0) {
        $sql = "SELECT * FROM research_uploader WHERE uploader_email='$email' AND uploader_password='$password'";
        $res = $conn->query($sql);

        if ($res->num_rows > 0) {
            $user = $res->fetch_assoc();
            $_SESSION["email"] = $user["uploader_email"];
            header("Location: research.php");
        } else {
            array_push($error, "Unauthorize access");
        }
        $conn->close();
    }
}
?>

<?php include "./includes/header.php" ?>
<div class="container text-center mb-5">
    <h5 class="text-uppercase" style="font-weight: 600;">Admin Login</h5>
</div>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="POST" class="container col-sm-10 col-md-5 col-lg-4" id="login-form">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input class="form-control" type="email" name="email" id="email" placeholder="admin@rectem.com">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input class="form-control" type="password" name="password" id="password">
    </div>
    <button type="submit" class="btn btn-primary w-100">LOGIN</button>
    <p class="fst-italic fw-light text-danger text-center mt-3" style="font-size: 0.8rem;">Only admin can upload research pappers</p>
    <div class="alert-container">
        <?php
        foreach ($error as $err) {
            echo "
            <div class='alert alert-danger'>$err</div>
            ";
        }
        ?>
    </div>
</form>
<?php include "./includes/footer.php" ?>