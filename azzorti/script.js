(() => {
  const WHATSAPP_URL = "https://wa.me/51917902822";

  const setYear = () => {
    const el = document.getElementById("year");
    if (el) el.textContent = String(new Date().getFullYear());
  };

  const initMobileMenu = () => {
    const btn = document.querySelector(".menu");
    const mobile = document.querySelector(".mobile");
    if (!btn || !mobile) return;

    const close = () => {
      mobile.hidden = true;
      btn.setAttribute("aria-expanded", "false");
    };

    btn.addEventListener("click", () => {
      const nextHidden = !mobile.hidden ? true : false;
      mobile.hidden = nextHidden;
      btn.setAttribute("aria-expanded", String(!nextHidden));
    });

    mobile.addEventListener("click", (e) => {
      const a = e.target.closest("a");
      if (a) close();
    });

    window.addEventListener("resize", () => {
      if (window.matchMedia("(min-width: 761px)").matches) close();
    });
  };

  const initRevealOnScroll = () => {
    const els = Array.from(document.querySelectorAll("[data-animate]"));
    if (!els.length) return;

    const reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (reduce) {
      els.forEach((el) => el.classList.add("is-visible"));
      return;
    }

    const io = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("is-visible");
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12 }
    );

    els.forEach((el) => io.observe(el));
  };

  const initSlider = () => {
    const root = document.querySelector("[data-slider]");
    const track = document.querySelector("[data-slider-track]");
    const slides = Array.from(document.querySelectorAll("[data-slide]"));
    const btnPrev = document.querySelector("[data-slider-prev]");
    const btnNext = document.querySelector("[data-slider-next]");
    const dotsRoot = document.querySelector("[data-slider-dots]");

    if (!root || !track || slides.length === 0 || !dotsRoot) return;

    const reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    let index = 0;
    let timer = null;

    const goTo = (nextIndex) => {
      index = (nextIndex + slides.length) % slides.length;
      track.style.transform = `translateX(${-index * 100}%)`;
      const dots = Array.from(dotsRoot.querySelectorAll(".slider__dot"));
      dots.forEach((d, i) => d.setAttribute("aria-current", i === index ? "true" : "false"));
    };

    const makeDots = () => {
      dotsRoot.innerHTML = "";
      slides.forEach((_, i) => {
        const dot = document.createElement("button");
        dot.type = "button";
        dot.className = "slider__dot";
        dot.setAttribute("aria-label", `Ir a slide ${i + 1}`);
        dot.setAttribute("aria-current", i === index ? "true" : "false");
        dot.addEventListener("click", () => {
          stop();
          goTo(i);
          start();
        });
        dotsRoot.appendChild(dot);
      });
    };

    const start = () => {
      if (reduce) return;
      stop();
      timer = window.setInterval(() => goTo(index + 1), 5200);
    };

    const stop = () => {
      if (timer) {
        window.clearInterval(timer);
        timer = null;
      }
    };

    btnPrev?.addEventListener("click", () => {
      stop();
      goTo(index - 1);
      start();
    });

    btnNext?.addEventListener("click", () => {
      stop();
      goTo(index + 1);
      start();
    });

    root.addEventListener("mouseenter", stop);
    root.addEventListener("mouseleave", start);
    root.addEventListener("focusin", stop);
    root.addEventListener("focusout", start);

    makeDots();
    goTo(0);
    start();

    document.addEventListener("visibilitychange", () => {
      if (document.hidden) stop();
      else start();
    });
  };

  const initWhatsappButtons = () => {
    const wa = Array.from(document.querySelectorAll('a[href*="wa.me/"]'));
    wa.forEach((a) => {
      a.addEventListener("click", () => {
        if (!a.getAttribute("href")) a.setAttribute("href", WHATSAPP_URL);
      });
    });
  };

  setYear();
  initMobileMenu();
  initRevealOnScroll();
  initSlider();
  initWhatsappButtons();
})();
