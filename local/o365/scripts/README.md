# Moodle Entra ID App Registration Script

This PowerShell script automates the process of creating and configuring a Microsoft Entra ID application registration for Moodle integration.

## Prerequisites

- PowerShell 7.5 or later on any supported platform (Windows, MacOS, Linux)
- Administrator access to your Microsoft Entra ID tenant
- A Moodle server with HTTPS enabled

## Installation
1. Download and extract the `Moodle-EntraID-PowerShell.zip` file.
2. Open the extracted folder, which contains the script files:
    - `Moodle-EntraID-Script.ps1`
    - `Json/permissions.json`
    - `Json/EntraIDOptionalClaims.json`
    - `Assets/moodle-logo.jpg`

## Usage
1. Open PowerShell 7
2. Navigate to the directory containing the script
3. Run the script:
   ```powershell
   ./Moodle-EntraID-Script.ps1
   ```
4. Follow the prompts:
    - Enter a name for your Microsoft Entra ID application
    - Enter your Moodle server URL (must start with https://)
    - Choose whether to grant admin consent

5. The script will output your Application (Client) ID and Client Secret. Save these credentials securely as they will be needed for Moodle configuration.

## What the Script Does

- Creates a Microsoft Entra ID application registration
- Configures required API permissions
- Sets up authentication URLs
- Configures optional claims
- Adds Teams integration support
- Sets up front-channel logout URL
- Grants admin consent for required permissions
- Generates a client secret
- Sets application logo

## Troubleshooting

- If you get permission errors, make sure you have administrator rights in your Microsoft Entra ID tenant
- If the script fails, you can safely run it again
- Make sure all required files are present in their correct locations

## Security Notes

- Store the generated client secret securely
- Only run this script while connected to a trusted network
- Use the generated credentials only for your Moodle configuration

## Support

For issues with:
- The script: Please report issues to [the repository](https://github.com/microsoft/o365-moodle/issues)
- Microsoft Entra ID: Contact Microsoft Support
- Moodle integration: Refer to [the Moodle documentation](https://docs.moodle.org/405/en/Microsoft_365)

## Code of Conduct

This project has adopted the [Microsoft Open Source Code of Conduct](https://opensource.microsoft.com/codeofconduct/). For more information see the [Code of Conduct FAQ](https://opensource.microsoft.com/codeofconduct/faq/) or contact [opencode@microsoft.com](mailto:opencode@microsoft.com) with any additional questions or comments.

## Copyright

&copy; Microsoft, Inc.  Code for this script is licensed under the GPLv3 license.

Any Microsoft trademarks and logos included in these plugins are property of Microsoft and should not be reused, redistributed, modified, repurposed, or otherwise altered or used outside of this plugin.