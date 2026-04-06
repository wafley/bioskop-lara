// Loader helper
let loaderCount = 0;
function loader(status) {
    loaderCount += status ? 1 : -1;
    loaderCount = Math.max(loaderCount, 0);
    $("html").attr("loader", loaderCount > 0 ? "enable" : "disable");
}

// Toggle password handler
function togglePassword(passwordId, toggleBtn) {
    const passwordInput = $("#" + passwordId);
    const type =
        passwordInput.attr("type") === "password" ? "text" : "password";
    passwordInput.attr("type", type);
    $(toggleBtn).find("i").toggleClass("fa-eye fa-eye-slash");
}

// Show toast helper
function showToast(icon, message, timer = 3000) {
    const $html = $("html");
    const isDark = $html.attr("data-theme-mode") === "dark";

    Swal.fire({
        toast: true,
        position: "top-end",
        icon: icon,
        title: message,
        showConfirmButton: false,
        timer: timer,
        background: isDark ? "rgb(43, 48, 84)" : "#fff",
    });
}

// Input Price handler
$("#price").on("input", function () {
    this.value = this.value.replace(/[^0-9]/g, "");
});
