let page = 1;

let query = '';



document.addEventListener('DOMContentLoaded', () => {

    if (document.getElementById('bookList')) {

        loadBooks();

        document.getElementById('searchInput').addEventListener('input', e => {

            query = e.target.value;

            page = 1;

            loadBooks();

        });

        document.getElementById('prevBtn').onclick = () => {

            if (page > 1) page--;

            loadBooks();

        };

        document.getElementById('nextBtn').onclick = () => {

            page++;

            loadBooks();

        };

    }



    if (typeof BOOK_ID !== 'undefined') {

        loadDetail();

    }

});



/**

 * =====================

 * LIST BOOKS

 * =====================

 */

async function loadBooks() {

    setState('loading');

    try {

        const res = await fetch(`${API_BASE}/books?page=${page}&search=${query}`);

        const json = await res.json();



        if (!json.data || json.data.length === 0) {

            setState('empty');

            return;

        }



        renderBooks(json.data);

        document.getElementById('pageInfo').innerText = `Page ${page}`;

        setState('success');

    } catch (err) {

        setState('error');

    }

}



function renderBooks(books) {

    const el = document.getElementById('bookList');

    el.innerHTML = '';



    books.forEach(b => {

        el.innerHTML += `

        <div class="col-md-3">

            <div class="card h-100">

                <div class="card-body">

                    <h6>${b.title}</h6>

                    <p class="text-muted">${b.author}</p>

                    <a href="/member/books/${b.id}" class="btn btn-sm btn-primary">

                        Detail

                    </a>

                </div>

            </div>

        </div>

        `;

    });

}



/**

 * =====================

 * DETAIL BOOK

 * =====================

 */

async function loadDetail() {

    setState('loading');

    try {

        const res = await fetch(`${API_BASE}/books/${BOOK_ID}`);

        const json = await res.json();



        const b = json.data;

        document.getElementById('title').innerText = b.title;

        document.getElementById('author').innerText = b.author;

        document.getElementById('year').innerText = b.year;

        document.getElementById('stock').innerText = b.stock;

        document.getElementById('description').innerText = b.description;



        document.getElementById('detailCard').classList.remove('d-none');

        setState('success');

    } catch {

        setState('error');

    }

}



/**

 * =====================

 * UI STATE

 * =====================

 */

function setState(type) {

    const el = document.getElementById('state');

    if (!el) return;



    if (type === 'loading') el.innerText = '⏳ Memuat data...';

    if (type === 'empty') el.innerText = '📭 Buku tidak ditemukan.';

    if (type === 'error') el.innerText = '❌ Gagal memuat data.';

    if (type === 'success') el.innerText = '';

}
