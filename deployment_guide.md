# Deployment Guide: Moodle on AWS (Test & Prod)

This guide details how to deploy your Moodle application to **two separate AWS EC2 instances** (Test and Production) using Terraform and GitHub Actions.

## Prerequisites

1.  **AWS Account**: Active account with permissions to create EC2 instances.
2.  **AWS CLI**: Installed and configured (`aws configure`).
3.  **Terraform**: Installed locally.
4.  **GitHub Repository**: Code pushed to GitHub.

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
    git clone https://github.com/BryantVanOrdenAust/AuST_Moodle.git ~/moodle
    ```

3.  **Create Configuration File (`.env_deploy`)**:
    *   Copy the example file: `cp .env_deploy.example .env_deploy`
    *   Edit it: `nano .env_deploy`
    *   **Crucial**: Fill in all values (DB credentials, Autodesk keys, etc.).
    *   *Note: This file replaces the need for a separate `.env` file for Docker Compose variables.*

4.  **Private Repo Setup (Required if Private)**:
    *   **Deploy Key**: Generate an SSH key on the server (`ssh-keygen`), add the public key to your GitHub Repo's **Deploy Keys**, and clone using `git@github.com:...`.
    *   **Docker Login**: Create a Personal Access Token (PAT) with `read:packages` scope.
        ```bash
        export CR_PAT=YOUR_TOKEN
        echo $CR_PAT | docker login ghcr.io -u YOUR_USERNAME --password-stdin
        ```

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

# Deploy to Prod
git checkout main
git push origin main
```

## Troubleshooting

-   **SSH Connection Failed**: Check AWS Security Groups (Port 22).
-   **Wrong Image Deployed**: Ensure `docker-compose.prod.yml` uses `${TAG}` and that the GitHub Action exports the correct `TAG` variable (which it is configured to do).
