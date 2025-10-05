<?php
// report_witness.php
session_start();
include 'backend/db.php';

$message = '';
if (!isset($_SESSION['report_data'])) {
    header("Location: report.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['report_data']['witnesses'] = [];
    for ($i = 0; $i < $_SESSION['report_data']['witness_count']; $i++) {
        $_SESSION['report_data']['witnesses'][] = [
            'nid' => filter_input(INPUT_POST, "witness_nid_$i", FILTER_VALIDATE_INT),
            'first_name' => filter_input(INPUT_POST, "witness_first_name_$i", FILTER_SANITIZE_STRING),
            'last_name' => filter_input(INPUT_POST, "witness_last_name_$i", FILTER_SANITIZE_STRING),
            'gender' => filter_input(INPUT_POST, "witness_gender_$i", FILTER_SANITIZE_STRING),
            'age' => filter_input(INPUT_POST, "witness_age_$i", FILTER_VALIDATE_INT),
            'attire_description' => filter_input(INPUT_POST, "witness_attire_$i", FILTER_SANITIZE_STRING)
        ];
    }
    header("Location: report_final.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Witness | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    
    <!--Google Icons-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
</head>
<body>
<?php include 'include/header.php'; ?>

    <div class="profile-container">
        <div class="left-fixed">
            <h1><b class="violet">REPORT</b><b class="red">WITNESS</b></h1>
            <button class="back-btn" onclick="window.location.href='report_suspect.php'">PREVIOUS PAGE</button>
        </div>
        <div class="right-scroll">
            <form method="post" action="report_witness.php">
                <?php for ($i = 0; $i < $_SESSION['report_data']['witness_count']; $i++): ?>
                    <h3>Witness <?php echo $i + 1; ?></h3>
                    <div class="form-group">
                        <label>Witness NID No. :</label>
                        <input type="text" name="witness_nid_<?php echo $i; ?>" class="profileformI">
                    </div>
                    <div class="form-group">
                        <label>Witness Name :</label>
                        <input type="text" name="witness_first_name_<?php echo $i; ?>" placeholder="First Name" class="profileform">
                        <input type="text" name="witness_last_name_<?php echo $i; ?>" placeholder="Last Name" class="profileform">
                    </div>
                    <div class="form-group">
                        <label>Witness's Gender :</label>
                        <select name="witness_gender_<?php echo $i; ?>" class="profileformI">
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Witness's Age :</label>
                        <input type="number" name="witness_age_<?php echo $i; ?>" class="profileformI">
                    </div>
                    <div class="form-group">
                        <label>Attire Description<b class="red">*</b> :</label>
                        <textarea name="witness_attire_<?php echo $i; ?>" rows="5" class="profileformI" required></textarea>
                    </div>
                <?php endfor; ?>
                <div class="btn_at_center">
                    <button type="submit" class="next-btn">NEXT PAGE</button>
                </div>
            </form>
        </div>
    </div>

<?php include 'include/footer.php'; ?>
</body>
</html>