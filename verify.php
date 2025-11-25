<?php
session_start();

// Generate token session
function gcodeGenerateSession($length = 5) {
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $token = '';
    $maxRandIndex = strlen($characters) - 1;
    for ($i = 0; $i < $length; $i++) {
        $token .= $characters[random_int(0, $maxRandIndex)];
    }
    return $token;
}

function saveSessionToken($token) {
    $_SESSION['secToken'] = $token;
}

function getSessionToken() {
    return $_SESSION['secToken'] ?? null;
}

function validateSessionToken($token) {
    return $token === getSessionToken();
}

// Pastikan userAgent cocok
if (!isset($_SESSION['userAgent']) || $_SESSION['userAgent'] !== $_SERVER['HTTP_USER_AGENT']) {
    session_regenerate_id(true);
    $_SESSION['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
}

// Inisialisasi token jika belum ada
if (!isset($_SESSION['secToken'])) {
    saveSessionToken(gcodeGenerateSession(5));
}

$securityToken = getSessionToken();

// Saat tombol "Lanjutkan" diklik
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gcodeToken'])) {
    if (validateSessionToken($_POST['gcodeToken'])) {
        // Redirect otomatis ke index.php
        echo "
        <form id='gcodeSub' method='POST' action='index.php?gToken=verified'>
            <input type='hidden' name='sessionToken' value='well'>
        </form>
        <script>document.getElementById('gcodeSub').submit();</script>";
        exit();
    } else {
        echo "<script>alert('Token tidak valid!'); window.location='verify.php';</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Memuat Keamanan...</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @keyframes slideRight {
      0% { width: 0%; }
      100% { width: 100%; }
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-bar { animation: slideRight 3s ease-out forwards; }
    .fade-in { animation: fadeIn 0.6s ease-out forwards; }
  </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen px-4">

  <form method="POST" action="" class="bg-white shadow-lg border border-gray-200 rounded-2xl p-6 space-y-6 w-full max-w-md transition-all duration-300">
    <p id="statusText" class="text-gray-600 text-center text-base">Mohon tunggu sebentar...</p>

    <div class="w-full bg-blue-100 border border-blue-300 rounded-xl overflow-hidden">
      <div class="h-2 bg-blue-600 animate-bar" id="loadingBar"></div>
    </div>

    <div id="formSection" class="space-y-4 opacity-0 hidden">
      <input type="hidden" name="gcodeToken" value="<?= htmlspecialchars($securityToken); ?>">
      <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-xl text-sm font-semibold transition duration-300 fade-in">
        Lanjutkan
      </button>
    </div>
  </form>

  <script>
    setTimeout(() => {
      document.getElementById('loadingBar').parentElement.remove(); // hilangkan loading bar
      const formSection = document.getElementById('formSection');
      const statusText = document.getElementById('statusText');
      statusText.innerHTML = "Berhasil!<br><b>Klik tombol Lanjutkan di bawah</b>";
      formSection.classList.remove('hidden');
      setTimeout(() => formSection.classList.remove('opacity-0'), 50);
    }, 2000); // lebih cepat: 2 detik
  </script>

</body>
</html>
