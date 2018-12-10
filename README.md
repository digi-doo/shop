<p align="center">
    <a href="https://digidoo.cz" target="_blank">
        <img src="https://digidoo.cz/wp-content/themes/autodevelo/static/logo-digidoo.png" alt="Digidoo" />
    </a>
</p>

Installation
------------
- composer install
- run `php bin/console sylius:install:database --no-interaction`
- run `php bin/console sylius:fixtures:load sshop --no-interaction`
- run `php bin/console rabbitmq-supervisor:rebuild`
- run `php bin/console rabbitmq-supervisor:control start`