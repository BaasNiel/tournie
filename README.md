## Tournie

Features

* [Laravel v8](https://laravel.com/docs/8.x)
* [VueJS v3](https://v3.vuejs.org)
* [Tailwind v2](https://tailwindcss.com)

### Prerequisites

* [Composer](https://getcomposer.org/)
* [Docker](https://www.docker.com/)

### Getting started

1. Clone this repository
```shell
cd /path/to/projects
git clone git@github.com:BaasNiel/tournie.git
cd tournie
```

2. Set the environmental variables
```shell
cp .env.example .env
```

3. Install composer dependencies for the first time
```shell
composer install --ignore-platform-reqs
```

4. Launch the environment
```shell
./vendor/bin/sail up -d
```

5. Create the application key
```shell
./vendor/bin/sail artisan key:generate
```

6. Install NPM dependencies
```shell
./vendor/bin/sail npm install
```

7. Start the dev serve
```shell
./vendor/bin/sail npm run watch
```

8. Go to [http://localhost](http://localhost)

### Tips

- Create an alias for `sail`, see https://laravel.com/docs/8.x/sail#configuring-a-bash-alias
