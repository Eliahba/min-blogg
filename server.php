<?php
// 🔧 FEILVISNING: Aktiver feilmeldinger for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 🔗 API-SVAR: Sett riktig responsformat
header("Content-Type: application/json");

// 🔒 SESJON: Start PHP-session for å holde bruker logget inn
session_start();


// 🔌 DATABASE: Koble til MySQL med PDO
try {
  $pdo = new PDO(
    'mysql:host=localhost;dbname=blogg;charset=utf8',  // DB-server, databasenavn og tegnsett
    'eliah',                                            // Brukernavn
    'U22V#yXN*ghXFFj'                                   // Passord
  );
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Aktiver detaljerte feil
} catch (PDOException $e) {
  // ❌ FEIL: Returner feilmelding hvis databasen ikke kobler
  echo json_encode([
    "success" => false,
    "message" => "DB-tilkobling feilet",
    "error" => $e->getMessage()
  ]);
  exit;
}


// 📦 INNDATA: Hent JSON-data fra klienten
$data = json_decode(file_get_contents("php://input"), true);
$action = $_GET['action'] ?? $data['action'] ?? null;


// 🧠 HOVEDLOGIKK: Velg handling basert på action-type
try {

  // 👤 HANDLING: Registrering av ny bruker
  if ($action === "register") {
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $hash = password_hash($data['password'], PASSWORD_DEFAULT); // Krypter passord
    $stmt->execute([$data['username'], $hash]);
    echo json_encode(["success" => true, "message" => "Bruker registrert"]);
  }

  // 🔐 HANDLING: Innlogging av bruker
  elseif ($action === "login") {
    $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE username = ?");
    $stmt->execute([$data['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($data['password'], $user['password_hash'])) {
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['username'] = $data['username'];
      echo json_encode(["success" => true]);
    } else {
      echo json_encode(["success" => false, "message" => "Feil brukernavn/passord"]);
    }
  }

  // 📝 HANDLING: Publisere nytt innlegg
  elseif ($action === "post") {
    if (!isset($_SESSION['user_id'])) {
      echo json_encode(["success" => false, "message" => "Ikke innlogget"]);
      exit;
    }

    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, anonymous) VALUES (?, ?, ?)");
    $stmt->execute([
      $_SESSION['user_id'],
      $data['content'],
      $data['anonymous'] ? 1 : 0
    ]);
    echo json_encode(["success" => true]);
  }

  // 📥 HANDLING: Hente alle innlegg
  elseif ($action === "load") {
    $res = $pdo->query("SELECT posts.content, posts.anonymous, users.username 
                        FROM posts 
                        JOIN users ON posts.user_id = users.id 
                        ORDER BY posts.created_at DESC");

    $out = [];
    foreach ($res as $row) {
      $out[] = [
        "content" => $row['content'],
        "user" => $row['anonymous'] ? null : $row['username']
      ];
    }
    echo json_encode($out);
  }

  // 🔓 HANDLING: Logg ut bruker
  elseif ($action === "logout") {
    session_destroy();
    echo json_encode(["success" => true]);
  }

  // ⚠️ FEIL: Ugyldig action-type
  else {
    echo json_encode(["success" => false, "message" => "Ugyldig action"]);
  }

} catch (Exception $e) {
  // ❌ UNNTAK: Returner feilmelding hvis noe går galt
  echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
