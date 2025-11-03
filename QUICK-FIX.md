# Quick Fix for Access Issues

## Problem
You can't access `http://study.afgou.local` because:

1. **Domain not in hosts file** - The domain needs to be added to `/etc/hosts`
2. **Port 80 is in use** - Using port 8080 instead

## Solution

### Step 1: Add Domain to Hosts File

**Run this command (you'll be prompted for your password):**
```bash
./docker/setup-local-domain.sh
```

**Or manually:**
```bash
sudo nano /etc/hosts
```

Add this line:
```
127.0.0.1    study.afgou.local www.study.afgou.local
```

Save and exit (Ctrl+X, then Y, then Enter for nano).

### Step 2: Restart Containers

```bash
docker-compose down
docker-compose up -d
```

### Step 3: Access Moodle

Since port 80 is in use, access via:
```
http://study.afgou.local:8080
```

**OR** if you want to use port 80, you can:
1. Stop whatever is using port 80
2. Or create a `.env` file with `WEB_PORT=80`

## Verify It's Working

```bash
# Check if domain resolves
ping study.afgou.local

# Check if containers are running
docker-compose ps

# Check web container logs
docker-compose logs web

# Test if accessible
curl -I http://study.afgou.local:8080
```

If you see HTTP 200 or 302, it's working!

