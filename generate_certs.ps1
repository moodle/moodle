# Create certs directory if it doesn't exist
$certDir = Join-Path $PSScriptRoot "certs"
if (-not (Test-Path -Path $certDir)) {
    New-Item -ItemType Directory -Path $certDir | Out-Null
}

Write-Host "Generating self-signed certificate for localhost using Alpine..."
# We use alpine and install openssl on the fly
docker run --rm -v "${certDir}:/certs" alpine /bin/sh -c "apk add --no-cache openssl && openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /certs/privkey.pem -out /certs/fullchain.pem -subj '/CN=localhost'"

if (Test-Path "$certDir\fullchain.pem") {
    Write-Host "Certificate generation complete. Files created."
}
else {
    Write-Error "Certificate generation failed. Files not found."
}
