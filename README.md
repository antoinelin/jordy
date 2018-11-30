# wordpress-api-docker
WordPress API theme using Docker

Work in progress !

**TODO**
- [x] Add WordPress CLI for automation
- [-] Create docker env for dev / staging / prod
- [x] Add Caddy container for reverse proxy / https
- [ ] Add user restriction for nginx and mysql
- [ ] Create the WordPress API Theme

# Installation
## Getting started

Clone the repo :
```sh
$ git clone https://github.com/toinelin/wordpress-api-docker.git
```

## Create wordpress.dev vhost

### Using dnsmasq (OSX) :

#### => [Full tuto here](https://passingcuriosity.com/2013/dnsmasq-dev-osx/)

Install dnsmasq :
```sh
# Install dnsmasq
$ brew up && brew install dnsmasq
# Copy the default configuration file.
$ cp $(brew list dnsmasq | grep /dnsmasq.conf.example$) /usr/local/etc/dnsmasq.conf
# Copy the daemon configuration file into place.
$ sudo cp $(brew list dnsmasq | grep /homebrew.mxcl.dnsmasq.plist$) /Library/LaunchDaemons/
# Start Dnsmasq automatically.
$ sudo launchctl load /Library/LaunchDaemons/homebrew.mxcl.dnsmasq.plist
```

Update dnsmasq.conf :
```sh
$ vim /usr/local/etc/dnsmasq.conf
```

Add address option :
```sh
address=/dev/127.0.0.1
```

Restart dnsmasq :
```sh
$ sudo launchctl stop homebrew.mxcl.dnsmasq
$ sudo launchctl start homebrew.mxcl.dnsmasq
```

Create resolver :
```sh
$ sudo mkdir -p /etc/resolver
$ cd /etc/resolver
$ sudo touch wordpress.dev
$ sudo vim wordpress.dev
```

Paste it this
```sh
sudo tee /etc/resolver/dev >/dev/null <<EOF
nameserver 127.0.0.1
EOF
```

## Generate SSL certification for dev

#### => [Full tuto here](https://deliciousbrains.com/ssl-certificate-authority-for-local-https-development/)

Create certificate authority (w'ill create it on ```/docker/caddy/ssl``` but you should store CA keys in safe place and use it globaly)
```sh
$ cd docker/caddy/ssl
$ openssl genrsa -des3 -out WordpressRootCA.key 2048
$ openssl req -x509 -new -nodes -key WordpressRootCA.key -sha256 -days 1825 -out WordpressRootCA.pem
```

#### => [Install certificate authority](https://www.bounca.org/tutorials/install_root_certificate.html)

```sh
$ openssl genrsa -out wordpress.dev.key 2048
$ openssl req -new -key wordpress.dev.key -out wordpress.dev.csr # Copy the absolute path of wordpress.dev.key
```

Create a wordpress.dev.ext file and paste it this :
```sh
authorityKeyIdentifier=keyid,issuer
basicConstraints=CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment, dataEncipherment
subjectAltName = @alt_names

[alt_names]
DNS.1 = wordpress.dev
```

Create the certificate : 
```sh
$ openssl x509 -req -in wordpress.dev.csr -CA WordpressRootCA.pem -CAkey WordpressRootCA.key -CAcreateserial -out wordpress.dev.crt -days 1825 -sha256 -extfile wordpress.dev.ext
```

Then delete ```wordpress.dev.csr```

## Run

### Start development
```sh
$ cd docker
$ docker-compose up --build web-server database wordpress redis
$ docker-compose up --build proxy-dev
$ docker-compose run --rm wordpress-cli install-wp
```

### Build
```sh
$ cd docker
$ docker-compose up --build web-server database wordpress redis
$ docker-compose up --build proxy-prod
$ docker-compose run --rm wordpress-cli install-wp
```

### Down / clean commands
```sh
$ docker-compose down # Stop all docker-compose services
$ docker volume prune # Care that it will erase your volumes (including database)
$ docker rm $(docker ps -a -q) # Kill all docker containers
$ docker rmi -f $(docker images -q) # Delete images
```

# Credits
[Thomas Sutton](https://passingcuriosity.com/)
[Brad Touesnard @ Delicious Brain](https://deliciousbrains.com/author/bradt/)
[bounca.org](https://www.bounca.org)
