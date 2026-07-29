(() => {
  const DEFAULT_MESSAGE =
    "Hola buenos días, quisiera información sobre la matrícula y demás información.";
  const DEFAULT_PHONE = "923008704";

  const buildWhatsAppUrl = ({ message, phone } = {}) => {
    const text = encodeURIComponent(message || DEFAULT_MESSAGE);

    const finalPhone =
      typeof phone === "string" && phone.trim().length > 0 ? phone.trim() : DEFAULT_PHONE;

    if (finalPhone && String(finalPhone).trim().length > 0) {
      return `https://wa.me/${encodeURIComponent(String(finalPhone).trim())}?text=${text}`;
    }

    // Sin número: abre WhatsApp con el texto listo para enviar
    return `https://wa.me/?text=${text}`;
  };

  const setupNav = () => {
    const toggle = document.querySelector(".nav-toggle");
    const nav = document.querySelector(".nav");
    if (!toggle || !nav) return;

    const setOpen = (open) => {
      nav.classList.toggle("is-open", open);
      toggle.setAttribute("aria-expanded", String(open));
    };

    toggle.addEventListener("click", () => {
      const isOpen = nav.classList.contains("is-open");
      setOpen(!isOpen);
    });

    document.addEventListener("click", (e) => {
      if (!nav.classList.contains("is-open")) return;
      const target = e.target;
      if (!(target instanceof Element)) return;

      if (target.closest(".nav") || target.closest(".nav-toggle")) return;
      setOpen(false);
    });

    nav.addEventListener("click", (e) => {
      const target = e.target;
      if (!(target instanceof Element)) return;
      if (target.matches("a.nav__link")) setOpen(false);
    });

    window.addEventListener("resize", () => {
      if (window.matchMedia("(min-width: 721px)").matches) {
        setOpen(false);
      }
    });
  };

  const setupRevealOnScroll = () => {
    const items = Array.from(document.querySelectorAll(".reveal"));
    if (items.length === 0) return;

    const observer = new IntersectionObserver(
      (entries) => {
        for (const entry of entries) {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            observer.unobserve(entry.target);
          }
        }
      },
      { threshold: 0.12 }
    );

    for (const el of items) observer.observe(el);
  };

  const setupWhatsAppLinks = () => {
    const floatBtn = document.getElementById("wa-float");
    const ctaBtn = document.getElementById("cta-whatsapp");
    const form = document.getElementById("contact-form");

    const url = buildWhatsAppUrl();
    if (floatBtn) floatBtn.setAttribute("href", url);
    if (ctaBtn) ctaBtn.setAttribute("href", url);

    if (form) {
      form.addEventListener("submit", (e) => {
        e.preventDefault();

        const nombre = (document.getElementById("nombre")?.value || "").trim();
        const telefono = (document.getElementById("telefono")?.value || "").trim();
        const mensaje = (document.getElementById("mensaje")?.value || "").trim();

        const composed =
          `${DEFAULT_MESSAGE}` +
          (nombre ? `\n\nNombre: ${nombre}` : "") +
          (telefono ? `\nTeléfono: ${telefono}` : "") +
          (mensaje ? `\n\nMensaje: ${mensaje}` : "");

        window.open(
          buildWhatsAppUrl({ message: composed }),
          "_blank",
          "noopener,noreferrer"
        );
      });
    }
  };

  const setupLightbox = () => {
    const root = document.getElementById("lightbox");
    const img = document.getElementById("lightbox-img");
    const triggers = Array.from(document.querySelectorAll("[data-lightbox-src]"));

    if (!root || !img || triggers.length === 0) return;

    const open = (src, alt) => {
      img.src = src;
      img.alt = alt || "Vista previa";
      root.classList.add("is-open");
      root.setAttribute("aria-hidden", "false");
      document.documentElement.style.overflow = "hidden";
    };

    const close = () => {
      root.classList.remove("is-open");
      root.setAttribute("aria-hidden", "true");
      document.documentElement.style.overflow = "";

      // Limpieza ligera
      img.src = "";
    };

    triggers.forEach((btn) => {
      btn.addEventListener("click", () => {
        const src = btn.getAttribute("data-lightbox-src");
        const alt = btn.querySelector("img")?.getAttribute("alt") || "";
        if (!src) return;
        open(src, alt);
      });
    });

    root.addEventListener("click", (e) => {
      const target = e.target;
      if (!(target instanceof Element)) return;
      if (target.hasAttribute("data-lightbox-close")) close();
    });

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && root.classList.contains("is-open")) close();
    });
  };

  setupNav();
  setupRevealOnScroll();
  setupWhatsAppLinks();
  setupLightbox();
})();
