# AuST Moodle Platform

![AuST Logo](AuST-Group_Logo_MAIN.png)

## Overview
This is the custom Moodle Learning Management System (LMS) for **AuST**. It is containerized using Docker and deployed on AWS, featuring a custom theme and several specialized plugins to automate course creation and user management.

## Quick Links
- **[Deployment Guide](deployment_guide.md)**: Instructions for deploying to Test and Production environments.
- **[CI/CD Pipeline](docs/ci_cd.md)**: Details on automated testing and troubleshooting.
- **[Custom Plugins & Themes](docs/custom_plugins.md)**: Documentation for our custom code (`theme_aust`, `local_masterbuilder`, etc.).
- **[Upgrading](UPGRADING.md)**: General Moodle upgrade notes.

## Architecture
- **Core**: Moodle 4.x (PHP 8.3)
- **Database**: PostgreSQL (External/RDS)
- **Infrastructure**: AWS EC2 (Terraform)
- **CI/CD**: GitHub Actions -> GitHub Container Registry -> EC2

## Deployment Workflow
We use a **branch-based** deployment strategy. You control where code goes by pushing to specific branches.

| Environment | Branch | How to Deploy |
| :--- | :--- | :--- |
| **Test** | `test` | Push code to the `test` branch. The **Test Server** will automatically update. |
| **Production** | `main` | Push code to the `main` branch. The **Prod Server** will automatically update. |
| **Both** | - | Push to `test` first to verify, then merge `test` into `main` and push. |

### How to Deploy

#### 1. Deploy to Test (Routine)
Use this when you want to test new features.
```bash
git checkout test
git pull origin test      # Ensure you have the latest test code
git merge <your-branch>   # Merge your changes (e.g., git merge my-new-feature)
git push origin test      # Triggers deployment to Test Server
```

#### 2. Deploy to Production (Release)
Use this ONLY after verifying changes on the Test site.
```bash
git checkout main
git pull origin main      # Ensure you have the latest prod code
git merge test            # Merge the tested code from the test branch
git push origin main      # Triggers deployment to Production Server
```

## Getting Started (Local Dev)
1. **Clone**: `git clone ...`
2. **Env**: create and configure `.env`. Use `env.example` as the structure.
3. **Run**: `docker-compose up -d --build`
4. **Access**: `http://localhost`

## Support
For deployment issues, refer to the [Deployment Guide](deployment_guide.md).
For code documentation, check the `docs/` folder.
