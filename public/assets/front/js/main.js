//  hearder and search

const header = document.querySelector(".airbnb-header");
const compactSearch = document.querySelector(".search-small");
const largeSearch = document.querySelector(".search-large");
const searchContainer = document.querySelector("#searchContainer");

const fields = document.querySelectorAll(".search-field");
const panels = document.querySelectorAll(".panel");

/* HEADER SHRINK ON SCROLL */
window.addEventListener("scroll", () => {
  if (window.scrollY > 80 && !header.classList.contains("force-open")) {
    header.classList.add("shrink");
  } else if (!header.classList.contains("force-open")) {
    header.classList.remove("shrink");
  }
});

/* CLICK COMPACT SEARCH → OPEN LARGE SEARCH */
compactSearch.addEventListener("click", () => {
  header.classList.remove("shrink");
  header.classList.add("force-open");

  // Activate WHERE by default
  fields.forEach((f) => f.classList.remove("active"));
  panels.forEach((p) => p.classList.remove("active"));

  const whereField = document.querySelector('[data-panel="where"]');
  const wherePanel = document.querySelector(".panel-where");

  whereField.classList.add("active");
  wherePanel.classList.add("active");
});

/* SEARCH FIELD CLICK */
fields.forEach((field) => {
  field.addEventListener("click", (e) => {
    e.stopPropagation();

    fields.forEach((f) => f.classList.remove("active"));
    panels.forEach((p) => p.classList.remove("active"));

    field.classList.add("active");
    document
      .querySelector(`.panel-${field.dataset.panel}`)
      .classList.add("active");
  });
});

/* CLICK OUTSIDE → CLOSE SEARCH */
document.addEventListener("click", (e) => {
  if (
    !e.target.closest(".search-container") &&
    !e.target.closest(".search-panels") &&
    !e.target.closest(".search-small")
  ) {
    header.classList.remove("force-open");
    fields.forEach((f) => f.classList.remove("active"));
    panels.forEach((p) => p.classList.remove("active"));
  }
});

// User menu

const userBtn = document.querySelector(".user-btn");
const userMenu = document.getElementById("userMenu");

userBtn.addEventListener("click", (e) => {
  e.stopPropagation();
  userMenu.style.display =
    userMenu.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", () => {
  userMenu.style.display = "none";
});

// counter

document.querySelectorAll(".counter").forEach((counter) => {
  const minusBtn = counter.querySelector(".minus");
  const plusBtn = counter.querySelector(".plus");
  const countEl = counter.querySelector(".count");

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

// Recently viewed carosal

const carousel = document.getElementById("recentCarousel");
const prevBtn = document.querySelector(".carousel-btn.prev");
const nextBtn = document.querySelector(".carousel-btn.next");

nextBtn.addEventListener("click", () => {
  carousel.scrollLeft += 300;
});

prevBtn.addEventListener("click", () => {
  carousel.scrollLeft -= 300;
});

document.querySelectorAll(".airbnb-section").forEach((section) => {
  const carousel = section.querySelector(".carousel");
  const prev = section.querySelector(".carousel-btn.prev");
  const next = section.querySelector(".carousel-btn.next");

  if (!carousel) return;

  next.addEventListener("click", () => {
    carousel.scrollLeft += carousel.clientWidth;
  });

  prev.addEventListener("click", () => {
    carousel.scrollLeft -= carousel.clientWidth;
  });
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
