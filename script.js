// ✅ STATUS: Skjermleserbeskjed
function setStatus(msg) {
  const statusDiv = document.getElementById("status");
  if (statusDiv) {
    statusDiv.textContent = msg;
    statusDiv.setAttribute("aria-live", "polite"); // Gjør status tilgjengelig for skjermlesere
  }
}

// ✅ AUTENTISERING: Logg inn
async function login() {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  try {
    const res = await fetch("server.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "login", username, password })
    });

    const data = await res.json();
    if (data.success) {
      window.location.href = "innlegg.php"; // Hvis innlogging er vellykket, send til innleggssiden
    } else {
      setStatus(data.message || "Feil brukernavn eller passord");
    }
  } catch (e) {
    console.error("Feil ved innlogging:", e);
    setStatus("Innlogging feilet – serveren svarte ikke med gyldig data.");
  }
}

// ✅ AUTENTISERING: Registrering
async function register() {
  const username = document.getElementById("username").value;
  const password = document.getElementById("password").value;

  try {
    const res = await fetch("server.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "register", username, password })
    });

    const data = await res.json();
    setStatus(data.message || "Registrering fullført.");
  } catch (e) {
    console.error("Feil ved registrering:", e);
    setStatus("Registrering feilet – sjekk serveren.");
  }
}

// ✅ INNLEGG: Publiser nytt
async function submitPost() {
  const content = document.getElementById("postContent").value;
  const anonymous = document.getElementById("anonymous").checked;

  try {
    const res = await fetch("server.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "post", content, anonymous })
    });

    const data = await res.json();
    if (data.success) {
      setStatus("Innlegg publisert!");
      loadPosts();
    } else {
      setStatus("Noe gikk galt");
    }
  } catch (e) {
    console.error("Feil ved posting:", e);
    setStatus("Klarte ikke å sende innlegget.");
  }
}

// ✅ INNLEGG: Hent og vis alle
async function loadPosts() {
  try {
    const res = await fetch("server.php?action=load");
    const contentType = res.headers.get("content-type");

    if (!res.ok || !contentType || !contentType.includes("application/json")) {
      throw new Error("Ugyldig svar fra server");
    }

    const posts = await res.json();

    if (!Array.isArray(posts)) {
      console.error("Forventet array, fikk:", posts);
      setStatus("Feil: Serveren returnerte ikke en liste med innlegg.");
      return;
    }

    const postsDiv = document.getElementById("posts");
    postsDiv.innerHTML = "";

    posts.forEach(post => {
      const div = document.createElement("div");
      div.innerHTML = `<p><strong>${post.user || "Anonym"}</strong>: ${post.content}</p>`;
      postsDiv.appendChild(div);
    });

  } catch (e) {
    console.error("Feil ved lasting av innlegg:", e);
    setStatus("Feil: Klarte ikke å laste innlegg fra server.");
  }
}

// ✅ BRUKER: Logg ut og gå til index
async function logout() {
  try {
    const res = await fetch("server.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "logout" })
    });

    const data = await res.json();
    if (data.success) {
      window.location.href = "index.html"; // Send brukeren til index.html etter utlogging
    }
  } catch (e) {
    console.error("Feil ved logout:", e);
    setStatus("Klarte ikke å logge ut.");
  }
}

if (window.location.pathname.includes("innlegg")) {
  window.onload = loadPosts; // Last innlegg når innlegg-siden lastes
}
