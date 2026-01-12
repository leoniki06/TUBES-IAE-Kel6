(() => {
    const qs = (sel, root = document) => root.querySelector(sel);
    const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    // --------------------
    // MODAL HELPERS (class .open)
    // --------------------
    function openModal(id) {
        const el = document.getElementById(id);
        if (!el) return;

        // tutup modal lain jika ada
        qsa(".m-overlay.open").forEach((m) => {
            m.classList.remove("open");
            m.style.display = "none";
        });

        el.classList.add("open");
        el.style.display = "block"; // aman karena CSS pakai !important -> jadi flex
        document.documentElement.classList.add("modal-open");
        document.body.classList.add("modal-open");
    }

    function closeModal(el) {
        if (!el) return;
        el.classList.remove("open");
        el.style.display = "none";

        const anyOpen = qsa(".m-overlay.open").length > 0;
        if (!anyOpen) {
            document.documentElement.classList.remove("modal-open");
            document.body.classList.remove("modal-open");
        }
    }

    // open buttons
    qsa("[data-open]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = btn.getAttribute("data-open");
            if (id) openModal(id);
        });
    });

    // close buttons + click outside
    qsa(".m-overlay").forEach((overlay) => {
        overlay.addEventListener("click", (e) => {
            if (e.target === overlay) closeModal(overlay);
        });

        qsa("[data-close]", overlay).forEach((btn) => {
            btn.addEventListener("click", () => closeModal(overlay));
        });
    });

    // ESC close
    document.addEventListener("keydown", (e) => {
        if (e.key !== "Escape") return;
        const opened = qs(".m-overlay.open");
        if (opened) closeModal(opened);
    });

    // auto open add modal on validation error
    if (window.BOOKS_PAGE && window.BOOKS_PAGE.openAddOnError) {
        openModal("modal-add");
    }

    // --------------------
    // EDIT FORM FILL + COVER PREVIEW
    // --------------------
    const editForm = qs("#editForm");
    if (!editForm) return;

    const e_isbn = qs("#e_isbn");
    const e_year = qs("#e_year");
    const e_title = qs("#e_title");
    const e_author = qs("#e_author");
    const e_publisher = qs("#e_publisher");
    const e_genre = qs("#e_genre");
    const e_stock_total = qs("#e_stock_total");
    const e_stock_available = qs("#e_stock_available");

    const e_cover_preview = qs("#e_cover_preview");
    const e_cover_meta = qs("#e_cover_meta");
    const e_cover = qs("#e_cover");
    const e_remove_cover = qs("#e_remove_cover");

    let lastExistingCoverUrl = "";

    function setExistingCover(url) {
        lastExistingCoverUrl = url ? String(url) : "";

        if (!e_cover_preview || !e_cover_meta) return;

        if (lastExistingCoverUrl.trim() !== "") {
            e_cover_preview.src = lastExistingCoverUrl;
            e_cover_preview.style.display = "block";
            e_cover_meta.textContent = "Cover saat ini";
        } else {
            e_cover_preview.removeAttribute("src");
            e_cover_preview.style.display = "none";
            e_cover_meta.textContent = "Tidak ada cover";
        }
    }

    // tombol edit
    qsa('[data-open="modal-edit"]').forEach((btn) => {
        btn.addEventListener("click", () => {
            const id = btn.dataset.id || "";

            // set action form update
            const base =
                window.BOOKS_PAGE && window.BOOKS_PAGE.baseBooksUrl
                    ? window.BOOKS_PAGE.baseBooksUrl
                    : "/librarian/books";
            editForm.action = `${base}/${id}/update`;

            // fill fields
            if (e_isbn) e_isbn.value = btn.dataset.isbn || "";
            if (e_year) e_year.value = btn.dataset.year || "";
            if (e_title) e_title.value = btn.dataset.title || "";
            if (e_author) e_author.value = btn.dataset.author || "";
            if (e_publisher) e_publisher.value = btn.dataset.publisher || "";
            if (e_genre) e_genre.value = btn.dataset.genre || "";
            if (e_stock_total) e_stock_total.value = btn.dataset.stock_total || "0";
            if (e_stock_available) e_stock_available.value = btn.dataset.stock_available || "0";

            // reset cover controls
            if (e_cover) e_cover.value = "";
            if (e_remove_cover) e_remove_cover.checked = false;

            // existing cover
            const coverUrl = btn.dataset.cover_url || "";
            setExistingCover(coverUrl);

            // open modal
            openModal("modal-edit");
        });
    });

    // preview cover baru saat pilih file
    if (e_cover) {
        e_cover.addEventListener("change", () => {
            const file = e_cover.files && e_cover.files[0];
            if (!file) return;

            if (e_remove_cover) e_remove_cover.checked = false;

            const url = URL.createObjectURL(file);
            if (e_cover_preview) {
                e_cover_preview.src = url;
                e_cover_preview.style.display = "block";
            }
            if (e_cover_meta) e_cover_meta.textContent = `Cover baru: ${file.name}`;
        });
    }

    // remove cover toggle
    if (e_remove_cover) {
        e_remove_cover.addEventListener("change", () => {
            const checked = e_remove_cover.checked;

            if (checked) {
                if (e_cover) e_cover.value = "";
                if (e_cover_preview) {
                    e_cover_preview.removeAttribute("src");
                    e_cover_preview.style.display = "none";
                }
                if (e_cover_meta) e_cover_meta.textContent = "Cover akan dihapus";
            } else {
                // restore existing cover
                if (lastExistingCoverUrl.trim() !== "") {
                    if (e_cover_preview) {
                        e_cover_preview.src = lastExistingCoverUrl;
                        e_cover_preview.style.display = "block";
                    }
                    if (e_cover_meta) e_cover_meta.textContent = "Cover saat ini";
                } else {
                    if (e_cover_preview) {
                        e_cover_preview.removeAttribute("src");
                        e_cover_preview.style.display = "none";
                    }
                    if (e_cover_meta) e_cover_meta.textContent = "Tidak ada cover";
                }
            }
        });
    }
})();
