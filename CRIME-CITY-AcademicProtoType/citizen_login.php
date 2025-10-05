<?php
// citizen_login.php
session_start();
include 'backend/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_address = mysqli_real_escape_string($conn, $_POST['email_address']);
    $password = $_POST['password'];

    if (empty($email_address) || empty($password)) {
        $message = "Error: Email and password are required.";
    } elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $message = "Error: Invalid email format.";
    } else {
        $sql = "SELECT cr.User_ID, cr.Citizen_Password, cu.Login_ID 
                FROM Citizen_Register cr 
                LEFT JOIN Citizen_User cu ON cr.User_ID = cu.User_ID 
                WHERE cr.Email_Address = '$email_address'";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['Citizen_Password'])) {
                $_SESSION['user_id'] = $user['User_ID'];
                $_SESSION['login_id'] = $user['Login_ID'];
                $_SESSION['last_login'] = date('Y-m-d H:i:s');
                header("Location: profile.php");
                exit();
            } else {
                $message = "Error: Incorrect password.";
            }
        } else {
            $message = "Error: Email not found.";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CRIME CITY</title>
    <link rel="shortcut icon" href="img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <h1>CRIME CITY</h1>
    <div class="middlebox">
        <h2>CITIZEN ACCOUNT | LOGIN</h2>
        <?php if (!empty($message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form class="fillup" action="citizen_login.php" method="post">
            <input type="email" placeholder="Email Address" class="form_input" name="email_address" required>
            <input type="password" placeholder="Password" class="form_input" name="password" required>
            <button type="submit" class="btn-login">LOGIN</button>
        </form>
        <a href="register.php" class="btn-prime">Do not have an Account? REGISTER</a>
    </div>
    <?php include 'include/fixedfoot.php'; ?>
</body>
</html>