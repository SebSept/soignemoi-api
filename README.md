# Projet ECF - API REST

# Certificat ssl

## Génération

```bash
docker compose -f compose-prod.yaml run --rm --entrypoint "\
certbot certonly --webroot -w /var/www/certbot \
-d api.ecf.seb7.fr --email sebastienmonterisi@gmail.com --agree-tos --no-eff-email \
--force-renewal" certbot
```

## Renouvellement (non testé)

0 12 * * * /usr/bin/docker-compose -f /path/to/your/docker-compose.yml run --rm --entrypoint "\
certbot renew --webroot -w /var/www/certbot --quiet --renew-hook \"nginx -s reload\"" certbot
