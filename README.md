# CMS Management - Node.js Backend Controller

Sistem manajemen CMS berbasis PHP untuk mengontrol multiple backend Node.js dengan interface web yang user-friendly, mirip dengan cPanel.

## 🚀 Fitur Utama

### 📊 Dashboard
- **Multiple Backend Support**: Kelola lebih dari satu backend Node.js secara bersamaan
- **Real-time Monitoring**: Monitor status backend secara real-time dengan auto-refresh
- **Process Management**: Lihat dan kelola proses backend yang aktif
- **Port Status**: Cek status port untuk setiap backend
- **Quick Actions**: Akses cepat ke fitur-fitur utama

### 📁 File Upload System
- **Drag & Drop Upload**: Upload file dengan drag & drop yang mudah
- **Multiple File Support**: Upload beberapa file sekaligus
- **Progress Tracking**: Progress bar untuk monitoring upload
- **File Management**: Lihat, download, dan hapus file yang diupload
- **Security**: Validasi file type dan ukuran
- **Supported Formats**: ZIP, TAR, GZ, RAR, JS, JSON, TXT, MD, YML, YAML

### 💻 Terminal Interface
- **Web-based Terminal**: Terminal command line dalam browser
- **Command History**: Riwayat command yang dapat dinavigasi
- **Quick Commands**: Tombol cepat untuk command umum
- **Working Directory**: Ganti direktori kerja dengan mudah
- **Real-time Output**: Output command real-time
- **Export History**: Export riwayat command ke file

### 🔧 Git Operations
- **Repository Management**: Clone, pull, dan kelola repository Git
- **Branch Information**: Lihat branch aktif dan status repository
- **Dependency Installation**: Install dependencies Node.js otomatis
- **Git Status**: Cek status repository (clean/dirty)
- **Operation History**: Riwayat operasi Git

### 📋 Log Management
- **Backend Logs**: Monitor log aplikasi backend
- **Error Logs**: Monitor error logs
- **Real-time Updates**: Auto-refresh log secara real-time
- **Log Filtering**: Filter log berdasarkan jumlah baris
- **Log Cleanup**: Bersihkan log lama otomatis

### ⚙️ System Information
- **PHP Environment**: Informasi versi PHP dan ekstensi
- **Node.js Info**: Versi Node.js dan npm
- **Git Info**: Versi Git yang terinstall
- **System Resources**: Memory usage, disk space
- **Backend Configuration**: Konfigurasi setiap backend

## 📋 Persyaratan Sistem

- **PHP**: 7.4 atau lebih tinggi
- **Node.js**: Terinstall di sistem
- **Git**: Terinstall untuk operasi Git
- **Web Server**: Apache/Nginx atau PHP built-in server
- **Extensions**: 
  - `shell_exec` enabled
  - `fileinfo` untuk validasi file
  - `curl` untuk request HTTP
- **Permissions**: Write access untuk direktori uploads dan logs

## 🛠️ Instalasi

### 1. Clone atau Download
```bash
git clone <repository-url>
cd cms_mangement
```

### 2. Konfigurasi Backend
Edit file `includes/config.php` untuk mengatur backend:

```php
$BACKENDS = [
    'backend1' => [
        'name' => 'Backend User Management',
        'path' => 'C:/Users/Aris/Documents/GitHub/backend_user',
        'port' => 3000,
        'script' => 'index.js',
        'description' => 'Backend untuk manajemen user'
    ],
    'backend2' => [
        'name' => 'Backend API',
        'path' => 'C:/Users/Aris/Documents/GitHub/backend_api',
        'port' => 3001,
        'script' => 'server.js',
        'description' => 'Backend untuk API utama'
    ]
];
```

### 3. Set Permissions
```bash
# Linux/macOS
chmod +x start.sh
chmod 755 uploads/
chmod 755 logs/

# Windows
# Pastikan folder uploads dan logs dapat ditulis
```

### 4. Jalankan CMS
```bash
# Menggunakan PHP built-in server
php -S localhost:8080

# Atau gunakan script yang disediakan
./start.sh          # Linux/macOS
start.bat           # Windows
```

### 5. Akses Dashboard
Buka browser dan kunjungi: `http://localhost:8080`

## 📖 Cara Penggunaan

### Dashboard
1. **Pilih Backend**: Gunakan dropdown untuk memilih backend yang ingin dikelola
2. **Monitor Status**: Lihat status real-time backend (Running/Stopped)
3. **Control Backend**: 
   - Start: Jalankan backend
   - Stop: Hentikan backend
   - Restart: Restart backend
   - Install Dependencies: Install npm packages
4. **Process Management**: Lihat dan kill proses yang aktif

### File Upload
1. **Upload File**: Drag & drop file atau klik untuk memilih
2. **Monitor Progress**: Lihat progress upload real-time
3. **Manage Files**: Download atau hapus file yang diupload
4. **File Types**: Hanya file yang diizinkan yang dapat diupload

### Terminal
1. **Execute Commands**: Ketik command di terminal
2. **Quick Commands**: Klik tombol quick command untuk command umum
3. **Change Directory**: Ganti working directory melalui modal
4. **Command History**: Gunakan arrow keys untuk navigasi history
5. **Export History**: Export riwayat command ke file

### Git Operations
1. **Clone Repository**: Masukkan URL repository dan target path
2. **Check Status**: Cek status Git repository setiap backend
3. **Pull Updates**: Pull update terbaru dari remote repository
4. **Install Dependencies**: Install dependencies setelah pull
5. **View History**: Lihat riwayat operasi Git

### Logs
1. **View Logs**: Lihat log backend dan error logs
2. **Auto-refresh**: Log update otomatis setiap beberapa detik
3. **Filter Logs**: Pilih jumlah baris log yang ditampilkan
4. **Clear Logs**: Bersihkan log lama

## 🔧 Konfigurasi

### Backend Configuration
File: `includes/config.php`

```php
// Tambah backend baru
'backend4' => [
    'name' => 'Backend New',
    'path' => '/path/to/backend',
    'port' => 3003,
    'script' => 'app.js',
    'description' => 'Backend baru'
]
```

### Upload Configuration
```php
define('MAX_UPLOAD_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_EXTENSIONS', ['zip', 'tar', 'gz', 'rar', 'js', 'json']);
```

### Security Configuration
File: `.htaccess`
- Proteksi file sensitif
- Security headers
- PHP settings untuk keamanan

## 🔌 API Endpoints

### Backend Control
- `POST /api/backend-control.php`
- Actions: `start`, `stop`, `restart`, `status`, `kill_process`

### File Management
- `POST /api/backend-control.php`
- Actions: `upload_file`, `get_uploaded_files`, `delete_file`

### Git Operations
- `POST /api/backend-control.php`
- Actions: `clone_repository`, `pull_repository`, `install_dependencies`

### System Info
- `POST /api/backend-control.php`
- Actions: `system_info`, `clean_logs`, `execute_command`

## 📁 Struktur File

```
cms_mangement/
├── index.php                 # Dashboard utama
├── upload.php               # File upload page
├── terminal.php             # Terminal interface
├── git.php                  # Git operations
├── logs.php                 # Log management
├── settings.php             # System settings
├── test.php                 # System test
├── start.bat                # Windows start script
├── start.sh                 # Linux/macOS start script
├── .htaccess                # Security configuration
├── README.md                # Documentation
├── includes/
│   ├── config.php          # Configuration
│   └── functions.php       # Core functions
├── api/
│   └── backend-control.php # API endpoints
├── assets/
│   ├── css/
│   │   └── style.css       # Styling
│   └── js/
│       └── script.js       # JavaScript
├── uploads/                 # Upload directory
└── logs/                    # Log files
```

## 🔒 Keamanan

### File Protection
- `.htaccess` melindungi file sensitif
- Validasi file upload
- Sanitasi input
- Security headers

### Access Control
- Validasi session
- Input sanitization
- Command injection protection
- File permission checks

### Recommendations
- Gunakan HTTPS di production
- Batasi akses ke direktori uploads
- Regular backup logs
- Monitor file uploads

## 🐛 Troubleshooting

### Common Issues

#### Backend tidak start
1. Cek path backend di `config.php`
2. Pastikan Node.js terinstall
3. Cek permission direktori
4. Lihat error logs

#### File upload gagal
1. Cek permission direktori uploads
2. Cek ukuran file (max 50MB)
3. Cek ekstensi file
4. Cek PHP upload settings

#### Git operations error
1. Pastikan Git terinstall
2. Cek network connection
3. Cek repository URL
4. Cek permission direktori

#### Terminal tidak berfungsi
1. Cek `shell_exec` enabled
2. Cek permission untuk execute commands
3. Cek working directory
4. Lihat browser console untuk error

### Debug Mode
Akses `test.php` untuk menjalankan diagnostic test sistem.

## 📝 Changelog

### v2.0.0 (Current)
- ✅ Multiple backend support
- ✅ File upload system dengan drag & drop
- ✅ Web-based terminal interface
- ✅ Git operations management
- ✅ Enhanced UI/UX
- ✅ Real-time monitoring
- ✅ Command history
- ✅ Process management
- ✅ Log management

### v1.0.0
- ✅ Basic backend control
- ✅ Single backend support
- ✅ Process monitoring
- ✅ Log viewing

## 🤝 Contributing

1. Fork repository
2. Buat feature branch
3. Commit changes
4. Push ke branch
5. Buat Pull Request

## 📄 License

MIT License - lihat file LICENSE untuk detail.

## 🆘 Support

Jika mengalami masalah:

1. Cek dokumentasi ini
2. Jalankan `test.php` untuk diagnostic
3. Cek error logs di direktori `logs/`
4. Buat issue di repository

## 🔗 Links

- **Dashboard**: `http://localhost:8080`
- **File Upload**: `http://localhost:8080/upload.php`
- **Terminal**: `http://localhost:8080/terminal.php`
- **Git Operations**: `http://localhost:8080/git.php`
- **Logs**: `http://localhost:8080/logs.php`
- **Settings**: `http://localhost:8080/settings.php`
- **System Test**: `http://localhost:8080/test.php`

---

**CMS Management** - Powerful Node.js Backend Controller dengan interface web yang modern dan user-friendly.
