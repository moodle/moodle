# Docker Setup for Moodle

This guide explains how to run Moodle using Docker and Docker Compose.

## Prerequisites

- Docker (version 20.10 or higher)
- Docker Compose (version 2.0 or higher)

## Quick Start

1. **Set up local domain** (required):

```bash
# Run the setup script (requires sudo)
./docker/setup-local-domain.sh

# Or manually add to /etc/hosts:
sudo bash -c 'echo "127.0.0.1    study.afgou.local www.study.afgou.local" >> /etc/hosts'
```

2. **Create a `.env` file** (optional - defaults are already configured):

```bash
# Moodle Docker Environment Configuration

# Web Server Configuration
WEB_PORT=80
MOODLE_URL=http://study.afgou.local

# Database Configuration
DB_TYPE=mariadb
DB_VERSION=11
DB_HOST=db
DB_PORT=3307
DB_NAME=moodle
DB_USER=moodle
DB_PASSWORD=moodle
DB_ROOT_PASSWORD=rootpassword
```

3. **Build and start the containers**:

```bash
docker-compose up -d
```

4. **Access Moodle**:

Open your browser and navigate to `http://study.afgou.local`

5. **Follow Moodle installation wizard**:

- Database type: `mariadb` (or `pgsql` if using PostgreSQL)
- Database host: `db`
- Database name: `moodle`
- Database user: `moodle`
- Database password: `moodle`
- Data directory: `/var/moodledata`

## Local Domain Setup

This setup uses `study.afgou.local` as the local domain name. To use it, you need to add it to your system's hosts file:

**macOS/Linux:**
```bash
sudo bash -c 'echo "127.0.0.1    study.afgou.local www.study.afgou.local" >> /etc/hosts'
```

Or use the provided script:
```bash
./docker/setup-local-domain.sh
```

**Windows:**
Edit `C:\Windows\System32\drivers\etc\hosts` as Administrator and add:
```
127.0.0.1    study.afgou.local www.study.afgou.local
```

After adding the domain, restart your Docker containers:
```bash
docker-compose down
docker-compose up -d
```

To change the domain, update:
- `docker/apache/moodle.conf` - ServerName and ServerAlias
- `docker-compose.yml` - MOODLE_URL environment variable
- Your `.env` file (if using one)

## Environment Variables

### Web Server
- `WEB_PORT`: Port on which the web server will be accessible (default: 80)
- `MOODLE_URL`: Full URL of your Moodle installation (default: `http://study.afgou.local`)
- `APACHE_SERVER_NAME`: Apache ServerName directive (default: `study.afgou.local`)

### Database
- `DB_TYPE`: Database type (`mariadb`, `mysql`, or `postgres`)
- `DB_VERSION`: Database version (e.g., `11` for MariaDB, `16` for PostgreSQL)
- `DB_HOST`: Database hostname (use `db` for Docker Compose)
- `DB_PORT`: Database port (default: 3307 for MySQL/MariaDB, 5432 for PostgreSQL) - Note: Changed from 3306 to avoid conflicts with local MySQL installations
- `DB_NAME`: Database name (default: `moodle`)
- `DB_USER`: Database username (default: `moodle`)
- `DB_PASSWORD`: Database password
- `DB_ROOT_PASSWORD`: Database root password

## Using PostgreSQL

To use PostgreSQL instead of MariaDB, use the provided `docker-compose.postgres.yml` file:

```bash
docker-compose -f docker-compose.postgres.yml up -d
```

Or update your `.env` file and use:
```bash
DB_TYPE=postgres
DB_VERSION=16
DB_PORT=5432
```

## Common Commands

### Start containers:
```bash
docker-compose up -d
```

### Stop containers:
```bash
docker-compose down
```

### View logs:
```bash
docker-compose logs -f
```

### Rebuild containers:
```bash
docker-compose build --no-cache
docker-compose up -d
```

### Access web container shell:
```bash
docker-compose exec web bash
```

### Access database:
```bash
docker-compose exec db mysql -u moodle -pmoodle moodle
```

For PostgreSQL:
```bash
docker-compose exec db psql -U moodle -d moodle
```

## Volumes

The following volumes are created:

- `moodledata`: Stores Moodle data files (user uploads, cache, etc.)
- `db_data`: Stores database data

To backup data:
```bash
docker run --rm -v moodle_moodledata:/data -v $(pwd):/backup alpine tar czf /backup/moodledata-backup.tar.gz /data
docker run --rm -v moodle_db_data:/data -v $(pwd):/backup alpine tar czf /backup/db-backup.tar.gz /data
```

To restore data:
```bash
docker run --rm -v moodle_moodledata:/data -v $(pwd):/backup alpine tar xzf /backup/moodledata-backup.tar.gz -C /
docker run --rm -v moodle_db_data:/data -v $(pwd):/backup alpine tar xzf /backup/db-backup.tar.gz -C /
```

## Troubleshooting

### Port Already in Use Error

If you get an error like `bind: address already in use` on port 3306 or 5432:

**Option 1: Use the updated default port (3307 for MySQL/MariaDB)**
The default port has been changed to 3307 to avoid conflicts. Just run:
```bash
docker-compose up -d
```

**Option 2: Remove port mapping if you don't need external database access**
Since containers can communicate internally via Docker networks, you can remove the `ports` section from the `db` service in `docker-compose.yml` if you don't need to connect from outside Docker:

```yaml
db:
  # ... other config ...
  # Remove or comment out the ports section:
  # ports:
  #   - "${DB_PORT:-3307}:3306"
```

**Option 3: Stop the conflicting service**
If you have MySQL/MariaDB running locally on port 3306:
```bash
# On macOS with Homebrew:
brew services stop mysql
# or
brew services stop mariadb

# On Linux:
sudo systemctl stop mysql
# or
sudo systemctl stop mariadb
```

### Permission Issues
If you encounter permission issues with the `moodledata` directory:

```bash
docker-compose exec web chown -R www-data:www-data /var/moodledata
docker-compose exec web chmod -R 777 /var/moodledata
```

### Database Connection Issues
Ensure the database container is running:
```bash
docker-compose ps
```

Check database logs:
```bash
docker-compose logs db
```

### Clear Cache
```bash
docker-compose exec web php admin/cli/purge_caches.php
```

### Run Moodle CLI commands
```bash
docker-compose exec web php admin/cli/cron.php
```

## Production Considerations

For production deployment:

1. **Change default passwords** in the `.env` file
2. **Use environment-specific configuration** (separate `.env` files)
3. **Set up SSL/TLS** with a reverse proxy (nginx or Apache)
4. **Configure proper backups** for both database and moodledata
5. **Set resource limits** in `docker-compose.yml`:
   ```yaml
   deploy:
     resources:
       limits:
         cpus: '2'
         memory: 4G
   ```
6. **Use secrets management** instead of plain text passwords in `.env`
7. **Configure log rotation** to prevent disk space issues

## Support

For Moodle-specific issues, refer to the [Moodle documentation](https://docs.moodle.org/).

