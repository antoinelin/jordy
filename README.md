# wordpress-api-docker
WordPress API theme using Docker

Work in progress !

**TODO**
- [ ] Add WordPress CLI for automation
- [ ] Create docker env for dev / staging / prod
- [ ] Add Caddy container for reverse proxy / https
- [ ] Add user restriction for nginx and mysql
- [ ] Create the WordPress API Theme


# Commands
```sh
$ docker-compose up --build web-server database wordpress redis
$ docker-compose up --build proxy
$ docker-compose run --rm wordpress-cli install-wp
````