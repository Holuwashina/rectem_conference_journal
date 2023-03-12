"use strict";

const inputAuthor = document.querySelector("#author");
const inpuTitle = document.querySelector("#title");
const updateBtn = document.querySelector("#update");
const alertEL = document.querySelectorAll(".alert");

const toastTrigger = document.getElementById("liveToastBtn");
const toastLiveExample = document.getElementById("liveToast");

let author;
let title;

if (inputAuthor) {
  inputAuthor.addEventListener("keyup", (e) => {
    author = e.currentTarget.value;
  });
}

if (inpuTitle) {
  inpuTitle.addEventListener("keyup", (e) => {
    title = e.currentTarget.value;
  });
}

if (updateBtn) {
  updateBtn.addEventListener("click", (e) => {
    e.preventDefault();
    location.href = `research.php?update=1&research_id=${inpuTitle.getAttribute(
      "research-id"
    )}&research_title=${title || inpuTitle.value}&research_author=${
      author || inputAuthor.value
    }`;
  });
}

function registerToast() {
  const toast = new bootstrap.Toast(toastLiveExample);
  toast.show();
}

if (location.pathname.includes("index.php")) {
  document.addEventListener("DOMContentLoaded", registerToast, { once: true });
}

alertEL.forEach(function (alert, index) {
  setTimeout(() => {
    alert.remove();
  }, (index + 1) * 2000);
});
