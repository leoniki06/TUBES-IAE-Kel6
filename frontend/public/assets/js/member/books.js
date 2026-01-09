document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnPinjam');

    if (btn) {
        btn.addEventListener('click', function () {
            const bookId = this.dataset.id;

            if (!confirm('Yakin ingin meminjam buku ini?')) return;

            fetch('/member/transactions/borrow', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ book_id: bookId })
            })
            .then(res => res.json())
            .then(data => {
                alert(data.message || 'Berhasil meminjam buku');
                location.reload();
            })
            .catch(err => {
                alert('Gagal meminjam buku');
            });
        });
    }
});
