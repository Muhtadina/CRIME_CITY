<?php
// report_victim.php
session_start();
include 'backend/db.php';

$message = '';
if (!isset($_SESSION['report_data'])) {
    header("Location: report.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $_SESSION['report_data']['victims'] = [];
    for ($i = 0; $i < $_SESSION['report_data']['victim_count']; $i++) {
        $_SESSION['report_data']['victims'][] = [
            'nid' => filter_input(INPUT_POST, "victim_nid_$i", FILTER_VALIDATE_INT),
            'first_name' => filter_input(INPUT_POST, "victim_first_name_$i", FILTER_SANITIZE_STRING),
            'last_name' => filter_input(INPUT_POST, "victim_last_name_$i", FILTER_SANITIZE_STRING),
            'gender' => filter_input(INPUT_POST, "victim_gender_$i", FILTER_SANITIZE_STRING),
            'age' => filter_input(INPUT_POST, "victim_age_$i", FILTER_VALIDATE_INT),
            'attire_description' => filter_input(INPUT_POST, "victim_attire_$i", FILTER_SANITIZE_STRING),
            'acquaintance' => filter_input(INPUT_POST, "victim_acquaintance_$i", FILTER_SANITIZE_STRING)
        ];
    }
    header("Location: report_suspect.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Victim | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <style>
        .toggle-container { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .toggle-switch { width: 50px; height: 24px; background-color: #ccc; border-radius: 12px; position: relative; cursor: pointer; }
        .toggle-switch.active { background-color: #4CAF50; }
        .toggle-button { width: 20px; height: 20px; background-color: white; border-radius: 50%; position: absolute; top: 2px; left: 2px; transition: transform 0.3s; }
        .toggle-switch.active .toggle-button { transform: translateX(26px); }
        .toggle-label { font-weight: bold; }
    </style>
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="profile-container">
    <div class="left-fixed">
        <h1><b class="violet">REPORT </b><b class="red">VICTIM</b></h1>
        <p><b>Do you know this Victim? (If it is yourself, then pick KNOWN)</b></p>
    </div>
    <div class="right-scroll">
        <form method="post" action="report_victim.php">
            <?php for ($i = 0; $i < $_SESSION['report_data']['victim_count']; $i++): ?>
                <h3>Victim <?php echo $i + 1; ?></h3>
                <div class="form-group">
                    <label>Victim Acquaintance :</label>
                    <div class="toggle-container">
                        <span class="toggle-label left">KNOWN</span>
                        <div class="toggle-switch" id="toggle-victim-<?php echo $i; ?>" onclick="toggleAcquaintance('victim', <?php echo $i; ?>)">
                            <div class="toggle-button"></div>
                        </div>
                        <span class="toggle-label right">UNKNOWN</span>
                        <input type="hidden" name="victim_acquaintance_<?php echo $i; ?>" id="victim_acquaintance_<?php echo $i; ?>" value="UNKNOWN">
                    </div>
                </div>
                <div class="form-group">
                    <label>Victim NID No. :</label>
                    <input type="text" name="victim_nid_<?php echo $i; ?>" class="profileformI">
                </div>
                <div class="form-group">
                    <label>Victim Name :</label>
                    <input type="text" name="victim_first_name_<?php echo $i; ?>" placeholder="First Name" class="profileform">
                    <input type="text" name="victim_last_name_<?php echo $i; ?>" placeholder="Last Name" class="profileform">
                </div>
                <div class="form-group">
                    <label>Victim's Gender :</label>
                    <select name="victim_gender_<?php echo $i; ?>" class="profileformI">
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Victim's Age :</label>
                    <input type="number" name="victim_age_<?php echo $i; ?>" class="profileformI">
                </div>
                <div class="form-group">
                    <label>Attire Description<b class="red">*</b> :</label>
                    <textarea name="victim_attire_<?php echo $i; ?>" rows="5" class="profileformI" required></textarea>
                </div>
            <?php endfor; ?>
            <div class="btn_at_center">
                <button type="submit" class="next-btn">NEXT PAGE</button>
            </div>
        </form>
    </div>
</div>

<?php include 'include/footer.php'; ?>
<script>
function toggleAcquaintance(type, index) {
    const toggle = document.getElementById(`${type}_acquaintance_${index}`);
    const toggleSwitch = document.getElementById(`toggle-${type}-${index}`);
    toggleSwitch.classList.toggle('active');
    toggle.value = toggleSwitch.classList.contains('active') ? 'KNOWN' : 'UNKNOWN';
}
</script>
</body>
</html>