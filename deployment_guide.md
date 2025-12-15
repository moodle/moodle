# Deployment Guide: Moodle on AWS (Test & Prod)

This guide details how to deploy your Moodle application to **two separate AWS EC2 instances** (Test and Production) using Terraform and GitHub Actions.

## Prerequisites

1.  **AWS Account**: Active account with permissions to create EC2 instances.
2.  **AWS CLI**: Installed and configured (`aws configure`).
3.  **Terraform**: Installed locally.
4.  **GitHub Repository**: Code pushed to GitHub.

## Step 0: Create SSH Key Pair

You must create an SSH key pair in AWS before deploying. This key is used to access your servers.

1.  Open your terminal (PowerShell on Windows, or Bash on Linux/Mac).
2.  Run the following command to create the key and save it locally:

    ```bash
    # Windows (PowerShell) - Ensure us-west-2 region
    aws ec2 create-key-pair --key-name moodle-key --query "KeyMaterial" --output text --region us-west-2 | Out-File -Encoding ascii -FilePath moodle-key.pem

    # Mac/Linux - Ensure us-west-2 region
    aws ec2 create-key-pair --key-name moodle-key --query "KeyMaterial" --output text --region us-west-2 > moodle-key.pem
    chmod 400 moodle-key.pem
    ```

## Step 1: Provision Infrastructure (Terraform)

1.  Navigate to the `terraform` directory:
    ```bash
    cd terraform
    ```

2.  Initialize and Apply:
    ```bash
    terraform init
    terraform apply
    ```
    *Type `yes` when prompted.*

3.  **Note the Outputs**: Terraform will output two IPs:
    -   `test_public_ip`: The IP of your **Test** server.
    -   `prod_public_ip`: The IP of your **Production** server.

## Step 2: Configure GitHub Secrets

Go to **Settings -> Secrets and variables -> Actions -> New repository secret**. Add:

| Secret Name | Value |
| :--- | :--- |
| `TEST_HOST` | The `test_public_ip` from Terraform. |
| `PROD_HOST` | The `prod_public_ip` from Terraform. |
| `EC2_USERNAME` | `ubuntu` |
| `EC2_SSH_KEY` | The private content of your SSH key (e.g., `moodle-key.pem`). |

## Step 3: Server Setup (Repeat for BOTH Servers)

You must perform these steps on **BOTH** the Test and Production servers.

1.  SSH into the server:
    ```bash
    ssh -i /path/to/key.pem ubuntu@<SERVER_IP>
    ```

2.  Clone your repository:
    ```bash
    # If using Private Repo (see below), use SSH URL
    git clone https://github.com/BryantVanOrdenAust/moodle.git ~/moodle
    ```

3.  **Create Configuration File (`.env_deploy`)**:
    *   Copy the example file: `cp .env.example .env_deploy`
    *   Edit it: `nano .env_deploy`
    *   **Crucial**: Fill in all values (DB credentials, Autodesk keys, SMTP settings).
    *   **MOODLE_URL**: Change `http://localhost` to `http://<YOUR_PUBLIC_IP>` (e.g., `http://44.000.00.000`). Moodle will not load correctly if this is not set to the exact URL you are using.
    *   *Note: This file replaces the need for a separate `.env` file for Docker Compose variables.*

4.  **Private Repo Setup (Required if Private)**:
    *   **Deploy Key**: Generate an SSH key on the server (`ssh-keygen`), add the public key to your GitHub Repo's **Deploy Keys**, and clone using `git@github.com:...`.
    *   **Docker Login**: Create a Personal Access Token (PAT) with `read:packages` scope.
        ```bash
        export CR_PAT=YOUR_TOKEN
        echo $CR_PAT | docker login ghcr.io -u YOUR_USERNAME --password-stdin
        ```

### SMTP Configuration (Emailer)

To ensure Moodle sends emails (password resets, notifications), you must configure the SMTP settings in your `.env_deploy` file. We recommend using **SendGrid** or **Amazon SES**.

#### Example: SendGrid
```ini
SMTP_HOST=smtp.sendgrid.net
SMTP_PORT=587
SMTP_SECURITY=starttls
SMTP_USER=apikey
SMTP_PASSWORD=your_sendgrid_api_key_starts_with_SG...
SMTP_FROM=no-reply@yourdomain.com
SMTP_FROM_NAME="Moodle System"
```

#### Troubleshooting Email
If emails are not sending:
1.  Check the Moodle logs or Docker logs: `docker-compose logs -f moodle`.
2.  Uncomment `// $CFG->debugsmtp = true;` in `config.php` (if accessible) or check the "Email settings" in Moodle Admin.
3.  Verify your firewall allows outbound traffic on port 587.

## Step 4: Deployment Workflow

The deployment is automated based on branches:

1.  **Deploy to Test**:
    -   Push code to the `test` branch.
    -   GitHub Actions will build the image, tag it as `test`, and deploy it to the **Test Server**.

2.  **Deploy to Production**:
    -   Push code to the `main` branch.
    -   GitHub Actions will build the image, tag it as `main`, and deploy it to the **Production Server**.

### Initial Deployment
To trigger the first deployment:
```bash
# Deploy to Test
git checkout -b test
git push origin test

## Step 4: Deployment Workflow

The deployment is automated based on branches using GitHub Actions.

### 1. Deploying to Test
The **Test** environment is updated automatically when you push to the `test` branch.

1.  **Checkout the test branch**:
    ```bash
    git checkout test
    # If the branch doesn't exist locally yet:
    # git checkout -b test
    ```
2.  **Merge changes** (if you were working on a feature branch):
    ```bash
    git merge my-feature-branch
    ```
3.  **Push to GitHub**:
    ```bash
    git push origin test
    ```
4.  **Verify**:
    -   Go to the **Actions** tab in GitHub to see the build progress.
    -   Once finished, visit your Test URL (e.g., `http://test-moodle.aust-mfg.com`) to confirm changes.

### 2. Deploying to Production
The **Production** environment is updated when you push to the `main` branch.

1.  **Checkout main**:
    ```bash
    git checkout main
    ```
2.  **Merge tested changes**:
    ```bash
    git merge test
    ```
3.  **Push to GitHub**:
    ```bash
    git push origin main
    ```

### 3. Verifying the Deployment on Server
If the site isn't loading, you can SSH into the server to check the status:

1.  SSH in: `ssh -i key.pem ubuntu@<IP>`
2.  Check running containers:
    ```bash
    docker ps
    ```
    *You should see `moodle` and `db` containers listed.*
3.  Check logs if it's crashing:
    ```bash
    docker-compose logs --tail=100 -f moodle
    ```

## Troubleshooting Common Issues

-   **SSH Connection Failed**: Check AWS Security Groups (Port 22 source should be your IP).
-   **"Permission denied" (publickey)**: Ensure you are using the correct `.pem` file and that its permissions are strict (`chmod 400 key.pem`).
-   **Wrong Image Deployed**: Ensure `docker-compose.prod.yml` uses `${TAG}` and that the GitHub Action exports the correct `TAG` variable.
