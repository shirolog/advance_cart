/** @format */

(() => {
  //ナビゲーションバー設定
  const $menuBtn = document.querySelector("#menu-btn");
  const $navbar = document.querySelector(".header .flex .navbar");

  $menuBtn.addEventListener("click", () => {
    $navbar.classList.toggle("active");
    $menuBtn.classList.toggle("fa-times");
  });

  window.addEventListener("scroll", () => {
    $navbar.classList.remove("active");
    $menuBtn.classList.remove("fa-times");
  });

  //edit-form-containerのオーバーレイ設定
  const $edit = document.querySelector(".edit-form-container");
  const $close = document.querySelector("#close-edit");
  $close.addEventListener("click", () => {
    $edit.style.display = "none";
    window.location.href = 'admin.php';
  });
})();
