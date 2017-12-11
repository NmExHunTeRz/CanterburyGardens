# Canterbury Gardens Ltd

Designed to help Farmer Cooksey at Canterbury Gardens with his day-to-day farming operations by applying data aggregation, fusion and visualisation.

You can also access this project from our [GitHub Repository](https://github.com/NmExHunTeRz/CanterburyGardens)

Our video demonstration of the project can be viewed on [Youtube](https://www.youtube.com/watch?v=JqUNPY5axpU&feature=youtu.be) 

The data processing and robustness criterias were primarily addressed in `CanterburyGardens\app\Http\Controllers\MainController` and visualisation has been addressed primarily in `C:\xampp\htdocs\CanterburyGardens\resources\views` in the files `graphs.blade.php` and `index.blade.php`
## Setup for Windows
1 - Install [Xampp PHP7](https://www.apachefriends.org/download.html) and install [Composer](https://getcomposer.org/download/)
```
Install Xampp with PHP7 or a preferred local web server solution.
```
2 -  Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf` and add the following to it

```
<VirtualHost canterburygardens.dev:80>
  DocumentRoot "C:\xampp\htdocs\canterburygardens\public"
  ServerAdmin canterburygardens.dev
  <Directory "C:\xampp\htdocs\canterburygardens></Directory>
</VirtualHost>
```
3 - Add a new host to `C:\Windows\System32\drivers\etc` 
```
127.0.0.1       canterburygardens.dev
```

4 - Run Xampp as admin, install the Apache and MySQL components then start them
 
5 - Visit [phpMyAdmin](http://localhost/phpmyadmin/) and create a local database with the name `CanterburyGardens`

6 - Git pull or copy the project files into `C:\xampp\htdocs\CanterburyGardens` and then `cd` into this directory from command prompt

7 - Run `composer update` to download all the packages required by Laravel

8 - Copy `.env.example` and save as `.env` then in terminal run `php artisan key:generate`

9 - Finally run `php artisan migrate:refresh --seed` and visit [Canterbury Gardens](http://canterburygardens.dev/), login with `cooksey@canterburygardens.co.uk` `password`
## Built With

* [Laravel](https://laravel.com/) - The MVC driven PHP framework
* [Chart.js](http://www.chartjs.org/) - Graph visualisations
* [Bootstrap 3](http://www.chartjs.org/) - Responsive front-end component library
* [Cosmo](https://bootswatch.com/3/cosmo/) - Bootstrap theme
* [Font Awesome](http://fontawesome.io/license/) - Scalable vector icons
* [Department for Environment Food and Rural Affairs](http://environment.data.gov.uk) - Rainfall data from the government
* [Guzzle](https://github.com/guzzle/guzzle) - A PHP HTTP Client
* [Met Office](https://www.metoffice.gov.uk/datapoint) - Weather data feeds
 

## Authors

* **Deniz Aygun**
* **Daniel Bard**
* **Huy Le Nguyen**