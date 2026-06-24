// Fungsi untuk membuka modal dan mengisi datanya secara otomatis
function openBookDetail(title, author, desc, img, price) {
    const modal = document.getElementById('bookModal');
    
    // Masukkan data dari PHP ke elemen HTML di dalam Modal
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalAuthor').innerText = "Penulis: " + author;
    document.getElementById('modalDesc').innerText = desc;
    document.getElementById('modalImg').src = img;
    document.getElementById('modalPrice').innerText = price;

    // Tampilkan modalnya
    modal.style.display = "block";
}

// Fungsi untuk menutup modal saat tombol (x) diklik
const closeBtn = document.querySelector('.close-modal');
if (closeBtn) {
    closeBtn.onclick = function() {
        document.getElementById('bookModal').style.display = "none";
    }
}

// Fungsi untuk menutup modal jika user klik di luar kotak modal (area gelap)
window.onclick = function(event) {
    const modal = document.getElementById('bookModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

