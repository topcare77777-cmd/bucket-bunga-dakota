Buku panduan ini berisi spesifikasi lengkap perangkat lunak (Software Requirement Specification - SRS), arsitektur, dan pedoman desain untuk sistem Manajemen Kampanye Promosi Digital.

## 1. Pendahuluan
Sistem Manajemen Kampanye Promosi Digital dirancang untuk memfasilitasi perancangan, manajemen aset, kolaborasi materi pemasaran digital, serta manajemen katalog produk secara terpusat.

## 2. Business Requirement
*   **Efisiensi Produksi Konten:** Mempercepat siklus pembuatan dan persetujuan iklan media sosial.
*   **Sentralisasi Aset & Produk:** Repositori aman untuk aset kreatif dan master data produk (SKU, varian).
*   **Kolaborasi Real-time:** Memfasilitasi komunikasi pengarah kreatif dan desainer dengan anotasi visual.

## 3. Functional Requirement
*   **Manajemen Kampanye:** Membuat, mengedit, menjadwalkan, mengarsipkan proyek promosi.
*   **Manajemen Aset (DAM):** Pengunggahan multi-format, transcoding video, manajemen versi.
*   **Alur Persetujuan:** Anotasi visual berbasis frame video atau koordinat gambar.
*   **Admin Panel (CMS):** Manajemen inventaris produk, taksonomi kategori, dan galeri pameran.
*   **Manajemen Akses (RBAC):** Otentikasi berbasis JWT dengan peran Super Admin, Creative Director, Designer, dan Reviewer.

## 4. Struktur Database Inti
Sistem menggunakan PostgreSQL dengan entitas utama:
*   `users`, `roles`, `audit_logs` (Keamanan & Akses)
*   `campaigns`, `assets`, `asset_reviews` (Manajemen Kampanye & Media)
*   `products`, `product_variants`, `categories` (Katalog)
*   `galleries`, `system_settings` (CMS & Konfigurasi)

## 5. UI Design System
*   **Tipografi:** Display Sans-Serif untuk judul (Heading), Inter/Geist Sans untuk antarmuka.
*   **Warna Utama:** Onyx Black (#121212) dan Pure White (#FFFFFF).
*   **Aksen:** Champagne Gold (#D4AF37), Emerald Green (Sukses), Amber (Menunggu), Rose Red (Error).
*   **Tata Letak:** 8-Point Grid System, Responsive Mobile-First, Dashboard Non-Scrolling (Desktop).