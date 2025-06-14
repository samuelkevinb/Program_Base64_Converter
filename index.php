<?php
session_start();

$encodedText = $_SESSION['encodedText'] ?? null;
$decodedText = $_SESSION['decodedText'] ?? null;
$error = $_SESSION['error'] ?? null;
$textToEncode = $_SESSION['textToEncode'] ?? ''; // Store the input for encoding
$textToDecode = $_SESSION['textToDecode'] ?? ''; // Store the input for decoding
unset($_SESSION['encodedText'], $_SESSION['decodedText'], $_SESSION['error'], $_SESSION['textToEncode'], $_SESSION['textToDecode']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'encode') {
        $textToEncode = $_POST['textToEncode'] ?? '';

        if (!empty($textToEncode)) {
            $encoded = base64_encode($textToEncode);
            $_SESSION['encodedText'] = $encoded;
            $_SESSION['textToEncode'] = $textToEncode; // Save the input for encoding
        } else {
            $_SESSION['error'] = "Please enter some text to encode.";
        }

    } elseif ($action === 'decode') {
        $textToDecode = $_POST['base64ToVerify'] ?? '';

        if (!empty($textToDecode)) {
            $decoded = base64_decode($textToDecode, true);
            if ($decoded === false) {
                $_SESSION['error'] = "Invalid Base64 string.";
            } else {
                $_SESSION['decodedText'] = $decoded;
                $_SESSION['textToDecode'] = $textToDecode; // Save the input for decoding
            }
        } else {
            $_SESSION['error'] = "Please enter some text to decode.";
        }
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
    <title>Base64 Converter</title>
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
            <h2>Base64 Converter</h2>
            <p>A simple tool to encode and decode Base64. All processing is done locally for your privacy.</p>
        </section>

        <main class="tool-column">
            <div class="tool-box">
                <h3>Encode/Decode Base64</h3>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="encode" />

                    <label for="textToEncode">Text to Encode to Base64</label>
                    <div class="input-container">
                        <input type="text" name="textToEncode" id="textToEncode" placeholder="Enter text to Base64" required value="<?= htmlspecialchars($textToEncode) ?>" />
                        <button type="button" class="clear-btn" onclick="clearInput('textToEncode')">✖</button>
                    </div>

                    <button type="submit" class="generate-btn">Encode Base64</button>
                </form>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="decode" />

                    <label for="base64ToVerify">Base64 String to Decode</label>
                    <div class="input-container">
                        <input type="text" name="base64ToVerify" id="base64ToVerify" placeholder="Enter Base64 string" required value="<?= htmlspecialchars($textToDecode) ?>" />
                        <button type="button" class="clear-btn" onclick="clearInput('base64ToVerify')">✖</button>
                    </div>

                    <button type="submit" class="generate-btn">Decode Base64</button>
                </form>

                <?php if ($error): ?>
                    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <?php if ($encodedText): ?>
                    <div class="output-box">
                        <label for="encodedResult">Encoded Result:</label>
                        <p class="hash-text" id="encodedResult"><?= htmlspecialchars($encodedText) ?></p>
                        <button type="button" onclick="copyToClipboard('encodedResult')">Copy</button>
                    </div>
                <?php endif; ?>

                <?php if ($decodedText): ?>
                    <div class="output-box">
                        <label for="decodedResult">Decoded Result:</label>
                        <p class="hash-text" id="decodedResult"><?= htmlspecialchars($decodedText) ?></p>
                        <button type="button" onclick="copyToClipboard('decodedResult')">Copy</button>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <section class="faq-section">
            <h2>FAQ</h2>
            <div class="tool-grid">
                <div class="tool-box">
                    <h3>What is Base64 and what is it used for?</h3>
                    <p>Base64 is a way to turn data into plain text so it can be easily stored or shared. It's often used when you need to include images, files, or special characters in places that only support text like emails, web pages, or APIs.</p>
                </div>
                <div class="tool-box">
                    <h3>Is it safe to use this tool for sensitive data?</h3>
                    <p>Yes. All encoding and decoding is performed directly on your device, with no data sent to any server, no cookies, and no tracking. Your privacy is fully protected.</p>
                </div>
                <div class="tool-box">
                    <h3>Can Base64 protect my data?</h3>
                    <p>Not really. Base64 is not a security feature, it’s just a way to encode data into a readable format. Anyone who decodes it can see the original content. If you need to protect sensitive data, consider using proper encryption methods instead.</p>
                </div>
            </div>
        </section>

        <section class="faq-section">
            <div class="tool-grid">
                <div class="tool-box">
                    <h3>Apa itu Base64 dan untuk apa digunakan?</h3>
                    <p>Base64 adalah cara untuk mengubah data menjadi teks biasa agar lebih mudah disimpan atau dibagikan. Biasanya digunakan saat kamu ingin menyisipkan gambar, file, atau karakter khusus di tempat yang hanya mendukung teks seperti email, halaman web, atau API.</p>
                </div>
                <div class="tool-box">
                    <h3>Apakah aman menggunakan alat ini untuk data sensitif?</h3>
                    <p>Ya. Semua pengkodean dan penguraian dilakukan langsung di perangkat Anda, tanpa data yang dikirim ke server mana pun, tanpa cookie, dan tanpa pelacakan. Privasi Anda sepenuhnya dilindungi.</p>
                </div>
                <div class="tool-box">
                    <h3>Apakah Base64 dapat melindungi data saya?</h3>
                    <p>Tidak benar-benar bisa. Base64 bukanlah fitur keamanan, ini hanya cara untuk mengkodekan data ke dalam format yang dapat dibaca. Siapa pun yang menguraikannya dapat melihat konten aslinya. Jika Anda perlu melindungi data sensitif, pertimbangkan untuk menggunakan metode enkripsi yang tepat.</p>
                </div>
            </div>
        </section>
    </div>

    <?php include('includes/footer.php'); ?>
</body>
</html>
