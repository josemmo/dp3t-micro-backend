# DP-3T μBackend

## What is this?
DP-3T Micro Backend (stylized as μBackend) is a lightweight, fast and simple implementation of the [DP-3T backend Web Service](https://github.com/DP-3T/dp3t-sdk-backend/blob/develop/documentation/documentation.pdf) specification.
It has been designed to run on almost any server, regardless of its computational capabilities.

## What is DP-3T?
Decentralized Privacy-Preserving Proximity Tracing (DP-3T) is an open project for the development of "a secure and decentralized privacy-preserving proximity tracing system". Check their [official documents](https://github.com/DP-3T/documents) for more information.

## How-To Install
1. Define the `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASS` environment variables to allow the app to connect to the database. *TIP: you can also create a root `.env` file instead to store these values*.
2. Run `composer install` to download third-party dependencies
3. Run `php install.php` to setup the database
4. Use nginx or other web server to redirect all requests to `public/index.php`
