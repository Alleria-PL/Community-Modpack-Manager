# Community-Modpack-Manager

A simple, self-hosted solution for managing a Minecraft modpack server and its client distribution — without granting root or administrator access to your team members.

## Overview

Community-Modpack-Manager was built to solve a common problem: you want to let someone on your team update and manage a Minecraft modpack server and its corresponding client pack, but you don't want to give them full server access. This project ties together three proven tools to create a secure, easy-to-use management workflow:

- **[Technic Launcher / Solder](https://www.technicpack.net/)** — hosts and distributes the modpack client to players.
- **[Pterodactyl Panel](https://pterodactyl.io/)** — provides a web-based game server management UI so administrators can start, stop, and update the server without SSH or root access.
- **[Cloudflare Zero Trust](https://www.cloudflare.com/zero-trust/)** — secures access to the management interfaces, so only authorized users can reach the admin tools, without exposing ports directly to the internet.

## Features

- Host and update a Minecraft modpack server through the Pterodactyl panel.
- Distribute the matching client modpack to players via Technic Launcher.
- Delegate day-to-day server management (restarts, config updates, mod updates) to trusted team members — no root or SSH credentials needed.
- All management UIs are protected behind Cloudflare Zero Trust; access is granted by identity, not by network location.

## Architecture

```
Players
  └─► Technic Launcher ──────────────────────────────► Modpack files (hosted)
                                                              │
Admins / Moderators                                           │
  └─► Cloudflare Zero Trust (identity check)                 │
           └─► Pterodactyl Panel ──► Game Server (Minecraft) ┘
```

## Prerequisites

| Tool | Purpose |
|------|---------|
| [Pterodactyl Panel](https://pterodactyl.io/panel/1.0/getting_started.html) | Game server management UI |
| [Technic Solder](https://docs.technicpack.net/solder/) (or static file host) | Modpack client distribution |
| [Cloudflare Zero Trust](https://developers.cloudflare.com/cloudflare-one/) | Access control / tunneling |
| A Linux server | Hosting everything above |

## Setup

### 1. Pterodactyl Panel

1. Follow the [official Pterodactyl installation guide](https://pterodactyl.io/panel/1.0/getting_started.html) to install the panel and a Wings daemon on your server.
2. Create a new server in the panel using a Minecraft egg that matches your modpack's Forge/Fabric version.
3. Upload your modpack server files through the panel's file manager.
4. Create a panel user account for each team member who needs to manage the server — assign them only the permissions they need (no admin role required for day-to-day tasks).

### 2. Technic Launcher / Solder

1. Set up [Technic Solder](https://docs.technicpack.net/solder/) or use a static web server to host your modpack ZIP files.
2. Create a modpack entry on the [Technic Platform](https://www.technicpack.net/) pointing to your Solder or direct-download URL.
3. Keep the client-side modpack in sync with the server-side mods so players always run the correct version.

### 3. Cloudflare Zero Trust

1. Add your domain to Cloudflare.
2. Create a [Cloudflare Tunnel](https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/) that forwards traffic to your Pterodactyl Panel (and optionally your Solder instance).
3. Configure an **Access Application** in the Zero Trust dashboard to require authentication (e.g., email OTP, GitHub, or Google SSO) before the panel is reachable.
4. Add team members to the appropriate Access policies — they log in through Cloudflare and reach the Pterodactyl panel without needing VPN or direct server access.

## Usage

- **Players** install the modpack through Technic Launcher using the pack URL and play normally.
- **Server managers** log in at the Pterodactyl panel URL (protected by Cloudflare Zero Trust) to restart the server, update configs, or swap mods.
- **Modpack maintainers** upload updated mod ZIPs to the Solder/static host and bump the pack version; Technic Launcher automatically prompts players to update.

## Security Notes

- No team member ever needs SSH keys or root credentials for routine management tasks.
- All administrative traffic passes through Cloudflare's network; the panel port is never open directly on the server's firewall.
- Revoke access instantly by removing a user from the Cloudflare Zero Trust policy — no password resets or key rotation needed.

## License

This project is provided as-is. Feel free to adapt it to your own community's needs.
