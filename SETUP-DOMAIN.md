# Complete Setup Guide: Moodle with Local Domain

This guide walks you through setting up Moodle with Docker and configuring it to use a local domain name (`study.afgou.local`) from scratch.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Install Docker](#install-docker)
3. [Clone/Prepare Moodle](#clone-prepare-moodle)
4. [Set Up Local Domain](#set-up-local-domain)
5. [Configure and Start Docker](#configure-and-start-docker)
6. [Access Moodle](#access-moodle)
7. [Complete Moodle Installation](#complete-moodle-installation)
8. [Verification](#verification)
9. [Troubleshooting](#troubleshooting)

## Prerequisites

Before starting, ensure you have:
- A computer running **macOS**, **Linux**, or **Windows**
- Administrator/root access (for Docker installation and hosts file editing)
- At least **2GB free RAM** and **10GB free disk space**
- Internet connection (for downloading Docker images)

## Install Docker

### macOS

**Option 1: Using Homebrew (Recommended)**
```bash
# Install Homebrew if you don't have it
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install Docker Desktop
brew install --cask docker

# Or install Docker CLI only
brew install docker docker-compose
```

**Option 2: Download Docker Desktop**
1. Visit [Docker Desktop for Mac](https://www.docker.com/products/docker-desktop/)
2. Download and install the `.dmg` file
3. Launch Docker Desktop from Applications
4. Wait for Docker to start (whale icon in menu bar should be steady)

### Linux (Ubuntu/Debian)

```bash
# Update package index
sudo apt-get update

# Install prerequisites
sudo apt-get install -y \
    ca-certificates \
    curl \
    gnupg \
    lsb-release

# Add Docker's official GPG key
sudo mkdir -p /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg

# Set up repository
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null

# Install Docker Engine and Docker Compose
sudo apt-get update
sudo apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Add your user to docker group (optional, to run without sudo)
sudo usermod -aG docker $USER

# Start Docker service
sudo systemctl start docker
sudo systemctl enable docker

# Log out and back in for group changes to take effect
```

### Linux (Fedora/RHEL/CentOS)

```bash
# Install prerequisites
sudo dnf install -y dnf-plugins-core

# Add Docker repository
sudo dnf config-manager --add-repo https://download.docker.com/linux/fedora/docker-ce.repo

# Install Docker Engine and Docker Compose
sudo dnf install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin

# Start Docker service
sudo systemctl start docker
sudo systemctl enable docker

# Add your user to docker group (optional)
sudo usermod -aG docker $USER
```

### Windows

**Option 1: Download Docker Desktop**
1. Visit [Docker Desktop for Windows](https://www.docker.com/products/docker-desktop/)
2. Download and run the installer
3. Follow the installation wizard
4. Restart your computer if prompted
5. Launch Docker Desktop from Start menu

**Option 2: Using WSL2 (Recommended for Windows 10/11)**
1. Install WSL2: `wsl --install`
2. Follow Linux instructions above within WSL

### Verify Docker Installation

After installation, verify Docker is working:

```bash
# Check Docker version
docker --version
# Should output something like: Docker version 24.0.0, build ...

# Check Docker Compose version
docker-compose --version
# Should output something like: Docker Compose version v2.20.0

# Test Docker
docker run hello-world
# This should download and run a test container
```

If you see "Hello from Docker!" it's working correctly!

## Clone/Prepare Moodle

### If You Already Have Moodle Files

If you're working in the Moodle directory already, skip to the next section.

### If You Need to Get Moodle

**Option 1: Git Clone**
```bash
# Navigate to your projects directory
cd ~/Documents  # or wherever you keep projects

# Clone Moodle (if you have a repository)
git clone <your-moodle-repo-url> lms
cd lms
```

**Option 2: Download Moodle**
1. Visit [Moodle Downloads](https://download.moodle.org/)
2. Download the latest version
3. Extract to your project directory

## Set Up Local Domain

### macOS/Linux

**Method 1: Using the Setup Script (Easiest)**

```bash
# Navigate to your Moodle directory
cd /path/to/your/moodle

# Make the script executable
chmod +x docker/setup-local-domain.sh

# Run the script (will prompt for password)
./docker/setup-local-domain.sh
```

**Method 2: Manual Setup**

```bash
# Open hosts file in editor
sudo nano /etc/hosts
# or
sudo vim /etc/hosts
```

Add this line at the end of the file:
```
127.0.0.1    study.afgou.local www.study.afgou.local
```

Save and exit:
- **nano**: Press `Ctrl+X`, then `Y`, then `Enter`
- **vim**: Press `Esc`, type `:wq`, press `Enter`

**Method 3: Quick Command**

```bash
echo "127.0.0.1    study.afgou.local www.study.afgou.local" | sudo tee -a /etc/hosts
```

### Windows

**Method 1: Using Notepad (as Administrator)**

1. Open **Start Menu** → Search for **Notepad**
2. Right-click **Notepad** → Select **Run as administrator**
3. In Notepad, click **File** → **Open**
4. Navigate to `C:\Windows\System32\drivers\etc\`
5. Change file type filter to **All Files (*.*)**
6. Open the file named `hosts` (no extension)
7. Add this line at the end:
   ```
   127.0.0.1    study.afgou.local www.study.afgou.local
   ```
8. Click **File** → **Save**
9. Close Notepad

**Method 2: Using PowerShell (as Administrator)**

1. Right-click **Start Menu** → Select **Windows PowerShell (Admin)** or **Terminal (Admin)**
2. Run:
   ```powershell
   Add-Content -Path C:\Windows\System32\drivers\etc\hosts -Value "127.0.0.1    study.afgou.local www.study.afgou.local"
   ```

### Verify Domain Setup

Test that the domain resolves correctly:

```bash
# macOS/Linux/Windows (WSL)
ping study.afgou.local

# Windows (PowerShell)
Test-Connection study.afgou.local

# Or test with curl
curl -I http://study.afgou.local:8080
```

You should see it resolving to `127.0.0.1` (localhost).

## Configure and Start Docker

### Step 1: Navigate to Moodle Directory

```bash
cd /path/to/your/moodle/project
# For example: cd ~/Documents/AUF/lms
```

### Step 2: Review Docker Configuration

The `docker-compose.yml` file is already configured with:
- Web server on port **8080** (default)
- Database on port **3307** (to avoid conflicts)
- Domain set to `study.afgou.local`

### Step 3: Create Environment File (Optional)

You can create a `.env` file to customize settings:

```bash
# Create .env file
cat > .env << EOF
# Web Server Configuration
WEB_PORT=8080
MOODLE_URL=http://study.afgou.local:8080

# Database Configuration
DB_TYPE=mariadb
DB_VERSION=11
DB_HOST=db
DB_PORT=3307
DB_NAME=moodle
DB_USER=moodle
DB_PASSWORD=moodle
DB_ROOT_PASSWORD=rootpassword
EOF
```

**Important:** Change the `DB_PASSWORD` and `DB_ROOT_PASSWORD` to secure values for production!

### Step 4: Build and Start Containers

```bash
# Build the Docker images (first time only, or after changes)
docker-compose build

# Start all services in detached mode (runs in background)
docker-compose up -d

# Or build and start in one command
docker-compose up -d --build
```

### Step 5: Check Container Status

```bash
# Check if containers are running
docker-compose ps

# You should see:
# - moodle-web: Up (running)
# - moodle-db: Up (running)

# Check logs if there are issues
docker-compose logs web
docker-compose logs db
```

### Step 6: Wait for Database to Initialize

The database container needs a moment to initialize. Wait 10-30 seconds, then check:

```bash
# Check database logs
docker-compose logs db | tail -20

# Should see "ready for connections" message when ready
```

## Access Moodle

### Open in Browser

Once containers are running, open your web browser and navigate to:

```
http://study.afgou.local:8080
```

**Note:** If you're using port 80 instead of 8080, use:
```
http://study.afgou.local
```

### If Browser Can't Connect

1. **Check if containers are running:**
   ```bash
   docker-compose ps
   ```

2. **Check container logs:**
   ```bash
   docker-compose logs web --tail 50
   ```

3. **Test if web server responds:**
   ```bash
   curl -I http://localhost:8080
   # or
   curl -I http://study.afgou.local:8080
   ```

4. **Verify hosts file:**
   ```bash
   # macOS/Linux
   cat /etc/hosts | grep study.afgou.local
   
   # Windows
   type C:\Windows\System32\drivers\etc\hosts | findstr study.afgou.local
   ```

## Complete Moodle Installation

When you first access Moodle, you'll see the installation wizard. Follow these steps:

### Installation Wizard

1. **Select Language**: Choose your preferred language

2. **Database Setup**:
   - **Database type**: `MariaDB` or `MySQL`
   - **Database host**: `db`
   - **Database name**: `moodle`
   - **Database user**: `moodle`
   - **Database password**: `moodle` (or what you set in `.env`)
   - **Port**: Leave empty (uses default 3306)
   - **Unix socket**: Leave empty
   - **Database prefix**: `mdl_` (default)

3. **Data Directory**:
   - **Data directory**: `/var/moodledata`

4. **Web Configuration**:
   - **Web address**: `http://study.afgou.local:8080`
   - **Moodle directory**: `/var/www/html/public`

5. **Admin Account**:
   - Create your administrator username and password
   - Enter your email address

6. **Complete Installation**: Click "Continue" and wait for installation to finish

### After Installation

Once installation completes, you'll be redirected to the Moodle dashboard. You can:

- Access admin panel: `http://study.afgou.local:8080/admin`
- Login with your admin credentials
- Start creating courses and content

## Verification

### Test Full Stack

Run these commands to verify everything is working:

```bash
# 1. Check Docker containers
docker-compose ps
# Should show both containers as "Up"

# 2. Test domain resolution
ping -c 3 study.afgou.local
# Should resolve to 127.0.0.1

# 3. Test web server
curl -I http://study.afgou.local:8080
# Should return HTTP 200 or 302

# 4. Test database connection
docker-compose exec db mysql -u moodle -pmoodle moodle -e "SELECT 1;"
# Should return "1"

# 5. Check Moodle logs
docker-compose exec web tail -20 /var/www/html/public/moodledata/*/error.log
```

## Troubleshooting

### Issue: "Cannot connect to Docker daemon"

**Solution:**
```bash
# macOS/Linux: Start Docker service
sudo systemctl start docker  # Linux
# macOS: Start Docker Desktop application

# Verify Docker is running
docker ps
```

### Issue: "Port already in use"

**Solution:**
```bash
# Check what's using the port
# macOS/Linux
lsof -i :8080

# Windows
netstat -ano | findstr :8080

# Stop the conflicting service or change port in docker-compose.yml
```

### Issue: "Domain not resolving"

**Solution:**
1. Verify hosts file entry:
   ```bash
   # macOS/Linux
   cat /etc/hosts | grep study.afgou.local
   
   # Windows
   type C:\Windows\System32\drivers\etc\hosts | findstr study.afgou.local
   ```

2. Flush DNS cache:
   ```bash
   # macOS
   sudo dscacheutil -flushcache; sudo killall -HUP mDNSResponder
   
   # Linux
   sudo systemd-resolve --flush-caches
   
   # Windows
   ipconfig /flushdns
   ```

3. Restart browser or try incognito/private mode

### Issue: "Database connection failed"

**Solution:**
```bash
# Check if database container is running
docker-compose ps db

# Check database logs
docker-compose logs db --tail 50

# Test database connection manually
docker-compose exec db mysql -u moodle -pmoodle moodle

# Restart database container
docker-compose restart db
```

### Issue: "Permission denied" errors

**Solution:**
```bash
# Fix moodledata permissions
docker-compose exec web chown -R www-data:www-data /var/moodledata
docker-compose exec web chmod -R 777 /var/moodledata
```

### Issue: "Container keeps restarting"

**Solution:**
```bash
# Check logs for errors
docker-compose logs web
docker-compose logs db

# Rebuild containers
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### Issue: "Cannot access after host file edit"

**Solution:**
1. Make sure you saved the hosts file correctly
2. Clear browser cache
3. Try accessing with IP directly: `http://127.0.0.1:8080`
4. Restart Docker containers:
   ```bash
   docker-compose restart
   ```

## Common Commands Reference

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f web
docker-compose logs -f db

# Restart a specific service
docker-compose restart web
docker-compose restart db

# Rebuild after changes
docker-compose build --no-cache
docker-compose up -d

# Access container shell
docker-compose exec web bash
docker-compose exec db bash

# Run Moodle CLI commands
docker-compose exec web php admin/cli/cron.php
docker-compose exec web php admin/cli/purge_caches.php

# View container status
docker-compose ps

# Stop and remove everything (including volumes)
docker-compose down -v
```

## Next Steps

After successful setup:

1. **Secure Your Installation:**
   - Change default database passwords
   - Configure SSL/TLS for production
   - Set up proper backup strategy

2. **Configure Moodle:**
   - Set up site settings
   - Configure email settings
   - Install additional plugins/themes

3. **Set Up Regular Maintenance:**
   - Configure cron jobs for scheduled tasks
   - Set up automated backups

4. **Documentation:**
   - Review [Moodle Documentation](https://docs.moodle.org/)
   - Explore Moodle admin panel features

## Support

- **Moodle Documentation**: https://docs.moodle.org/
- **Moodle Forums**: https://moodle.org/forums/
- **Docker Documentation**: https://docs.docker.com/

## Summary

You now have:
✅ Docker installed and running
✅ Moodle running in Docker containers
✅ Local domain `study.afgou.local` configured
✅ Database and web server connected
✅ Moodle accessible at `http://study.afgou.local:8080`

Enjoy using Moodle! 🎓
