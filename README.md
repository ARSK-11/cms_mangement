# CMS Management - Node.js Backend Controller

Sistem manajemen CMS berbasis PHP untuk mengontrol backend Node.js dengan interface web yang user-friendly.

## 🚀 Fitur Utama

- **Dashboard Real-time**: Monitor status backend secara real-time
- **Kontrol Backend**: Start, stop, dan restart backend Node.js
- **Process Management**: Lihat dan kelola proses backend yang aktif
- **Log Monitoring**: Monitor log backend dan error logs
- **System Information**: Informasi sistem dan resource usage
- **Configuration Management**: Pengaturan konfigurasi backend
- **Responsive Design**: Interface yang responsif untuk desktop dan mobile

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- Node.js terinstall di sistem
- Web server (Apache/Nginx) atau PHP built-in server
- Akses command line untuk eksekusi shell commands

## 🛠️ Instalasi

1. **Clone atau download project ini**
   ```bash
   git clone <repository-url>
   cd cms_mangement
   ```

2. **Konfigurasi awal**
   - Edit file `includes/config.php`
   - Sesuaikan path backend Node.js Anda
   - Set port yang digunakan backend

3. **Jalankan web server**
   ```bash
   # Menggunakan PHP built-in server
   php -S localhost:8080
   
   # Atau menggunakan Apache/Nginx
   # Pastikan folder project ada di web root
   ```

4. **Akses CMS**
   - Buka browser dan akses `http://localhost:8080`
   - CMS siap digunakan

## ⚙️ Konfigurasi

### File Konfigurasi: `includes/config.php`

```php
// Path ke folder backend Node.js
define('BACKEND_PATH', 'C:/Users/Aris/Documents/GitHub/backend_user');

// Port yang digunakan backend
define('BACKEND_PORT', 3000);

// File utama backend
define('BACKEND_SCRIPT', 'index.js');
```

### Pengaturan yang Dapat Dikonfigurasi

- **BACKEND_PATH**: Path lengkap ke folder backend Node.js
- **BACKEND_PORT**: Port yang digunakan oleh backend
- **BACKEND_SCRIPT**: File utama backend (index.js, app.js, dll)
- **COMMAND_TIMEOUT**: Timeout untuk eksekusi command (detik)
- **REFRESH_INTERVAL**: Interval auto-refresh dashboard (milidetik)

## 📖 Cara Penggunaan

### Dashboard
- **Status Monitoring**: Lihat status backend (Running/Stopped)
- **Process List**: Daftar proses backend yang aktif
- **Port Status**: Status port backend
- **Quick Actions**: Tombol untuk start, stop, restart backend

### Logs
- **Backend Logs**: Log aplikasi backend
- **Error Logs**: Log error dan warning
- **Auto-refresh**: Refresh otomatis log
- **Log Filtering**: Filter berdasarkan jumlah baris

### Settings
- **Configuration**: Update konfigurasi backend
- **System Info**: Informasi sistem dan resource
- **File Permissions**: Cek permission file dan folder
- **Quick Actions**: Akses cepat ke fungsi utama

## 🔧 API Endpoints

### Backend Control API: `api/backend-control.php`

#### Actions yang Tersedia:

1. **Start Backend**
   ```
   POST /api/backend-control.php
   action=start
   ```

2. **Stop Backend**
   ```
   POST /api/backend-control.php
   action=stop
   ```

3. **Restart Backend**
   ```
   POST /api/backend-control.php
   action=restart
   ```

4. **Check Status**
   ```
   GET /api/backend-control.php?action=status
   ```

5. **Kill Process**
   ```
   POST /api/backend-control.php
   action=kill_process&pid=<process_id>
   ```

6. **Get Logs**
   ```
   GET /api/backend-control.php?action=logs&lines=100
   ```

7. **System Info**
   ```
   GET /api/backend-control.php?action=system_info
   ```

8. **Clean Logs**
   ```
   POST /api/backend-control.php
   action=clean_logs&days=7
   ```

## 📁 Struktur File

```
cms_mangement/
├── index.php                 # Dashboard utama
├── logs.php                  # Halaman log
├── settings.php              # Halaman pengaturan
├── README.md                 # Dokumentasi
├── includes/
│   ├── config.php           # Konfigurasi sistem
│   └── functions.php        # Fungsi-fungsi utama
├── api/
│   └── backend-control.php  # API endpoint
├── assets/
│   ├── css/
│   │   └── style.css        # Styling CSS
│   └── js/
│       └── script.js        # JavaScript functions
└── logs/                    # Direktori log (auto-created)
    ├── backend.log          # Log backend
    └── error.log            # Log error
```

## 🔒 Keamanan

### Rekomendasi Keamanan:

1. **Restrict Access**: Batasi akses ke CMS hanya dari IP tertentu
2. **Authentication**: Tambahkan sistem login jika diperlukan
3. **HTTPS**: Gunakan HTTPS untuk koneksi aman
4. **File Permissions**: Set permission file yang tepat
5. **Log Rotation**: Aktifkan rotasi log untuk mencegah file log terlalu besar

### Contoh .htaccess untuk Apache:
```apache
# Restrict access to specific IPs
Order Deny,Allow
Deny from all
Allow from 127.0.0.1
Allow from ::1
Allow from 192.168.1.0/24

# Protect config files
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>
```

## 🐛 Troubleshooting

### Masalah Umum:

1. **Backend tidak bisa di-start**
   - Cek path backend di konfigurasi
   - Pastikan Node.js terinstall
   - Cek permission folder backend

2. **Port sudah digunakan**
   - Cek apakah ada proses lain yang menggunakan port yang sama
   - Ganti port di konfigurasi backend

3. **Permission denied**
   - Cek permission file dan folder
   - Pastikan web server memiliki akses ke command line

4. **Log tidak muncul**
   - Cek permission direktori logs
   - Pastikan backend menulis ke file log yang benar

### Debug Mode:

Aktifkan error reporting untuk debugging:
```php
// Tambahkan di awal file PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## 📝 Changelog

### Version 1.0.0
- Initial release
- Dashboard dengan monitoring real-time
- Kontrol backend (start/stop/restart)
- Log monitoring
- System information
- Configuration management

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 Lisensi

Project ini dilisensikan di bawah MIT License - lihat file [LICENSE](LICENSE) untuk detail.

## 📞 Support

Jika Anda mengalami masalah atau memiliki pertanyaan:

1. Cek dokumentasi ini
2. Lihat bagian troubleshooting
3. Buat issue di repository
4. Hubungi developer

---

**Dibuat dengan ❤️ untuk memudahkan manajemen backend Node.js**
