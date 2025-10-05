<?php
// report_final.php
session_start();
include 'backend/db.php';

$message = '';
if (!isset($_SESSION['report_data'])) {
    header("Location: report.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = isset($_POST['save_draft']) ? 'Draft' : (isset($_POST['delete_report']) ? 'Deleted' : 'Submitted');

    if ($status === 'Deleted') {
        unset($_SESSION['report_data']);
        header("Location: home.php");
        exit();
    }

    $conn->begin_transaction();
    try {
        $data = $_SESSION['report_data'];
        // Use NULL for Complainant_ID since login is not required
        $complainant_id = null;
        $sql = "INSERT INTO Crime (Complainant_ID, Crime_Type, Occurrence_Time, Division, District, Postal_Code, Victim_Count, Suspect_Count, Witness_Count, Crime_Description, Submission_Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $occurrence_time = $data['crime_date'] . ' ' . $data['crime_time'];
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssiiiss", $complainant_id, $data['crime_type'], $occurrence_time, $data['division'], $data['district'], $data['postal_code'], $data['victim_count'], $data['suspect_count'], $data['witness_count'], $data['crime_description'], $status);
        $stmt->execute();
        $report_id = $conn->insert_id;
        $stmt->close();

        // Insert Victims
        foreach ($data['victims'] as $victim) {
            $sql = "INSERT INTO Victim (Report_ID, Acquaintance, NID, First_Name, Last_Name, Gender, Age, Attire) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ississis", $report_id, $victim['acquaintance'], $victim['nid'], $victim['first_name'], $victim['last_name'], $victim['gender'], $victim['age'], $victim['attire_description']);
            $stmt->execute();
            $stmt->close();
        }

        // Insert Suspects
        foreach ($data['suspects'] as $suspect) {
            $sql = "INSERT INTO Suspect (Report_ID, Acquaintance, Enlistment, Criminal_Display_ID, NID, First_Name, Last_Name, Gender, Age, Attire) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $enlistment = ($suspect['criminal_record'] === 'Yes') ? 'Yes' : 'No';
            $criminal_display_id = ($suspect['recorded_id']) ? $suspect['recorded_id'] : null;
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssisssis", $report_id, $suspect['acquaintance'], $enlistment, $criminal_display_id, $suspect['nid'], $suspect['first_name'], $suspect['last_name'], $suspect['gender'], $suspect['age'], $suspect['attire_description']);
            $stmt->execute();
            $stmt->close();
        }

        // Insert Witnesses
        if (isset($data['witnesses'])) {
            foreach ($data['witnesses'] as $witness) {
                $sql = "INSERT INTO Witness (Report_ID, NID, First_Name, Last_Name, Gender, Age, Attire) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iisssis", $report_id, $witness['nid'], $witness['first_name'], $witness['last_name'], $witness['gender'], $witness['age'], $witness['attire_description']);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Insert Evidence
        if ($data['evidence_image'] || $data['evidence_video'] || $data['evidence_audio'] || $data['evidence_url']) {
            $sql = "INSERT INTO Evidence (Report_ID, Images, Video, Audio, Link) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("issss", $report_id, $data['evidence_image'], $data['evidence_video'], $data['evidence_audio'], $data['evidence_url']);
            $stmt->execute();
            $stmt->close();
        }

        $conn->commit();
        unset($_SESSION['report_data']);
        header("Location: home.php");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error: " . $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Submission | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Google Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined">
    <style>
        .summary-section { margin-bottom: 20px; }
        .summary-section h2 { color: #333; }
        .summary-section p { margin: 5px 0; }
        .evidence-image { max-width: 200px; height: auto; margin-top: 10px; }
    </style>
</head>
<body>
<?php include 'include/header.php'; ?>

<div class="profile-container">
    <div class="left-fixed">
        <h1><b class="violet">REPORT</b><b class="red"> CRIME</b></h1>
        <form method="post" action="report_final.php">
            <button type="submit" name="save_draft" class="save-draft-btn">SAVE AS DRAFT</button>
            <button type="submit" name="submit_report" class="save-changes-btn">SUBMIT REPORT</button>
            <button type="submit" name="delete_report" class="delete-account-btn">DELETE REPORT</button>
        </form>
        <button class="back-btn" onclick="window.location.href='report_witness.php'">PREVIOUS PAGE</button>
    </div>
    <div class="right-scroll">
        <?php if ($message): ?>
            <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <div class="summary-section">
            <h2>Crime Details</h2>
            <p><strong>Crime Type:</strong> <?php echo htmlspecialchars($_SESSION['report_data']['crime_type'] ?? 'N/A'); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($_SESSION['report_data']['crime_date'] ?? 'N/A'); ?></p>
            <p><strong>Time:</strong> <?php echo htmlspecialchars($_SESSION['report_data']['crime_time'] ?? 'N/A'); ?></p>
            <p><strong>Division:</strong> <?php echo htmlspecialchars($_SESSION['report_data']['division'] ?? 'N/A'); ?></p>
            <p><strong>District:</strong> <?php echo htmlspecialchars($_SESSION['report_data']['district'] ?? 'N/A'); ?></p>
            <p><strong>Postal Code:</strong> <?php echo htmlspecialchars($_SESSION['report_data']['postal_code'] ?? 'N/A'); ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($_SESSION['report_data']['crime_description'] ?? 'N/A'); ?></p>
            <p><strong>Visibility:</strong> <?php echo htmlspecialchars($_SESSION['report_data']['visibility'] ?? 'N/A'); ?></p>
        </div>
        <div class="summary-section">
            <h2>Victims</h2>
            <?php foreach ($_SESSION['report_data']['victims'] as $index => $victim): ?>
                <h3>Victim <?php echo $index + 1; ?></h3>
                <p><strong>Acquaintance:</strong> <?php echo htmlspecialchars($victim['acquaintance'] ?? 'UNKNOWN'); ?></p>
                <p><strong>NID:</strong> <?php echo htmlspecialchars($victim['nid'] ?? 'N/A'); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars(($victim['first_name'] ?? '') . ' ' . ($victim['last_name'] ?? '')); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($victim['gender'] ?? 'N/A'); ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($victim['age'] ?? 'N/A'); ?></p>
                <p><strong>Attire:</strong> <?php echo htmlspecialchars($victim['attire_description'] ?? 'N/A'); ?></p>
            <?php endforeach; ?>
        </div>
        <div class="summary-section">
            <h2>Suspects</h2>
            <?php foreach ($_SESSION['report_data']['suspects'] as $index => $suspect): ?>
                <h3>Suspect <?php echo $index + 1; ?></h3>
                <p><strong>Acquaintance:</strong> <?php echo htmlspecialchars($suspect['acquaintance'] ?? 'UNKNOWN'); ?></p>
                <p><strong>NID:</strong> <?php echo htmlspecialchars($suspect['nid'] ?? 'N/A'); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars(($suspect['first_name'] ?? '') . ' ' . ($suspect['last_name'] ?? '')); ?></p>
                <p><strong>Gender:</strong> <?php echo htmlspecialchars($suspect['gender'] ?? 'N/A'); ?></p>
                <p><strong>Age:</strong> <?php echo htmlspecialchars($suspect['age'] ?? 'N/A'); ?></p>
                <p><strong>Attire:</strong> <?php echo htmlspecialchars($suspect['attire_description'] ?? 'N/A'); ?></p>
                <p><strong>Criminal Record:</strong> <?php echo htmlspecialchars($suspect['criminal_record'] ?? 'N/A'); ?></p>
                <p><strong>Recorded ID:</strong> <?php echo htmlspecialchars($suspect['recorded_id'] ?? 'N/A'); ?></p>
            <?php endforeach; ?>
        </div>
        <div class="summary-section">
            <h2>Witnesses</h2>
            <?php if (isset($_SESSION['report_data']['witnesses']) && !empty($_SESSION['report_data']['witnesses'])): ?>
                <?php foreach ($_SESSION['report_data']['witnesses'] as $index => $witness): ?>
                    <h3>Witness <?php echo $index + 1; ?></h3>
                    <p><strong>NID:</strong> <?php echo htmlspecialchars($witness['nid'] ?? 'N/A'); ?></p>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars(($witness['first_name'] ?? '') . ' ' . ($witness['last_name'] ?? '')); ?></p>
                    <p><strong>Gender:</strong> <?php echo htmlspecialchars($witness['gender'] ?? 'N/A'); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($witness['age'] ?? 'N/A'); ?></p>
                    <p><strong>Attire:</strong> <?php echo htmlspecialchars($witness['attire_description'] ?? 'N/A'); ?></p>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No witnesses reported.</p>
            <?php endif; ?>
        </div>
        <div class="summary-section">
            <h2>Evidence</h2>
            <p><strong>Image:</strong>
                <?php if (!empty($_SESSION['report_data']['evidence_image'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($_SESSION['report_data']['evidence_image']); ?>" alt="Evidence Image" class="evidence-image">
                <?php else: ?>
                    No image uploaded.
                <?php endif; ?>
            </p>
            <p><strong>Video:</strong> <?php echo !empty($_SESSION['report_data']['evidence_video']) ? 'Video uploaded' : 'No video uploaded'; ?></p>
            <p><strong>Audio:</strong> <?php echo !empty($_SESSION['report_data']['evidence_audio']) ? 'Audio uploaded' : 'No audio uploaded'; ?></p>
            <p><strong>URL:</strong> <?php echo htmlspecialchars($_SESSION['report_data']['evidence_url'] ?? 'No URL provided'); ?></p>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>
</body>
</html>