# DP-3T μBackend

## What is this?
DP-3T Micro Backend (stylized as μBackend) is a lightweight, fast and simple implementation of the [DP-3T backend Web Service](https://github.com/DP-3T/dp3t-sdk-backend/blob/develop/documentation/documentation.pdf) specification.
It has been designed to run on almost any server, regardless of its computational capabilities.

## What is DP-3T?
Decentralized Privacy-Preserving Proximity Tracing (DP-3T) is an open project for the development of "a secure and decentralized privacy-preserving proximity tracing system". Check their [official documents](https://github.com/DP-3T/documents) for more information.

## How-To Install
Unless you know what you're doing, I strongly recommend you to use the Docker installation method as it's easier and more reliable.

### Using Docker
Just clone this repository and deploy the application with the help of Docker Compose:

```sh
git clone https://github.com/josemmo/dp3t-micro-backend
cd dp3t-micro-backend
docker-compose up -d --build
```

That's it! Now you have a working DP-3T backend ready to serve requests.

To upgrade the app, **do not remove the running containers** and just execute the very **same commands** as in the installation process. The database will be preserved between upgrades and automatically migrated if necessary.

### Manual installation
1. Define the `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASS` environment variables to allow the app to connect to the database. *TIP: you can also create a root `.env` file instead to store these values*.
2. Run `composer install` to download third-party dependencies
3. Run `php install.php` to setup the database
4. Use nginx or other web server to redirect all requests to `public/index.php`
