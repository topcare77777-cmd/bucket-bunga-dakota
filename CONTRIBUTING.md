# Panduan Kontribusi (Contributing Guidelines)

Terima kasih telah berpartisipasi dalam pengembangan Digital Promo Campaign Manager!

## Standar Kode (Coding Standards)
1. **Tidak Ada Placeholder:** Semua data tiruan harus menggunakan konteks nyata (misal: "Parfum Musim Panas" bukan "Test Produk 1").
2. **Kualitas Produksi:** Semua kode yang di-*commit* harus siap produksi (*production-ready*).
3. **Batas Ukuran File:** Maksimal 500 baris kode per file. Jika mencapai 450 baris, lakukan refaktorisasi dengan memecahnya ke dalam modul/layanan yang lebih kecil.
4. **Linting & Typing:** Seluruh kode TypeScript harus melewati aturan `ESLint` yang ketat, tanpa peringatan (warnings), dan tidak menggunakan `any`.

## Proses Pull Request (PR)
1. Buat cabang baru dari `main` dengan format `fitur/nama-fitur` atau `bugfix/nama-bug`.
2. Lakukan *commit* dengan pesan deskriptif. (Misal: `feat: implementasi fungsi anotasi video berbasis koordinat`).
3. Dorong (*push*) cabang Anda dan buka Pull Request.
4. PR harus diaudit dan disetujui minimal oleh satu pengembang senior sebelum digabungkan.

## Laporan Bug (Bug Reports)
Pastikan melampirkan log error, tangkapan layar, dan langkah mereproduksi saat membuka isu baru.