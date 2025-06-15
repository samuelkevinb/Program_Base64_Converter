<?php
session_start();

$hashedText = $_SESSION['hashedText'] ?? null;
$plainText = $_SESSION['plainText'] ?? null;
$error = $_SESSION['error'] ?? null;
$textToHash = $_SESSION['textToHash'] ?? ''; // Store the input for hashing
$textToVerify = $_SESSION['textToVerify'] ?? ''; // Store the input for verification
$hashToVerify = $_SESSION['hashToVerify'] ?? ''; // Store the input for hash verification
$costFactor = $_SESSION['costFactor'] ?? 12; // Default cost factor
unset($_SESSION['hashedText'], $_SESSION['plainText'], $_SESSION['error'], $_SESSION['textToHash'], $_SESSION['costFactor']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'hash') {
        $textToHash = $_POST['textToHash'] ?? '';
        $costFactor = (int)($_POST['costFactor'] ?? 10); // Get cost factor from input

        if (!empty($textToHash)) {
            // Validate cost factor
            if ($costFactor < 4 || $costFactor > 20) {
                $_SESSION['error'] = "Cost factor must be between 4 and 20.";
            } else {
                $options = ['cost' => $costFactor];
                $hashed = password_hash($textToHash, PASSWORD_BCRYPT, $options);
                $_SESSION['hashedText'] = $hashed;
                $_SESSION['textToHash'] = $textToHash; // Save the input for hashing
                $_SESSION['costFactor'] = $costFactor; // Save the cost factor
                // Clear verification inputs
                $_SESSION['textToVerify'] = ''; // Keep the text to verify empty
                $_SESSION['hashToVerify'] = ''; // Keep the hash to verify empty
            }
        } else {
            $_SESSION['error'] = "Please enter some text to hash.";
        }

    } elseif ($action === 'verify') {
        $textToVerify = $_POST['textToVerify'] ?? '';
        $hashToVerify = $_POST['hashToVerify'] ?? '';

        if (!empty($textToVerify) && !empty($hashToVerify)) {
            if (password_verify($textToVerify, $hashToVerify)) {
                $_SESSION['plainText'] = "Hash matches the text!";
            } else {
                $_SESSION['error'] = "Hash does not match the text!";
            }
        } else {
            $_SESSION['error'] = "Please enter both text to verify and hash.";
        }
        
        // Save the inputs to session to retain them
        $_SESSION['textToVerify'] = $textToVerify;
        $_SESSION['hashToVerify'] = $hashToVerify;
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Bcrypt Generator</title>
    <link rel="stylesheet" href="assets/css/style.css"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        function clearInput(inputId) {
            document.getElementById(inputId).value = '';
        }

        function copyToClipboard(id) {
            const text = document.getElementById(id).innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Text copied to clipboard!');
            });
        }
    </script>
</head>
<body>
    <?php include('includes/header.php'); ?>

    <div class="container">
        <section class="intro">
            <p>A simple tool to hash and verify passwords using Bcrypt. All processing is done locally for your privacy.</p>
        </section>

        <main class="tool-column">
            <div class="tool-box">
                <h3>Hash/Verify Password</h3>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="hash" />

                    <label for="textToHash">Text to Hash</label>
                    <div class="input-container">
                        <input type="text" name="textToHash" id="textToHash" placeholder="Enter text to hash" required value="<?= htmlspecialchars($textToHash) ?>" />
                        <button type="button" class="clear-btn" onclick="clearInput('textToHash')">✖</button>
                    </div>

                    <label for="costFactor">Cost Factor (4-20)</label>
                    <div class="input-container">
                        <select name="costFactor" id="costFactor" required>
                            <?php for ($i = 4; $i <= 20; $i++): ?>
                                <option value="<?= $i ?>" <?= $i == $costFactor ? 'selected' : '' ?>><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <button type="submit" class="generate-btn">Hash Text</button>
                </form>

                <?php if ($hashedText): ?>
                    <div class="output-box">
                        <label for="hashedResult">Hashed Result:</label>
                        <p class="hash-text" style="color:red;" id="hashedResult"><?= htmlspecialchars($hashedText) ?></p>
                        <button type="button" class="copy-btn" onclick="copyToClipboard('hashedResult')">Copy</button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="verify" />

                    <label for="hashToVerify">Hash to Verify</label>
                    <div class="input-container">
                        <input type="text" name="hashToVerify" id="hashToVerify" placeholder="Enter hash to verify against" required value="<?= htmlspecialchars($hashToVerify) ?>" />
                        <button type="button" class="clear-btn" onclick="clearInput('hashToVerify')">✖</button>
                    </div>

                    <label for="textToVerify">Text to Verify</label>
                    <div class="input-container">
                        <input type="text" name="textToVerify" id="textToVerify" placeholder="Enter text to verify" required value="<?= htmlspecialchars($textToVerify) ?>" />
                        <button type="button" class="clear-btn" onclick="clearInput('textToVerify')">✖</button>
                    </div>

                    <button type="submit" class="generate-btn">Verify Text</button>
                </form>

                <?php if ($error): ?>
                    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <?php if ($plainText): ?>
                    <div class="output-box">
                        <label for="plainResult">Verification Result:</label>
                        <p class="hash-text" style="color:red;" id="plainResult"><?= htmlspecialchars($plainText) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <section class="faq-section">
            <h2>FAQ</h2>
            <div class="tool-grid">
                <div class="tool-box">
                    <h3>What is Bcrypt and what is it used for?</h3>
                    <p>Bcrypt is a password hashing function designed to be computationally intensive, making it more resistant to brute-force attacks. It is commonly used for securely storing passwords.</p>
                </div>
                <div class="tool-box">
                    <h3>How many rounds should I use?</h3>
                    <p>12 rounds is the recommended minimum for production use. More rounds increase security but also processing time. Choose based on your security requirements.</p>
                </div>
                <div class="tool-box">
                    <h3>Can Bcrypt protect my data?</h3>
                    <p>Yes, Bcrypt is a secure way to hash passwords, making it difficult for attackers to retrieve the original password even if they gain access to the hashed data.</p>
                </div>
            </div>
        </section>

        <section class="faq-section">
            <div class="tool-grid">
                <div class="tool-box">
                    <h3>Apa itu Bcrypt dan untuk apa digunakan?</h3>
                    <p>Bcrypt adalah fungsi hashing password yang dirancang untuk menjadi intensif secara komputasi, sehingga lebih tahan terhadap serangan brute-force. Ini umum digunakan untuk menyimpan password dengan aman.</p>
                </div>
                <div class="tool-box">
                    <h3>Berapa jumlah putaran yang sebaiknya saya gunakan?</h3>
                    <p>12 putaran adalah jumlah minimum yang direkomendasikan untuk penggunaan produksi. Semakin banyak putaran akan meningkatkan keamanan, tetapi juga memperlambat waktu pemrosesan. Pilih jumlah putaran berdasarkan kebutuhan keamanan Anda.</p>
                </div>
                <div class="tool-box">
                    <h3>Apakah Bcrypt dapat melindungi data saya?</h3>
                    <p>Ya, Bcrypt adalah cara yang aman untuk melakukan hashing password, sehingga sulit bagi penyerang untuk mengambil password asli meskipun mereka mendapatkan akses ke data yang telah di-hash.</p>
                </div>
            </div>
        </section>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>
