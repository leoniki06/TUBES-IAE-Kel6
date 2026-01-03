(function () {
    const form = document.getElementById("bookForm");
    if (!form) return;

    const setErr = (key, msg) => {
        const el = form.querySelector(`[data-err="${key}"]`);
        if (el) el.textContent = msg || "";
    };

    const clearAll = () => {
        ["title", "author", "category", "stock", "isbn", "year"].forEach(k => setErr(k, ""));
    };

    form.addEventListener("submit", function (e) {
        clearAll();

        const title = form.title.value.trim();
        const author = form.author.value.trim();
        const category = form.category.value.trim();
        const stock = Number(form.stock.value || 0);
        const isbn = form.isbn.value.trim();
        const year = form.year.value ? Number(form.year.value) : 0;

        let ok = true;

        if (!title) { setErr("title", "Title wajib diisi"); ok = false; }
        if (!author) { setErr("author", "Author wajib diisi"); ok = false; }
        if (!category) { setErr("category", "Category wajib diisi"); ok = false; }

        if (Number.isNaN(stock) || stock < 0) { setErr("stock", "Stock tidak boleh negatif"); ok = false; }
        if (form.year.value && (Number.isNaN(year) || year < 0)) { setErr("year", "Year tidak valid"); ok = false; }

        if (isbn && isbn.length < 10) { setErr("isbn", "ISBN minimal 10 karakter"); ok = false; }

        if (!ok) e.preventDefault();
    });
})();
