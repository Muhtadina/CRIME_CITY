<?php
// report.php
session_start();
include 'backend/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['report_data'] = [
        'crime_type' => filter_input(INPUT_POST, 'crime_type', FILTER_SANITIZE_STRING),
        'crime_date' => filter_input(INPUT_POST, 'crime_date', FILTER_SANITIZE_STRING),
        'crime_time' => filter_input(INPUT_POST, 'crime_time', FILTER_SANITIZE_STRING),
        'division' => filter_input(INPUT_POST, 'division', FILTER_SANITIZE_STRING),
        'district' => filter_input(INPUT_POST, 'district', FILTER_SANITIZE_STRING),
        'postal_code' => filter_input(INPUT_POST, 'postal_code', FILTER_SANITIZE_STRING),
        'victim_count' => filter_input(INPUT_POST, 'victim_count', FILTER_VALIDATE_INT),
        'suspect_count' => filter_input(INPUT_POST, 'suspect_count', FILTER_VALIDATE_INT),
        'witness_count' => filter_input(INPUT_POST, 'witness_count', FILTER_VALIDATE_INT),
        'crime_description' => filter_input(INPUT_POST, 'crime_description', FILTER_SANITIZE_STRING),
        'visibility' => filter_input(INPUT_POST, 'visibility', FILTER_SANITIZE_STRING),
        'evidence_image' => $_FILES['evidence_image']['tmp_name'] ? file_get_contents($_FILES['evidence_image']['tmp_name']) : null,
        'evidence_video' => $_FILES['evidence_video']['tmp_name'] ? file_get_contents($_FILES['evidence_video']['tmp_name']) : null,
        'evidence_audio' => $_FILES['evidence_audio']['tmp_name'] ? file_get_contents($_FILES['evidence_audio']['tmp_name']) : null,
        'evidence_url' => filter_input(INPUT_POST, 'evidence_url', FILTER_SANITIZE_URL)
    ];
    header("Location: report_victim.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Crime | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="profile-container">
    <div class="left-fixed">
        <h1><b class="violet">REPORT </b><b class="red">CRIME</b></h1>
        <p>As a Citizen in CRIME CITY, you have a choice to Report Crime Publicly where the public will be able to see your identity and Case Report from the Case Files after the Investigation is complete, or you may Anonymously Report Crime where your identity won’t be revealed to the public but the CYBERPOL BD.</p>
        <div class="toggle-container">
            <span class="toggle-label left">PUBLIC</span>
            <div class="toggle-switch" onclick="this.classList.toggle('active')">
                <div class="toggle-button"></div>
            </div>
            <span class="toggle-label right">ANONYMOUS</span>
        </div>
    </div>
    <div class="right-scroll">
        <?php if ($message): ?>
            <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
        <?php else: ?>
            <form method="post" action="report.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Crime Type<b class="red">*</b> :</label>
                    <select name="crime_type" class="profileformI" required>
                        <option value="">Select Category</option>
                        <option value="murder">Murder</option>
                        <option value="theft">Theft</option>
                        <option value="sexual_assault">Sexual Assault</option>
                        <option value="sexual_harassment">Sexual Harassment</option>
                        <option value="extortion">Extortion</option>
                        <option value="bribe_corruption">Bribe-Corruption</option>
                        <option value="robbery">Robbery</option>
                        <option value="child_marriage">Child Marriage</option>
                        <option value="dowry">Dowry</option>
                        <option value="blackmail">Blackmail</option>
                        <option value="cyber_bullying">Cyber Bullying</option>
                        <option value="online_fishing">Online Phishing</option>
                        <option value="hacking">Hacking</option>
                        <option value="piracy">Piracy</option>
                        <option value="money_laundering">Money Laundering</option>
                        <option value="overcharging">Overcharging</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Crime Date<b class="red">*</b> :</label>
                    <input type="date" name="crime_date" class="profileformI" required>
                </div>
                <div class="form-group">
                    <label>Crime Time<b class="red">*</b> :</label>
                    <input type="time" name="crime_time" class="profileformI" required>
                </div>
                <div class="form-group">
                    <label>Division<b class="red">*</b> :</label>
                    <select name="division" class="profileformI" required>
                        <option value="">Select Division</option>
                        <option value="Dhaka">Dhaka</option>
                        <option value="Chittagong">Chittagong</option>
                        <option value="Rajshahi">Rajshahi</option>
                        <option value="Khulna">Khulna</option>
                        <option value="Barisal">Barisal</option>
                        <option value="Sylhet">Sylhet</option>
                        <option value="Rangpur">Rangpur</option>
                        <option value="Mymensingh">Mymensingh</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>District<b class="red">*</b> :</label>
                    <select name="district" class="profileformI" required>
                        <option value="">Select District</option>
                        <option value="Dhaka">Dhaka</option>
                        <option value="Gazipur">Gazipur</option>
                        <option value="Chittagong">Chittagong</option>
                        <option value="Coxs_Bazar">Cox's Bazar</option>
                        <option value="Rajshahi">Rajshahi</option>
                        <option value="Natore">Natore</option>
                        <option value="Khulna">Khulna</option>
                        <option value="Jessore">Jessore</option>
                        <option value="Barisal">Barisal</option>
                        <option value="Patuakhali">Patuakhali</option>
                        <option value="Sylhet">Sylhet</option>
                        <option value="Sunamganj">Sunamganj</option>
                        <option value="Rangpur">Rangpur</option>
                        <option value="Dinajpur">Dinajpur</option>
                        <option value="Mymensingh">Mymensingh</option>
                        <option value="Netrokona">Netrokona</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Postal Code :</label>
                    <input type="text" name="postal_code" class="profileformI">
                </div>
                <div class="form-group">
                    <label>Numbers of victim<b class="red">*</b> :</label>
                    <input type="number" name="victim_count" class="profileformI" min="1" required>
                </div>
                <div class="form-group">
                    <label>Numbers of Suspect<b class="red">*</b> :</label>
                    <input type="number" name="suspect_count" class="profileformI" min="1" required>
                </div>
                <div class="form-group">
                    <label>Numbers of Witness :</label>
                    <input type="number" name="witness_count" class="profileformI" min="0" value="0">
                </div>
                <div class="form-group">
                    <label>Crime Description<b class="red">*</b> :</label>
                    <textarea name="crime_description" rows="5" class="profileformI" required></textarea>
                </div>
                <div class="form-group">
                    <label>Evidence IMAGE :</label>
                    <input type="file" accept=".png,.jpg,.jpeg" name="evidence_image" class="profileformI">
                </div>
                <div class="form-group">
                    <label>Evidence VIDEO :</label>
                    <input type="file" accept=".mp4,.avi,.mkv" name="evidence_video" class="profileformI">
                </div>
                <div class="form-group">
                    <label>Evidence AUDIO :</label>
                    <input type="file" accept=".mp3,.wav,.flac" name="evidence_audio" class="profileformI">
                </div>
                <div class="form-group">
                    <label>Evidence URL :</label>
                    <input type="url" name="evidence_url" class="profileformI" placeholder="https://example.com">
                </div>
                <div class="form-group">
                    <label>Visibility :</label>
                    <select name="visibility" class="profileformI" required>
                        <option value="Public">Public</option>
                        <option value="Anonymous">Anonymous</option>
                    </select>
                </div>
                <div class="btn_at_center">
                    <button type="submit" class="next-btn">NEXT PAGE</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include 'include/footer.php'; ?>
</body>
</html>