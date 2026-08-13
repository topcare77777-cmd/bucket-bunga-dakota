<?php declare(strict_types=1); ?>
<div class="product-create-container">
    <h2>Tambah Produk Baru</h2>
    <form action="/admin/products/store" method="POST" enctype="multipart/form-data" class="admin-form">
        <div class="form-group">
            <label for="name">Nama Produk</label>
            <input type="text" id="name" name="name" required class="form-control">
        </div>

        <div class="form-group">
            <label for="price">Harga (Rp)</label>
            <input type="number" id="price" name="price" step="0.01" required class="form-control">
        </div>

        <div class="form-group">
            <label for="stock">Stok</label>
            <input type="number" id="stock" name="stock" value="0" required class="form-control">
        </div>

        <div class="form-group">
            <label for="description">Deskripsi Produk</label>
            <textarea id="description" name="description" rows="4" class="form-control"></textarea>
        </div>

        <div class="form-group">
            <label for="image">Gambar Produk (JPG, PNG, WEBP)</label>
            <input type="file" id="image" name="image" accept="image/*" class="form-control">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
            <a href="/admin/products" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>