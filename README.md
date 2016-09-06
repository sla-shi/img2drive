Hi there! Welcome to img2drive!

img2drive is a PHP script that backs up your images from the given URLs to Google Drive.
It can be launched in schedule that is defined in the crontab file on your server.

## Support & Documentation

### Install Google API library
php composer.phar require google/apiclient:^2.0

### Google Drive API Requirements
1. Enable the Drive API in your Google Cloud console as described on this page Step 1
https://developers.google.com/drive/v3/web/quickstart/php 
You can also configure the Drive API here https://console.developers.google.com/apis 
if you don't have Google Cloud account.

2. Create 2 kind of credentials - Service account keys and the OAuth 2.0 client ID
https://console.developers.google.com/apis/credentials

3. Download the Service account JSON file and save it as client-secret.json in the project root.

Github repository:
