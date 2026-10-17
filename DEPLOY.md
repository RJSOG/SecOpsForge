# Déploiement sur le VPS (Docker)

L'image contient le site (PHP 8.4 + Caddy via FrankenPHP) et les assets déjà buildés. Les articles n'y sont pas : ils viennent du dépôt `SecOpsForge-content`, cloné sur le VPS et monté dans le conteneur.

## 1. Premier déploiement

```bash
# Code du site
git clone git@github.com:RJSOG/SecOpsForge.git /docker/SecOpsForge
cd /docker/SecOpsForge

# Articles (deploy key en lecture seule conseillée, voir plus bas)
git clone git@github.com:RJSOG/SecOpsForge-content.git /docker/SecOpsForge/content
chown -R 1000:1000 /docker/SecOpsForge/content   # l'éditeur en ligne écrit dedans (voir « Droits sur les articles »)

# Configuration
cp .env.production.example .env
docker compose run --rm --no-deps --entrypoint php app artisan key:generate --show   # → APP_KEY dans .env
nano .env   # APP_URL, SESSION_DOMAIN, SERVER_NAME, ADMIN_*

docker compose up -d --build

# Compte admin (une seule fois), puis retire ADMIN_PASSWORD du .env
docker compose exec app php artisan db:seed --class=AdminSeeder --force
```

## 2. HTTPS et ports 80 / 443

- **Rien d'autre n'écoute sur 80/443** : mets `SERVER_NAME=ton-domaine.fr` et fais pointer le DNS (enregistrement A) vers l'IP du VPS. Caddy obtient et renouvelle le certificat Let's Encrypt tout seul.
- **Un autre reverse proxy occupe déjà 80/443** : mets `SERVER_NAME=:80` et `TRUSTED_PROXIES=*` dans `.env`, puis adapte le conteneur à ton proxy dans un `docker-compose.override.yml` (ignoré par git, chargé automatiquement par Docker Compose). Exemple avec Traefik (provider Docker) :

  ```yaml
  services:
    app:
      ports: !reset []
      labels:
        - traefik.enable=true
        - traefik.http.routers.secopsforge.rule=Host(`ton-domaine.fr`)
        - traefik.http.routers.secopsforge.entrypoints=websecure
        - traefik.http.routers.secopsforge.tls.certresolver=letsencrypt
        - traefik.http.services.secopsforge.loadbalancer.server.port=80
  ```

  Adapte `entrypoints` et `certresolver` aux noms de ta config Traefik. Avec Nginx ou un autre proxy, publie plutôt le port en local (`HTTP_PORT=127.0.0.1:8081`, `HTTPS_PORT=127.0.0.1:8443`) et fais pointer le proxy vers `127.0.0.1:8081`.

Vérifie ce qui écoute déjà avec `sudo ss -tlnp | grep -E ':80|:443'`.

## Droits sur les articles

Le site tourne dans le conteneur sous l'utilisateur `secopsforge` (UID 1000). Pour que l'éditeur en ligne puisse créer et modifier des articles, le dossier monté doit lui appartenir :

```bash
chown -R 1000:1000 /docker/SecOpsForge/content
git config --global --add safe.directory /docker/SecOpsForge/content   # git lancé en root sur un dossier UID 1000
```

Un `git pull` lancé en root crée des fichiers appartenant à root : relance le `chown` après chaque pull (voir §3).

## 3. Mettre à jour

```bash
cd /docker/SecOpsForge && git pull && docker compose up -d --build   # code
git -C /docker/SecOpsForge/content pull                               # articles (pris en compte immédiatement)
chown -R 1000:1000 /docker/SecOpsForge/content                         # droits pour l'éditeur en ligne
```

Les migrations et les caches Laravel sont relancés automatiquement au démarrage du conteneur (`docker/entrypoint.sh`).

## 4. Données et sauvegardes

| Quoi | Où |
|---|---|
| Base SQLite (comptes) | volume `app-data` |
| Logs, caches | volume `app-storage` (les logs vont aussi sur `docker compose logs`) |
| Certificats TLS | volume `caddy-data` |
| Articles | `/docker/SecOpsForge/content` (dépôt git) |

L'éditeur en ligne écrit directement dans `/docker/SecOpsForge/content`. Si tu l'utilises en prod, commite depuis le VPS (`git -C /docker/SecOpsForge/content add -A && git commit && git push`) pour ne pas diverger de ta version locale. Le plus simple est d'écrire les articles en local et de les pousser.

## 5. Deploy key pour les articles

Pour que le VPS puisse cloner `SecOpsForge-content` sans avoir accès à tout ton compte GitHub :

```bash
ssh-keygen -t ed25519 -f ~/.ssh/secopsforge_content -N ""
cat ~/.ssh/secopsforge_content.pub   # à coller dans GitHub › SecOpsForge-content › Settings › Deploy keys (lecture seule)
```

Puis dans `~/.ssh/config` du VPS :

```
Host github-content
    HostName github.com
    IdentityFile ~/.ssh/secopsforge_content
```

et clone avec `git clone git@github-content:RJSOG/SecOpsForge-content.git /docker/SecOpsForge/content`.
