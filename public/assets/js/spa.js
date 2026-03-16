const layout = {
    content: "#page-content",
    pageTitle: "#page-title",
    breadcrumb: "#breadcrumb-container",
    sidebarParent: ".slide",
    sidebarLink: ".side-menu__item",
    modalContainer: "#modal-container",
};

function ajaxRequest({
    url,
    method = "POST",
    data = {},
    isFormData = false,
    confirm = {},
    onSuccess,
    onError,
    onComplete,
}) {
    const csrfToken = $('meta[name="csrf-token"]').attr("content");

    const showError = (xhr) => {
        const { status, responseJSON } = xhr;

        if (status === 422 && responseJSON?.errors) {
            const errors = Object.values(responseJSON.errors)
                .flat()
                .join("<br>");
            return showToast(
                "error",
                errors || "Data yang dimasukkan tidak valid",
                5000
            );
        }

        if ([401, 419].includes(status)) {
            showToast("warning", "Silahkan login untuk melanjutkan.", 5000);
            loadPage(window.routes.login);
            history.pushState(null, null, window.routes.login);
            return;
        }

        showToast(
            "error",
            responseJSON?.message || "Terjadi kesalahan, coba lagi.",
            5000
        );
    };

    const handleRedirect = (res) => {
        const { redirect_type, redirect } = res;
        if (!redirect_type) return;
        if (redirect_type !== "reload" && !redirect) return;

        const redirectActions = {
            reload: () => location.reload(),
            spa: () => {
                loadPage(redirect);
                history.pushState(null, null, redirect);
            },
            http: () => (window.location.href = redirect),
        };

        (redirectActions[redirect_type] || redirectActions.http)();
    };

    const runAjax = () => {
        $.ajax({
            url,
            type: method,
            data,
            dataType: "json",
            processData: !isFormData,
            contentType: isFormData
                ? false
                : "application/x-www-form-urlencoded; charset=UTF-8",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "X-Requested-With": "XMLHttpRequest",
            },
            success: (res) => {
                if (typeof onSuccess === "function") {
                    onSuccess(res);
                    return;
                }

                const isSuccess = res.status === "success";

                if (res.message) {
                    showToast(res.status, res.message, isSuccess ? 3000 : 5000);
                }

                if (isSuccess) {
                    setTimeout(() => handleRedirect(res), 300);
                    return;
                }

                handleRedirect(res);
            },
            error: (xhr, status, error) => {
                if (typeof onError === "function") {
                    return onError(xhr, status, error);
                }
                showError(xhr);
            },
            complete: () => {
                if (typeof onComplete === "function") {
                    onComplete();
                } else {
                    console.log("AJAX request completed");
                }
            },
        });
    };

    if (method == "DELETE") {
        Swal.fire({
            title: confirm.title || "Apakah Anda yakin?",
            text: confirm.text || "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: confirm.confirmText || "Ya",
            cancelButtonText: confirm.cancelText || "Batal",
        }).then((result) => {
            if (result.isConfirmed) runAjax();
        });
    } else {
        runAjax();
    }
}

// Active sidebar link handler
function setSidebarLink(url) {
    $(layout.sidebarLink).removeClass("active");
    $(layout.sidebarParent).removeClass("open active");

    $(layout.sidebarLink).each(function () {
        const href = $(this).attr("href");
        if (!href || href === "#") return;

        if (url.startsWith(href)) {
            $(this).addClass("active");

            $(this)
                .parents(layout.sidebarParent)
                .each(function () {
                    if ($(this).attr("data-settled") === "true") {
                        $(this).addClass("open active");
                    }
                });
        }
    });
}

// SPA load helper
function loadPage(url) {
    loader(true);

    ajaxRequest({
        url: url,
        method: "GET",
        onSuccess: (res) => {
            // Remove partial tags
            $('style[data-partial="1"], script[data-partial="1"]').remove();

            // Load styles
            if (res.styles) {
                $("head").append($(res.styles).attr("data-partial", "1"));
            }

            // Load breadcrumb
            if (res.breadcrumb) {
                $(layout.breadcrumb).html(res.breadcrumb);
            }

            // Load content
            $(layout.content).html(res.content || "");

            // Load modal
            if (res.modal) {
                $(layout.modalContainer).html(res.modal);
            }

            // Load script
            if (res.scripts) {
                $("body").append($(res.scripts).attr("data-partial", "1"));
            }

            // Replace title
            if (res.title) {
                document.title = res.title + " - " + window.appName;
                $(layout.pageTitle).text(res.title);
            }

            // Set sidebar link
            setSidebarLink(url);
        },
        onError: (xhr) => {
            if (
                xhr.status === 302 ||
                xhr.status === 419 ||
                xhr.status === 401
            ) {
                window.location.href = url;
                return;
            }

            $(layout.content).html(
                '<h4 class="text-danger">Gagal memuat halaman.</h4>'
            );
        },
        onComplete: () => {
            loader(false);
        },
    });
}

// SPA click handler
$(document).on("click", ".spa-link", function (e) {
    const url = $(this).attr("href");
    if (!url || url === "#") return;

    e.preventDefault();
    loadPage(url);
    history.pushState(null, null, url);
});

// Browser back/forward
window.onpopstate = function () {
    loadPage(location.href);
};
