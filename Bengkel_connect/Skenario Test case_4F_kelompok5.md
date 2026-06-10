# TEST CASE NEGATIVE

| ID TEST | KATEGORI | SKENARIO PENGUJIAN | TEST DATA | EXPECTED RESULT | ACTUAL RESULT | STATUS | Bukti (Screenshot) |
|---------|----------|--------------------|-----------|-----------------|---------------|--------|-------------------|
| TC-N-01 | NEGATIVE | Login dengan usr password salah | User: admin@gmail.com Pw: 123 / User Mekanik: AgusSaputra@gmail.com Pw: mekanik123 / User: Irmamainawaroh@gmail.com Pw: Mainawaroh05 | Login gagal, pesan error muncul | Login Gagal dan menampilkan pesan username tidak ditemukan | PAS | |
| TC-N-02 | NEGATIVE | Login dengan field kosong | User: - / Pw: - | Validasi gagal, pesan error muncul | Login gagal dan menampilkan pesan harus mengisi kolom tsb | PAS | |
| TC-N-03 | NEGATIVE | Item layanan terlewati | Ketika melakukan konfirmasi booking, item pilih layanan terlewati | Konfirmasi gagal, pesan error muncul | Konfirmasi gagal, menampilkan "Harus pilih salah satu item daftar" | PAS | |
| TC-N-04 | NEGATIVE | Tidak mengupload bukti pembayaran | Setelah melakukan transaksi tidak mengupload bukti pembayaran | Simpan data gagal, pesan error muncul | Konfirmasi gagal, menampilkan "Pilih atau upload di bagian bukti pembayaran" | PAS | |
| TC-N-05 | NEGATIVE | Tidak memilih item mekanik | Saat admin ingin mengirim pekerjaan ke mekanik | Konfirmasi gagal, pesan error muncul | Konfirmasi gagal, menampilkan pesan error | PAS | |
