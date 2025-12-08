# AuST Moodle Platform

![AuST Logo](AuST-Group_Logo_MAIN.png)

## Overview
This is the custom Moodle Learning Management System (LMS) for **AuST**. It is containerized using Docker and deployed on AWS, featuring a custom theme and several specialized plugins to automate course creation and user management.

## Quick Links
- **[Deployment Guide](deployment_guide.md)**: Instructions for deploying to Test and Production environments.
- **[Custom Plugins & Themes](docs/custom_plugins.md)**: Documentation for our custom code (`theme_aust`, `local_masterbuilder`, etc.).
- **[Upgrading](UPGRADING.md)**: General Moodle upgrade notes.

## Architecture
- **Core**: Moodle 4.x (PHP 8.3)
- **Database**: PostgreSQL (External/RDS)
- **Infrastructure**: AWS EC2 (Terraform)
- **CI/CD**: GitHub Actions -> GitHub Container Registry -> EC2

## Getting Started (Local Dev)
1. **Clone**: `git clone ...`
2. **Env**: create and configure `.env` and configure. Use env.example as the structure.
3. **Run**: `docker-compose up -d --build`
4. **Access**: `http://localhost`

## Support
For deployment issues, refer to the [Deployment Guide](deployment_guide.md).
For code documentation, check the `docs/` folder.
