# Server Report Dashboard

Aplikasi Laravel untuk monitoring dan manajemen server dari **Google Cloud Platform (GCP)** dan **Proxmox VE**. Dashboard terpusat untuk melacak resource Compute Engine, Cloud SQL, dan Virtual Machine Proxmox dengan fitur sinkronisasi otomatis dan export data.

## Tech Stack

- **Backend:** Laravel 9.x
- **PHP:** 8.1+
- **Frontend:** Bootstrap 5, Vite, Sass
- **Database:** MySQL
- **Cloud Integration:** Google Cloud API (Compute Engine & Cloud SQL)
- **Export:** Maatwebsite Excel
- **Deployment:** Docker (php8.1-fpm + Nginx)
- **Monitoring:** OpenTelemetry (optional)

## Fitur Utama

### 1. Manajemen Lokasi
- **GCP:** Sinkronisasi otomatis project Google Cloud dengan read-only access
- **Proxmox:** CRUD manual untuk datacenter Proxmox VE

### 2. Report Usage GCP
- **Compute Engine:**
  - Sync otomatis instance VM
  - Detail spesifikasi (CPU, RAM, disk, network)
  - Tracking status running/stopped
  - Auto-detect instance yang dihapus
  - Export to Excel
  
- **Cloud SQL:**
  - Sync database instances
  - Monitoring storage & konfigurasi
  - Detail tier & version database
  - Export to Excel

### 3. Report Usage Proxmox
- Sync VM dari Proxmox API
- Detail resource VM (vCPU, memory, storage)
- Start/Stop VM langsung dari dashboard
- Monitoring status real-time

### 4. Security & Authentication
- Laravel Auth dengan email verification
- Protected routes dengan middleware
- Encrypted credentials storage

### 5. History Report
- Tracking perubahan resource
- Log operasi VM

## Installation

### Prerequisites
- PHP 8.1 atau lebih tinggi
- Composer
- MySQL/MariaDB
- Docker (optional)
- Google Cloud Service Account dengan Compute Engine read-only permission
- Proxmox API credentials

### Setup Manual

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd server-report
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```
   
   Atau manual dengan:
   ```bash
   openssl rand --base64 32
   ```

5. **Configure database**
   
   Edit `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=server_report
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Configure Google Cloud credentials**
   
   Edit `.env`:
   ```env
   GOOGLE_APPLICATION_CREDENTIALS="/path/to/service-account.json"
   ```
   
   Service Account harus memiliki permission:
   - `roles/compute.viewer` atau `Compute Engine Viewer`

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **Seed database (optional)**
   ```bash
   php artisan db:seed
   ```
   
   Default users:
   - **Admin:** admin@serverreport.com / password123
   - **Test:** test@serverreport.com / password123
   - **User:** john@example.com / password123
   - **User:** jane@example.com / password123

9. **Build assets**
   ```bash
   npm run build
   ```

10. **Run application**
   ```bash
   php artisan serve
   ```

### Setup dengan Docker

1. **Build Docker image**
   ```bash
   docker build -t server-report:latest .
   ```

2. **Edit docker-compose configuration**
   
   Edit `docker-config/docker-compose.yaml` sesuai kebutuhan

3. **Run dengan docker-compose**
   ```bash
   docker-compose -f docker-config/docker-compose.yaml up -d
   ```

4. **Run migration di container**
   ```bash
   docker exec -it <container-name> php artisan migrate
   ```

5. **Seed database (optional)**
   ```bash
   docker exec -it <container-name> php artisan db:seed
   ```

## Configuration

### Environment Variables

#### Application
```env
APP_NAME="Server Report"
APP_ENV=production
APP_KEY=base64:your-generated-key
APP_DEBUG=false
APP_URL=https://your-domain.com
APP_TIMEZONE="Asia/Jakarta"
```

#### Database
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=server_report
DB_USERNAME=root
DB_PASSWORD=
```

#### Google Cloud Platform
```env
GOOGLE_APPLICATION_CREDENTIALS="/path/to/service-account.json"
```

#### OpenTelemetry (Optional)
```env
OTEL_EXPORTER_OTLP_ENDPOINT="http://your-otel-collector:4318/v1/traces"
OTEL_SERVICE_NAME="server-report"
OTEL_EXPORTER_OTLP_INSECURE=true
```

#### Redis (Optional for caching)
```env
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Usage

### Sinkronisasi Data GCP

1. Tambahkan lokasi/project GCP melalui menu **Lokasi > GCP**
2. Klik **Sync** untuk sinkronisasi project
3. Akses **Report Usage > GCP** untuk melihat resource
4. Klik **Sync CE** atau **Sync Cloud SQL** untuk update data
5. Export data melalui tombol **Export to Excel**

### Manajemen Proxmox

1. Tambahkan datacenter Proxmox melalui menu **Lokasi > Proxmox**
2. Input API endpoint dan credentials
3. Akses **Report Usage > Proxmox** untuk melihat VM
4. Klik **Sync VM** untuk update data
5. Gunakan tombol **Start** untuk menjalankan VM yang stopped

## API Routes

### Web Routes (Protected with Auth)

#### Lokasi
- `GET /lokasi/gcp` - List GCP projects
- `GET /lokasi/gcp/sync` - Sync GCP projects
- `GET /lokasi/proxmox` - List Proxmox datacenters
- `POST /lokasi/proxmox/save` - Add Proxmox datacenter

#### Report GCP
- `GET /report/usage/gcp` - Dashboard GCP resources
- `GET /report/usage/gcp/ce/sync` - Sync Compute Engine instances
- `GET /report/usage/gcp/csql/sync` - Sync Cloud SQL instances
- `GET /report/usage/gcp/ce/excel` - Export CE to Excel
- `GET /report/usage/gcp/csql/excel` - Export Cloud SQL to Excel

#### Report Proxmox
- `GET /report/usage/proxmox` - Dashboard Proxmox VMs
- `GET /report/usage/proxmox/vm/sync` - Sync VM data
- `GET /report/usage/proxmox/node/{id_node}/vm/{id_vm}` - Start VM

## Database Schema

### Tables

- **users** - User authentication
- **lokasi_gcp** - GCP projects/locations
- **server_gcp** - Compute Engine instances
- **csql_gcp** - Cloud SQL instances
- **lokasi_proxmox** - Proxmox datacenters
- **server_proxmox** - Proxmox virtual machines

## Development

### Running Tests
```bash
php artisan test
```

### Database Seeding
```bash
# Seed all tables
php artisan db:seed

# Seed specific seeder
php artisan db:seed --class=UserSeeder

# Fresh migration with seed
php artisan migrate:fresh --seed
```

### Code Style
```bash
./vendor/bin/pint
```

### Local Development
```bash
php artisan serve
npm run dev
```

## Troubleshooting

### Google Cloud Authentication Error
- Pastikan `GOOGLE_APPLICATION_CREDENTIALS` path benar
- Verifikasi Service Account memiliki permission yang diperlukan
- Test dengan: `gcloud auth application-default print-access-token`

### Database Connection Error
- Periksa credentials di `.env`
- Pastikan MySQL service berjalan
- Test koneksi: `php artisan migrate:status`

### Docker Build Error
- Clear Docker cache: `docker builder prune`
- Rebuild without cache: `docker build --no-cache -t server-report:latest .`

## Security

- Jangan commit file `.env` ke repository
- Simpan Google Service Account JSON di lokasi aman
- Gunakan `.gitignore` untuk file sensitif
- Aktifkan HTTPS di production
- Update dependencies secara berkala: `composer update`

## Contributing

Pull requests are welcome. Untuk perubahan major, silakan buka issue terlebih dahulu.

## License

MIT License

## Support

[Buy me a Coffee](https://trakteer.id/captainAldi/link)

## Author

Maintained by [CaptainAldi](https://github.com/captainAldi)