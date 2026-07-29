const state = {
  projects: [],
  filtered: [],
};

const qs = (sel, root = document) => root.querySelector(sel);
const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

function formatWhatsAppLink(phone, message) {
  const clean = String(phone || "").replace(/[^0-9]/g, "");
  const text = encodeURIComponent(message || "Hola, vi tu portafolio y me interesa una página web para mi negocio.");
  return `https://wa.me/${clean}?text=${text}`;
}

function setWhatsAppLinks() {
  const phone = window.PORTFOLIO_CONFIG?.whatsappPhone || "";
  const message = window.PORTFOLIO_CONFIG?.whatsappMessage || "";
  const link = formatWhatsAppLink(phone, message);

  const ids = ["cta-whatsapp", "cta-whatsapp-2", "wa-float", "footer-whatsapp"];
  for (const id of ids) {
    const el = document.getElementById(id);
    if (el) el.setAttribute("href", link);
  }
}

function projectCard(p) {
  const href = p.url || "#";
  const img = p.image ? `<img loading="lazy" src="${p.image}" alt="Vista previa de ${escapeHtml(p.name)}" />` : "";

  return `
    <article class="project" data-name="${escapeHtml(p.name)}" data-tags="${escapeHtml((p.tags || []).join(" "))}">
      <div class="project__media">${img}</div>
      <div class="project__body">
        <h3 class="project__title">${escapeHtml(p.name)}</h3>
        <p class="project__desc">${escapeHtml(p.description || "")}</p>
        <div class="project__actions">
          <div class="project__meta">${escapeHtml(p.stack || "Sitio web")}</div>
          <a class="btn btn--primary btn--small" href="${href}" target="_blank" rel="noreferrer">Ver página</a>
        </div>
      </div>
    </article>
  `.trim();
}

function escapeHtml(str) {
  return String(str)
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function renderProjects() {
  const root = qs("#projects");
  if (!root) return;

  const list = state.filtered;
  if (!list.length) {
    root.innerHTML = `
      <div class="card" style="grid-column: span 12">
        <h3 class="card__title">No se encontraron proyectos</h3>
        <p class="card__text">Prueba con otro término de búsqueda o revisa <span class="mono">data/projects.json</span>.</p>
      </div>
    `;
    return;
  }

  root.innerHTML = list.map(projectCard).join("\n");
}

function applyFilter(term) {
  const t = String(term || "").trim().toLowerCase();
  if (!t) {
    state.filtered = state.projects;
    return;
  }

  state.filtered = state.projects.filter((p) => {
    const hay = `${p.name || ""} ${p.description || ""} ${(p.tags || []).join(" ")} ${p.stack || ""}`.toLowerCase();
    return hay.includes(t);
  });
}

async function loadProjects() {
  const res = await fetch("./data/projects.json", { cache: "no-store" });
  if (!res.ok) throw new Error("No se pudo cargar data/projects.json");
  const data = await res.json();
  return Array.isArray(data) ? data : data.projects || [];
}

function getEmbeddedProjectsFallback() {
  return [
    {
      id: "pagina_de_servicio_fotocopiadoras",
      name: "Servicio de Fotocopiadoras",
      description: "Landing para servicios con secciones y llamado a la acción.",
      url: "https://ofercopyperu.netlify.app",
      image: "./img/pagina_de_servicio_fotocopiadoras.jpg",
      stack: "HTML / CSS / JS",
      tags: ["negocio", "landing", "servicios"],
    },
    {
      id: "azzorti",
      name: "Azzorti",
      description: "Landing para catálogo/presentación de producto.",
      url: "https://azzorti.netlify.app",
      image: "./img/azzorti.jpg",
      stack: "HTML",
      tags: ["landing", "catalogo", "negocio"],
    },
    {
      id: "pagina_noticias",
      name: "Página de Noticias",
      description: "Sitio estilo portal con secciones y páginas internas.",
      url: "https://perunoticias.netlify.app",
      image: "./img/pagina_noticias.jpg",
      stack: "HTML / CSS / JS",
      tags: ["noticias", "portal", "multi-pagina"],
    },
    {
      id: "paginacolegio",
      name: "Página de Colegio",
      description: "Sitio informativo para institución educativa.",
      url: "https://colegiojesusredentor.netlify.app",
      image: "./img/pagina_colegio.jpg",
      stack: "HTML / CSS / JS",
      tags: ["educacion", "institucion", "informativo"],
    },
    {
      id: "servicios_streming",
      name: "Servicios de Streaming",
      description: "Página enfocada en presentación de planes/servicios.",
      url: "https://renatostreaming.netlify.app",
      image: "./img/servicios_streaming.png",
      stack: "HTML",
      tags: ["streaming", "planes", "ventas"],
    },
  ];
}

function setupSearch() {
  const input = qs("#search");
  if (!input) return;

  input.addEventListener("input", (e) => {
    applyFilter(e.target.value);
    renderProjects();
  });
}

function setupRevealOnScroll() {
  const els = qsa("[data-animate]");
  if (!els.length) return;

  const io = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          io.unobserve(entry.target);
        }
      }
    },
    { threshold: 0.14 }
  );

  for (const el of els) io.observe(el);
}

function animateCounters() {
  const els = qsa("[data-counter]");
  if (!els.length) return;

  const run = (el) => {
    const to = Number(el.getAttribute("data-value") || "0");
    const duration = 900;
    const start = performance.now();

    const tick = (now) => {
      const p = Math.min(1, (now - start) / duration);
      const eased = 1 - Math.pow(1 - p, 3);
      const value = Math.round(to * eased);
      el.textContent = String(value);
      if (p < 1) requestAnimationFrame(tick);
    };

    requestAnimationFrame(tick);
  };

  const io = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          run(entry.target);
          io.unobserve(entry.target);
        }
      }
    },
    { threshold: 0.35 }
  );

  for (const el of els) io.observe(el);
}

function setYear() {
  const el = qs("#year");
  if (el) el.textContent = String(new Date().getFullYear());
}

async function main() {
  setYear();
  setWhatsAppLinks();
  setupRevealOnScroll();
  animateCounters();
  setupSearch();

  try {
    state.projects = await loadProjects();
    state.filtered = state.projects;
    renderProjects();
  } catch (err) {
    state.projects = getEmbeddedProjectsFallback();
    state.filtered = state.projects;
    renderProjects();
  }
}

main();
