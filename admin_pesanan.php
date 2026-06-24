<?php
// Query untuk mengambil SEMUA pesanan dari tabel orders
$query = "SELECT orders.*, books.judul, users.username 
          FROM orders 
          JOIN books ON orders.book_id = books.id 
          JOIN users ON orders.user_id = users.id 
          ORDER BY orders.id DESC";

$result = mysqli_query($conn, $query);
?>

<div style="padding: 20px;">
    <div style="margin-bottom: 20px;">
        <h2 style="color: white; margin: 0; font-weight: 600;">Daftar Pesanan Masuk</h2>
        <p style="color: #94a3b8; font-size: 0.9rem; margin-top: 5px;">Pantau dan kelola seluruh transaksi pembelian buku dari pelanggan.</p>
    </div>
    
    <div style="width: 100%; overflow-x: auto; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);">
        <table style="width: 100%; border-collapse: collapse; background: #1e293b; color: white; border-radius: 10px; overflow: hidden; min-width: 800px;">
            <thead>
                <tr style="background: #334155; text-align: left;">
                    <th style="padding: 15px; font-size: 0.9rem; color: #cbd5e1;">ID Pesanan</th>
                    <th style="padding: 15px; font-size: 0.9rem; color: #cbd5e1;">Nama Pelanggan</th>
                    <th style="padding: 15px; font-size: 0.9rem; color: #cbd5e1;">Judul Buku</th>
                    <th style="padding: 15px; font-size: 0.9rem; color: #cbd5e1;">Total Harga</th>
                    <th style="padding: 15px; font-size: 0.9rem; color: #cbd5e1;">Metode</th>
                    <th style="padding: 15px; font-size: 0.9rem; color: #cbd5e1; text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr style="border-bottom: 1px solid #334155; background: #1e293b;">
                        <td style="padding: 15px; font-size: 0.9rem;">#ORD-<?= $row['id'] ?></td>
                        <td style="padding: 15px; font-size: 0.9rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?= htmlspecialchars($row['username']) ?></td>
                        <td style="padding: 15px; font-size: 0.9rem; color: #cbd5e1;"><?= htmlspecialchars($row['judul']) ?></td>
                        <td style="padding: 15px; font-size: 0.9rem; color: #10b981; font-weight: bold;">
                            Rp <?= number_format($row['total_harga'], 0, ',', '.') ?>
                        </td>
                        <td style="padding: 15px; font-size: 0.9rem;"><?= $row['metode_pembayaran'] ?></td>
                        <td style="padding: 15px; text-align: center;">
                            <span style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 5px 12px; border-radius: 5px; font-size: 0.8rem; font-weight: 500;">
                                Lunas
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding: 40px; text-align: center; color: #64748b; background: #1e293b;">
                            Belum ada pesanan masuk.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>