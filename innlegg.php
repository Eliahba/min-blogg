<?php
// 🔒 SIKKERHET: Krever innlogging
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: index.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="no" role="document">
<head>
  <!-- 🔧 TILRETTELEGGING: Responsiv visning, universell utforming -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Skriv og les innlegg">
  <title>Innlegg</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- ♿ TASTATURNAVIGASJON: Skip-lenke for tilgjengelighet -->
  <a href="#postForm" class="skip-link">Hopp til innhold</a>

  <!-- 🔓 BRUKER: Logg ut-knapp i hjørnet -->
  <button id="logoutBtn" class="logout-btn" onclick="logout()" aria-label="Logg ut av kontoen">Logg ut</button>

  <main id="app" role="main">
    <!-- 🧾 OVERSKRIFT -->
    <h1>📝 Publiser og les innlegg</h1>

    <!-- 📝 SKJEMA: Nytt innlegg -->
    <section id="postForm" aria-labelledby="post-title">
      <h2 id="post-title">Skriv nytt innlegg</h2>

      <label for="postContent">Innleggstekst</label>
      <textarea id="postContent" name="postContent" rows="6" aria-required="true"></textarea>

      <label for="anonymous">
        <input type="checkbox" id="anonymous"> Post anonymt
      </label>

      <button onclick="submitPost()" aria-label="Publiser innlegget">Publiser</button>
    </section>

    <!-- ♿ STATUS: Skjermleservennlig tilbakemelding -->
    <div id="status" role="status" aria-live="polite" class="statusbox"></div>

    <!-- 📰 VISNING: Liste med publiserte innlegg -->
    <section aria-labelledby="feed-title">
      <h2 id="feed-title">Alle innlegg</h2>
      <div id="posts" aria-live="polite" aria-label="Innleggsliste"></div>
    </section>
  </main>

  <script src="script.js"></script>
</body>
</html>
