(() => {
    const qs = (sel, root = document) => root.querySelector(sel);
    const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    // ===== MODAL =====
    function openModal(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.add("is-open");
        document.documentElement.classList.add("modal-open");
    }

    function closeModal(el) {
        if (!el) return;
        el.classList.remove("is-open");
        document.documentElement.classList.remove("modal-open");
    }

    // open modal
    qsa("[data-open]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const target = btn.getAttribute("data-open");
            if (!target) return;

            if (target === "modal-edit") {
                // fill edit form
                const id = btn.dataset.id;
                const editForm = qs("#editForm");
                if (editForm && id) {
                    editForm.setAttribute("action", `${window.BOOKS_PAGE?.baseBooksUrl}/${id}/update`);
                }

                const setVal = (idInput, v) => {
                    const el = qs(`#${idInput}`);
                    if (el) el.value = v ?? "";
                };

                setVal("e_isbn", btn.dataset.isbn);
                setVal("e_year", btn.dataset.year);
                setVal("e_title", btn.dataset.title);
                setVal("e_author", btn.dataset.author);
                setVal("e_publisher", btn.dataset.publisher);
                setVal("e_stock_total", btn.dataset.stock_total);
                setVal("e_stock_available", btn.dataset.stock_available);

                const genreSel = qs("#e_genre");
                if (genreSel) genreSel.value = btn.dataset.genre ?? "";
            }

            openModal(target);
        });
    });

    // close buttons
    qsa("[data-close]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const overlay = btn.closest(".m-overlay");
            closeModal(overlay);
        });
    });

    // click overlay to close
    qsa(".m-overlay").forEach((ov) => {
        ov.addEventListener("click", (e) => {
            if (e.target === ov) closeModal(ov);
        });
    });

    // ESC close
    document.addEventListener("keydown", (e) => {
        if (e.key !== "Escape") return;
        const opened = qs(".m-overlay.is-open");
        if (opened) closeModal(opened);
    });

    // open add modal if validation error from server
    if (window.BOOKS_PAGE?.openAddOnError) {
        openModal("modal-add");
    }

    // ===== TOAST =====
    function removeToast(t) {
        if (!t) return;
        t.classList.add("is-leave");
        setTimeout(() => t.remove(), 180);
    }

    qsa(".js-toast").forEach((t) => {
        const ms = parseInt(t.getAttribute("data-autohide") || "0", 10);
        const closeBtn = qs("[data-toast-close]", t);

        if (closeBtn) {
            closeBtn.addEventListener("click", () => removeToast(t));
        }

        if (ms > 0) {
            setTimeout(() => removeToast(t), ms);
        }
    });
})();
