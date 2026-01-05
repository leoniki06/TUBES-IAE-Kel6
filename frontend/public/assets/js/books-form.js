(() => {
    const qs = (sel, root = document) => root.querySelector(sel);

    function bindStockValidation(form, totalSel, availSel, errSel) {
        if (!form) return;

        const totalEl = qs(totalSel, form);
        const availEl = qs(availSel, form);
        const errEl = qs(errSel, form);
        const submitBtn = qs(".js-submit", form);

        if (!totalEl || !availEl) return;

        function setError(msg) {
            if (errEl) errEl.textContent = msg || "";
            if (submitBtn) submitBtn.disabled = !!msg;
        }

        function readInt(el) {
            const v = parseInt(el.value || "0", 10);
            return Number.isFinite(v) ? v : 0;
        }

        function validate() {
            const total = Math.max(0, readInt(totalEl));
            const avail = Math.max(0, readInt(availEl));

            // normalize displayed values only if negative typed
            if (readInt(totalEl) < 0) totalEl.value = "0";
            if (readInt(availEl) < 0) availEl.value = "0";

            if (avail > total) {
                setError("Stock Available tidak boleh lebih besar dari Stock Total.");
                return false;
            }

            setError("");
            return true;
        }

        totalEl.addEventListener("input", validate);
        availEl.addEventListener("input", validate);

        form.addEventListener("submit", (e) => {
            if (!validate()) {
                e.preventDefault();
                availEl.focus();
            }
        });

        validate();
    }

    bindStockValidation(
        qs("#addForm"),
        'input[name="stock_total"]',
        'input[name="stock_available"]',
        '[data-err="add_stock"]'
    );

    bindStockValidation(
        qs("#editForm"),
        "#e_stock_total",
        "#e_stock_available",
        '[data-err="edit_stock"]'
    );
})();
