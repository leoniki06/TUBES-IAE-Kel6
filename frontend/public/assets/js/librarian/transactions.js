(() => {
    // ===== Flash close =====
    document.querySelectorAll(".js-flash .alert-close").forEach((btn) => {
        btn.addEventListener("click", () => {
            const wrap = btn.closest(".js-flash");
            if (!wrap) return;
            wrap.classList.add("is-hiding");
            setTimeout(() => wrap.remove(), 220);
        });
    });

    const table = document.getElementById("txTable");
    if (!table) return;

    const returnBase = table.getAttribute("data-return-base") || "";
    const isDummy = (table.getAttribute("data-is-dummy") || "0") === "1";

    // ===== Modal =====
    const overlay = document.getElementById("mReturn");
    const form = document.getElementById("mReturnForm");
    const title = document.getElementById("mReturnTitle");
    const desc = document.getElementById("mReturnDesc");

    function buildReturnAction(id) {
        const base = (returnBase || "").replace(/\/$/, "");
        // ✅ kalau dummy mode, WAJIB ikut query dummy1
        return isDummy ? `${base}/${id}/return?dummy1` : `${base}/${id}/return`;
    }

    function openReturnModal(id, member, book) {
        if (!overlay || !form) return;

        if (title) title.textContent = `Konfirmasi pengembalian untuk #${id}`;
        if (desc) desc.textContent = `Member: ${member} • Buku: ${book}`;

        form.action = buildReturnAction(id);

        overlay.classList.add("is-open");
        overlay.setAttribute("aria-hidden", "false");
        document.documentElement.classList.add("has-modal");
    }

    function closeModal() {
        if (!overlay) return;
        overlay.classList.remove("is-open");
        overlay.setAttribute("aria-hidden", "true");
        document.documentElement.classList.remove("has-modal");
    }

    document.querySelectorAll('[data-close="mReturn"]').forEach((b) => {
        b.addEventListener("click", closeModal);
    });

    overlay?.addEventListener("click", (e) => {
        if (e.target === overlay) closeModal();
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") closeModal();
    });

    // bind buttons
    document.querySelectorAll(".js-return-btn").forEach((btn) => {
        btn.addEventListener("click", (e) => {
            e.preventDefault();
            e.stopPropagation();

            const id = btn.getAttribute("data-id");
            const member = btn.getAttribute("data-member") || "—";
            const book = btn.getAttribute("data-book") || "—";
            if (!id) return;

            openReturnModal(id, member, book);
        });
    });

    // ===== Hover Popover =====
    const pop = document.getElementById("txPop");
    const popTitle = document.getElementById("txPopTitle");
    const elMember = document.getElementById("txPopMember");
    const elBook = document.getElementById("txPopBook");
    const elBorrowed = document.getElementById("txPopBorrowed");
    const elDue = document.getElementById("txPopDue");
    const elReturned = document.getElementById("txPopReturned");
    const elStatus = document.getElementById("txPopStatus");
    const elFine = document.getElementById("txPopFine");

    function showPop(tr) {
        if (!pop) return;

        const id = tr.dataset.id || "";
        const member = `${tr.dataset.member || "—"} (${tr.dataset.email || "—"})`;
        const book = tr.dataset.book || "—";

        if (popTitle) popTitle.textContent = `Transaction #${id}`;
        if (elMember) elMember.textContent = member;
        if (elBook) elBook.textContent = book;
        if (elBorrowed) elBorrowed.textContent = tr.dataset.borrowed || "—";
        if (elDue) elDue.textContent = tr.dataset.due || "—";
        if (elReturned) elReturned.textContent = tr.dataset.returned || "—";
        if (elStatus) elStatus.textContent = tr.dataset.status || "—";
        if (elFine) elFine.textContent = tr.dataset.fine || "—";

        const r = tr.getBoundingClientRect();
        const pad = 10;

        let top = window.scrollY + r.top - pop.offsetHeight - 8;
        let left = window.scrollX + r.left + pad;

        if (top < window.scrollY + 8) top = window.scrollY + r.bottom + 8;

        const maxLeft =
            window.scrollX + document.documentElement.clientWidth - pop.offsetWidth - 8;
        if (left > maxLeft) left = maxLeft;

        pop.style.top = `${top}px`;
        pop.style.left = `${left}px`;

        pop.classList.add("is-on");
        pop.setAttribute("aria-hidden", "false");
    }

    function hidePop() {
        if (!pop) return;
        pop.classList.remove("is-on");
        pop.setAttribute("aria-hidden", "true");
    }

    document.querySelectorAll("#txTable .tx-row").forEach((tr) => {
        tr.addEventListener("mouseenter", () => showPop(tr));
        tr.addEventListener("mouseleave", hidePop);
    });

    window.addEventListener("scroll", hidePop, { passive: true });
})();
