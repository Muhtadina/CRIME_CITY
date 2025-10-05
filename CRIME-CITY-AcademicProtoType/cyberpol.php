<?php
// cyberpol.php
session_start();
include 'backend/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cyberpol_id = filter_input(INPUT_POST, 'cyberpol_id', FILTER_SANITIZE_STRING);
    $cell_number = filter_input(INPUT_POST, 'cell_number', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    if (empty($cyberpol_id) || empty($cell_number) || empty($password)) {
        $message = "Error: All fields are required.";
    } elseif (!preg_match('/^\d{11}$/', $cell_number)) {
        $message = "Error: Cell Number must be exactly 11 digits.";
    } elseif (strlen($cyberpol_id) > 13) {
        $message = "Error: Defense ID must not exceed 13 characters.";
    } else {
        // Check credentials
        $sql = "SELECT CyberPol_ID, Login_Pass FROM Principle_Login WHERE CyberPol_ID = ? AND Cell_Number = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $message = "Error: Database preparation failed: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $cyberpol_id, $cell_number);
            if (!$stmt->execute()) {
                $message = "Error: Failed to execute query: " . $stmt->error;
            } else {
                $result = $stmt->get_result();
                if ($result->num_rows === 0) {
                    $message = "Error: Invalid Defense ID or Cell Number.";
                } else {
                    $cyberpol = $result->fetch_assoc();
                    if (!password_verify($password, $cyberpol['Login_Pass'])) {
                        $message = "Error: Incorrect password.";
                    } else {
                        // Check for existing session
                        $sql = "SELECT COUNT(*) as login_count FROM Principle_Login WHERE CyberPol_ID = ?";
                        $stmt = $conn->prepare($sql);
                        if ($stmt === false) {
                            $message = "Error: Failed to check active sessions: " . $conn->error;
                        } else {
                            $stmt->bind_param("s", $cyberpol_id);
                            $stmt->execute();
                            $login_count = $stmt->get_result()->fetch_assoc()['login_count'];
                            if ($login_count > 1) {
                                $message = "Error: An active session already exists for this Defense ID.";
                            } else {
                                // Delete existing session and insert new one
                                $conn->begin_transaction();
                                try {
                                    $sql = "DELETE FROM Principle_Login WHERE CyberPol_ID = ?";
                                    $stmt = $conn->prepare($sql);
                                    if ($stmt === false) {
                                        throw new Exception("Failed to prepare session delete: " . $conn->error);
                                    }
                                    $stmt->bind_param("s", $cyberpol_id);
                                    if (!$stmt->execute()) {
                                        throw new Exception("Failed to delete existing session: " . $stmt->error);
                                    }
                                    $stmt->close();

                                    $sql = "INSERT INTO Principle_Login (CyberPol_ID, Cell_Number, Login_Pass) VALUES (?, ?, ?)";
                                    $stmt = $conn->prepare($sql);
                                    if ($stmt === false) {
                                        throw new Exception("Failed to prepare session insert: " . $conn->error);
                                    }
                                    $hashed_password = $cyberpol['Login_Pass'];
                                    $stmt->bind_param("sss", $cyberpol_id, $cell_number, $hashed_password);
                                    if (!$stmt->execute()) {
                                        throw new Exception("Failed to insert new session: " . $stmt->error);
                                    }
                                    $stmt->close();

                                    $conn->commit();
                                    $_SESSION['cyberpol_id'] = $cyberpol_id;
                                    header("Location: cyberpol_dashboard.php");
                                    exit();
                                } catch (Exception $e) {
                                    $conn->rollback();
                                    $message = "Error: " . $e->getMessage();
                                }
                            }
                        }
                    }
                }
                $stmt->close();
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CYBERPOL BD | CRIME CITY</title>
    <link rel="shortcut icon" href="css/img/logo-CRIMECITY.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Antic&family=Asap+Condensed:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Changa:wght@200..800&family=Comfortaa:wght@300..700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@200..700&family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&family=Rubik+Dirt&family=Rubik+Distressed&family=Rubik:ital,wght@0,300..900;1,300..900&family=Titillium+Web:ital,wght@0,200;0,300;0,400;0,600;0,700;0,900;1,200;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
<?php include 'include/cyberpol_header.php'; ?>

    <h1>CRIME CITY</h1>

    <div class="middlebox">
        <h2>CYBERPOL BD | LOGIN</h2>
        <?php if (!empty($message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form class="fillup" action="cyberpol.php" method="post">
            <input type="text" placeholder="Defense ID" class="form_input" name="cyberpol_id" required maxlength="13"/>
            <input type="text" placeholder="Cell Number" class="form_input" name="cell_number" required pattern="\d{11}" title="Cell Number must be exactly 11 digits"/>
            <input type="password" placeholder="Password" class="form_input" name="password" required/>
            <button type="submit" class="btn-login">LOGIN</button>
        </form>
        <button onclick="window.location.href='index.php'" class="btn-back">Wrong Page? GO BACK</button>
    </div>
    <div class="footer">
        <footer>
            <p>All rights reserved by CRIME CITY | Copyright &copy; 2025</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const cyberpol_id = document.querySelector('input[name="cyberpol_id"]').value;
                const cell_number = document.querySelector('input[name="cell_number"]').value;
                if (cyberpol_id.length > 13) {
                    e.preventDefault();
                    alert('Defense ID must not exceed 13 characters.');
                }
                if (!/^\d{11}$/.test(cell_number)) {
                    e.preventDefault();
                    alert('Cell Number must be exactly 11 digits.');
                }
            });
        });
    </script>
</body>
</html>