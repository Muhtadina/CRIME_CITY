<?php
// password Change
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Password Change | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    
    <!--Google Icons-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
</head>
<body>
<?php
include 'include/header.php';
?>
<br><br>
    <div class="profile-container">
        <div class="left-fixed">
            <h1 class="violet">PRO<span class="red">FILE</span></h1>
            <h2>CITIZEN FULL NAME</h2>
            
            <img src="placeholder.png" alt="Profile Placeholder" class="profile_display"> <!-- Replace with actual placeholder path -->

            <button class="edit-account-btn">EDIT ACCOUNT</button>
            <button class="delete-account-btn">DELETE ACCOUNT</button>
        </div>
        <div class="right-fixed">
            <form method="post" action=".php"> <!-- Adjust action as needed -->

                <div class="form-group">
                    <label></label>
                    <input type="password" name="citizen_password" class="profileformI" placeholder="Old Password">
                </div>

                <div class="form-group">
                    <label></label>
                    <input type="password" name="citizen_newpass" class="profileformI" placeholder="New Password">
                </div>
                <div class="form-group">
                    <label></label>
                    <input type="password" name="confirm_newpass" class="profileformI" placeholder="Confirm New Password">
                </div>
                <div class="btn_at_center">
                <button class="change-pass-btn">CHANGE PASSWORD</button>
                </div>
            </form>
        </div>
    </div>

<?php
include 'include/footer.php';
?>
</body>
</html>