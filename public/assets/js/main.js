// Setup global AJAX behavior
$.ajaxSetup({
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
    },
    error: function (xhr) {
        if (xhr.status === 401 || xhr.status === 419) {
            showToast("warning", "Silahkan login untuk melanjutkan.", 5000);
            loadPage(window.routes.login);
            history.pushState(null, null, window.routes.login);
        }
    },
});

// Theme logic
const $html = $("html");
const $toggle = $(".layout-setting");
const $darkIcon = $(".dark-layout");
const $lightIcon = $(".light-layout");

function applyTheme(theme) {
    if (theme === "dark") {
        $html.attr({
            "data-theme-mode": "dark",
            "data-menu-styles": "dark",
            "data-default-header-styles": "dark",
        });
        $darkIcon.hide();
        $lightIcon.show();
    } else {
        $html.attr({
            "data-theme-mode": "light",
            "data-menu-styles": "light",
            "data-default-header-styles": "light",
        });
        $darkIcon.show();
        $lightIcon.hide();
    }

    localStorage.setItem("theme-mode", theme);
}

const savedTheme = localStorage.getItem("theme-mode") || "light";
applyTheme(savedTheme);

$toggle.on("click", function (e) {
    e.preventDefault();
    const currentTheme = $html.attr("data-theme-mode");
    const newTheme = currentTheme === "dark" ? "light" : "dark";
    applyTheme(newTheme);
});

//
$(document).on("submit", "form[data-ajax='true']", function (e) {
    e.preventDefault();

    let $form = $(this);
    let url = $form.attr("action");
    let method = $form.attr("method") || "POST";

    let formData = new FormData(this);

    let btnSubmit = $form.find("button[type=submit]");
    let btnText = btnSubmit.text();
    let loadingText = btnSubmit.data("loading-text") || "Loading...";

    btnSubmit.prop("disabled", true).text(loadingText);

    ajaxRequest({
        url,
        method,
        data: formData,
        isFormData: true,
        onComplete: () => {
            btnSubmit.prop("disabled", false).text(btnText);

            let modalEl = $form.closest(".modal")[0];
            if (modalEl) {
                let modalInstance =
                    bootstrap.Modal.getInstance(modalEl) ||
                    new bootstrap.Modal(modalEl);
                modalInstance.hide();
            }
        },
    });
});

//
$(document).on("click", "button[data-ajax='delete']", function (e) {
    e.preventDefault();

    $btn = $(this);
    let url = $btn.data("url");

    ajaxRequest({
        url,
        method: "DELETE",
        confirm: {
            title: "Apakah anda yakin ingin menghapus data ini?",
            text: "Data yang sudah anda hapus tidak dapat dikembalikan lagi.",
        },
    });
});

//
const $password = $("#password");
const $passwordConfirmation = $("#password_confirmation");
$passwordConfirmation.prop("disabled", !$password.val());
$password.on("input", function () {
    if ($(this).val().length === 0) {
        $passwordConfirmation.prop("disabled", true).val("");
    } else {
        $passwordConfirmation.prop("disabled", false);
    }
});
