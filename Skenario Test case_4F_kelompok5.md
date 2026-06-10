# TEST CASE POSITIF - APLIKASI BENGKEL CONNECT

| ID TEST CASE | KATEGORI   | SKENARIO PENGUJIAN                                  | TEST DATA                                                                 | EXPECTED RESULT                                                                 | ACTUAL RESULT                              | STATUS | BUKTI SCREENSHOT                                                                 |
|--------------|------------|-----------------------------------------------------|---------------------------------------------------------------------------|---------------------------------------------------------------------------------|--------------------------------------------|--------|----------------------------------------------------------------------------------|
| Tc-p-01      | POSITIVE 1 | Login dengan data valid                             | USR: admin, mekanik, pelanggan<br>PW:<br>- admin1234<br>- mekanik1234     | User berhasil masuk ke dashboard                                                | Masuk ke halaman dashboard                 | PAS    | ![img](tmp/2188318481_docxword_media_image1.jpeg)<br>![img](tmp/2188318481_docxword_media_image2.jpeg) |
| Tc-p-02      | POSITIVE 2 | Login dengan data pengguna yang terdaftar           | USR: admin, mekanik, pelanggan<br>PW:<br>- admin1234<br>- mekanik1234     | User berhasil masuk ke dashboard                                                | Masuk ke halaman dashboard                 | PAS    | ![img](tmp/2188318481_docxword_media_image3.jpeg)<br>![img](tmp/2188318481_docxword_media_image1.jpeg) |
| Tc-p-03      | POSITIVE 3 | Pilih layanan kunjungi bengkel dan home service     | USR: pelanggan                                                            | Sistem berhasil menampilkan data formulir untuk melanjutkan booking            | Data muncul di menu kunjungi bengkel dan home service | PAS    | ![img](tmp/2188318481_docxword_media_image4.jpeg)<br>![img](tmp/2188318481_docxword_media_image5.jpeg) |
| Tc-p-04      | POSITIVE 4 | Upload bukti DP dengan format gambar JPG/PNG        | USR: pelanggan                                                            | Sistem berhasil mengirim bukti DP dan menampilkan: "Pembayaran sudah diterima. Data booking akan diproses oleh admin." | Sistem berhasil mengunggah bukti DP, notifikasi berhasil muncul | PAS    | ![img](tmp/2188318481_docxword_media_image6.jpeg)<br>![img](tmp/2188318481_docxword_media_image7.jpeg) |
| Tc-p-05      | POSITIVE 5 | Verifikasi bukti DP yang sudah diterima admin       | USR: Admin                                                                | Admin dapat melihat bukti DP yang diunggah pelanggan dan melakukan verifikasi | Admin berhasil melihat dan memverifikasi bukti DP | PAS    | ![img](tmp/2188318481_docxword_media_image8.jpeg) |

---
*Dokumen ini disusun sebagai acuan pengujian aplikasi Bengkel Connect - Kelompok 5*

---

# TEST CASE NEGATIVE

| ID TEST | KATEGORI | SKENARIO PENGUJIAN | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS | Bukti (Screenshot) |
|---------|----------|--------------------|-----------|-----------------|---------------|--------|-------------------|
| TC-N-01 | NEGATIVE | Login dengan usr password salah | User: admin@gmail.com Pw: 123 / User Mekanik: AgusSaputra@gmail.com Pw: mekanik123 / User: Irmamainawaroh@gmail.com Pw: Mainawaroh05 | Login gagal, pesan error muncul | Login Gagal dan menampilkan pesan username tidak ditemukan | PAS | |
| TC-N-02 | NEGATIVE | Login dengan field kosong | User: - / Pw: - | Validasi gagal, pesan error muncul | Login gagal dan menampilkan pesan harus mengisi kolom tsb | PAS | |
| TC-N-03 | NEGATIVE | Item layanan terlewati | Ketika melakukan konfirmasi booking, item pilih layanan terlewati | Konfirmasi gagal, pesan error muncul | Konfirmasi gagal, menampilkan "Harus pilih salah satu item daftar" | PAS | |
| TC-N-04 | NEGATIVE | Tidak mengupload bukti pembayaran | Setelah melakukan transaksi tidak mengupload bukti pembayaran | Simpan data gagal, pesan error muncul | Konfirmasi gagal, menampilkan "Pilih atau upload di bagian bukti pembayaran" | PAS | |
| TC-N-05 | NEGATIVE | Tidak memilih item mekanik | Saat admin ingin mengirim pekerjaan ke mekanik | Konfirmasi gagal, pesan error muncul | Konfirmasi gagal, menampilkan pesan error | PAS | |
