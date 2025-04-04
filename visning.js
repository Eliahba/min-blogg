// ✅ STATUS: Oppdater skjermleserbeskjed
function setStatus(msg) {
  const statusDiv = document.getElementById("status");
  if (statusDiv) {
    statusDiv.textContent = msg;
    statusDiv.setAttribute("aria-live", "polite");
  }
}

// ✅ INNLEGG: Hent innlegg fra server og vis dem
async function loadPosts() {
  try {
    // 🔗 Hent data fra serveren
    const res = await fetch("server.php?action=load");
    const contentType = res.headers.get("content-type");

    // 🔍 VALIDER: Kontroller at det er gyldig JSON-svar
    if (!res.ok || !contentType || !contentType.includes("application/json")) {
      throw new Error("Ugyldig svar fra server");
    }

    const posts = await res.json();

    // 🧪 SJEKK: Skal være en array med innlegg
    if (!Array.isArray(posts)) {
      console.error("Forventet array, fikk:", posts);
      setStatus("Feil: Serveren returnerte ikke en liste med innlegg.");
      return;
    }

    // 📦 RENDRING: Vis innlegg i DOM
    const postsDiv = document.getElementById("posts");
    postsDiv.innerHTML = ""; // Tøm tidligere visning

    posts.forEach(post => {
      const div = document.createElement("div");
      div.innerHTML = `<p><strong>${post.user || "Anonym"}</strong>: ${post.content}</p>`;
      postsDiv.appendChild(div);
    });

  } catch (e) {
    // ❌ FEILHÅNDTERING
    console.error("Feil ved lasting av innlegg:", e);
    setStatus("Feil: Klarte ikke å laste innlegg fra server.");
  }
}

// ✅ INIT: Last innlegg automatisk når siden lastes
window.onload = loadPosts;
