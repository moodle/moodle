# Setting Up Local Domain

To access Moodle at `http://study.afgou.local`, you need to add it to your hosts file.

## macOS/Linux

**Option 1: Use the provided script**
```bash
./docker/setup-local-domain.sh
```

**Option 2: Manually add**
```bash
sudo nano /etc/hosts
```

Then add this line:
```
127.0.0.1    study.afgou.local www.study.afgou.local
```

**Option 3: Quick command**
```bash
echo "127.0.0.1    study.afgou.local www.study.afgou.local" | sudo tee -a /etc/hosts
```

## Windows

1. Open Notepad **as Administrator**
2. Open `C:\Windows\System32\drivers\etc\hosts`
3. Add this line at the end:
   ```
   127.0.0.1    study.afgou.local www.study.afgou.local
   ```
4. Save the file

## Verify

After adding, verify it worked:
```bash
ping study.afgou.local
```

You should see it resolving to `127.0.0.1`

## Accessing Moodle

Once the domain is added:
- Access via: `http://study.afgou.local` (if using port 80)
- Or: `http://study.afgou.local:8080` (if using port 8080)

Check your `docker-compose.yml` or `.env` file to see which port is configured.

