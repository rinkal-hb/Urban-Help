// Map page specific JavaScript

// Image slider for listing cards
document.querySelectorAll(".listing-card").forEach((card) => {
  let index = 0;
  const slides = card.querySelectorAll(".slide");
  const dots = card.querySelectorAll(".dot");

  if (slides.length === 0) return;

  const show = (i) => {
    slides.forEach((slide) => slide.classList.remove("active"));
    dots.forEach((dot) => dot.classList.remove("active"));
    if (slides[i]) {
      slides[i].classList.add("active");
      if (dots[i]) dots[i].classList.add("active");
    }
  };

  const nextBtn = card.querySelector(".nav.next");
  const prevBtn = card.querySelector(".nav.prev");

  if (nextBtn) {
    nextBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      index = (index + 1) % slides.length;
      show(index);
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      index = (index - 1 + slides.length) % slides.length;
      show(index);
    });
  }

  // Dot navigation
  dots.forEach((dot, i) => {
    dot.addEventListener("click", (e) => {
      e.preventDefault();
      e.stopPropagation();
      index = i;
      show(index);
    });
  });

  show(0);
});

// Header functionality
const header = document.querySelector(".airbnb-header");
const compactSearch = document.querySelector(".search-small");
const searchBtn = compactSearch?.querySelector(".search-btn");
const fields = document.querySelectorAll(".search-field");
const panels = document.querySelectorAll(".panel");

// Always show compact search
if (header) {
  header.classList.add("shrink");
}

// Click search button to toggle
if (searchBtn) {
  searchBtn.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();

    if (header.classList.contains("force-open")) {
      header.classList.remove("force-open");
      header.classList.add("shrink");
      fields.forEach((f) => f.classList.remove("active"));
      panels.forEach((p) => p.classList.remove("active"));
    } else {
      header.classList.add("force-open");
      header.classList.remove("shrink");
      fields.forEach((f) => f.classList.remove("active"));
      panels.forEach((p) => p.classList.remove("active"));
      const whereField = document.querySelector('[data-panel="where"]');
      const wherePanel = document.querySelector(".panel-where");
      if (whereField) whereField.classList.add("active");
      if (wherePanel) wherePanel.classList.add("active");
    }
  });
}

// Field clicks
fields.forEach((field) => {
  field.addEventListener("click", (e) => {
    e.stopPropagation();
    fields.forEach((f) => f.classList.remove("active"));
    panels.forEach((p) => p.classList.remove("active"));
    field.classList.add("active");
    const panel = document.querySelector(`.panel-${field.dataset.panel}`);
    if (panel) panel.classList.add("active");
  });
});

// Click outside to close
document.addEventListener("click", (e) => {
  if (
    !e.target.closest(".search-container") &&
    !e.target.closest(".search-panels") &&
    !e.target.closest(".search-small")
  ) {
    header.classList.remove("force-open");
    header.classList.add("shrink");
    fields.forEach((f) => f.classList.remove("active"));
    panels.forEach((p) => p.classList.remove("active"));
  }
});

// calender js

const monthsEl = document.querySelectorAll(".month");
const whenInput = document.querySelector('[data-panel="when"] input');

let startDate = null;
let endDate = null;
let current = new Date();

function renderCalendar() {
  monthsEl.forEach((monthEl, i) => {
    const date = new Date(current.getFullYear(), current.getMonth() + i, 1);
    const year = date.getFullYear();
    const month = date.getMonth();

    monthEl.innerHTML = `
      <h5>${date.toLocaleString("default", { month: "long" })} ${year}</h5>
      <div class="weekdays">
        ${["S", "M", "T", "W", "T", "F", "S"]
          .map((d) => `<span>${d}</span>`)
          .join("")}
      </div>
      <div class="days"></div>
    `;

    const daysEl = monthEl.querySelector(".days");
    const firstDay = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
      daysEl.innerHTML += `<span></span>`;
    }

    for (let d = 1; d <= totalDays; d++) {
      const fullDate = new Date(year, month, d);
      const today = new Date();
      today.setHours(0, 0, 0, 0);

      let cls = "";
      if (fullDate < today) cls = "disabled";

      if (startDate && fullDate.getTime() === startDate.getTime())
        cls += " selected";
      if (endDate && fullDate.getTime() === endDate.getTime())
        cls += " selected";
      if (startDate && endDate && fullDate > startDate && fullDate < endDate)
        cls += " in-range";

      daysEl.innerHTML += `<span class="${cls}" data-date="${fullDate.toISOString()}">${d}</span>`;
    }
  });
}

document.addEventListener("click", (e) => {
  if (
    e.target.matches(".days span") &&
    !e.target.classList.contains("disabled")
  ) {
    const clickedDate = new Date(e.target.dataset.date);

    if (!startDate || endDate) {
      startDate = clickedDate;
      endDate = null;
    } else {
      if (clickedDate < startDate) {
        startDate = clickedDate;
      } else {
        endDate = clickedDate;
        whenInput.value =
          startDate.toLocaleDateString() + " – " + endDate.toLocaleDateString();
      }
    }
    renderCalendar();
  }
});

document.querySelector(".next").onclick = () => {
  current.setMonth(current.getMonth() + 1);
  renderCalendar();
};

document.querySelector(".prev").onclick = () => {
  current.setMonth(current.getMonth() - 1);
  renderCalendar();
};

renderCalendar();

// User menu
const userBtn = document.querySelector(".user-btn");
const userMenu = document.getElementById("userMenu");

if (userBtn && userMenu) {
  userBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    userMenu.style.display =
      userMenu.style.display === "block" ? "none" : "block";
  });

  document.addEventListener("click", () => {
    userMenu.style.display = "none";
  });
}

// Counter functionality
document.querySelectorAll(".counter").forEach((counter) => {
  const minusBtn = counter.querySelector(".counter-btn.minus");
  const plusBtn = counter.querySelector(".counter-btn.plus");
  const countEl = counter.querySelector(".count");

  if (!minusBtn || !plusBtn || !countEl) return;

  let count = 0;

  const update = () => {
    countEl.textContent = count;
    minusBtn.disabled = count === 0;
  };

  plusBtn.addEventListener("click", () => {
    count++;
    update();
  });

  minusBtn.addEventListener("click", () => {
    if (count > 0) {
      count--;
      update();
    }
  });

  update();
});

// heart js
document.addEventListener("click", (e) => {
  if (e.target.classList.contains("heart")) {
    const heart = e.target;

    heart.classList.toggle("active");

    // Toggle outline ↔ filled heart
    heart.textContent = heart.classList.contains("active") ? "♥" : "♡";
  }
});
