FROM wordpress:php8.2-apache

RUN apt-get update && \
    apt-get upgrade -y && \
    apt-get install -y git && \
    apt-get install -y unzip && \
    apt-get install -y nodejs build-essential npm

RUN node --version
RUN npm --version

# RUN curl -fsSL https://deb.nodesource.com/setup_current.x | bash - && \
#     apt-get install -y nodejs \
#     build-essential && \
#     node --version && \
#     npm --version

RUN curl -O https://github.com/wp-cli/wp-cli/releases/download/v2.9.0/wp-cli-2.9.0.phar
RUN php wp-cli-2.9.0.phar --info
RUN chmod +x wp-cli-2.9.0.phar
RUN mv wp-cli-2.9.0.phar /usr/local/bin/wp
RUN wp --info

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

RUN which composer

RUN composer global require --dev squizlabs/php_codesniffer
RUN composer global config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
RUN composer global require --dev dealerdirect/phpcodesniffer-composer-installer
RUN composer global require --dev phpcsstandards/phpcsutils
RUN composer global config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
RUN composer global require --dev wp-coding-standards/wpcs:"^3.0"
RUN composer global update wp-coding-standards/wpcs --with-dependencies
ENV PATH="${PATH}:/root/.composer/vendor/bin"
RUN echo $PATH
RUN phpcs -i
# RUN phpcs --config-set installed_paths /root/.composer/vendor/wp-coding-standards/wpcs
# RUN phpcs -i

# RUN phpcs /var/www/html/wp-content/plugins/google-authentication-for-wp


# ENV NODE_VERSION=21
# ENV NVM_DIR /tmp/nvm
# WORKDIR $NVM_DIR

# RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.0/install.sh | bash \
#   && . $NVM_DIR/nvm.sh \
#   && nvm install $NODE_VERSION \
#   && nvm alias default $NODE_VERSION \
#   && nvm use default

# ENV NODE_PATH $NVM_DIR/v$NODE_VERSION/lib/node_modules
# ENV PATH      $NVM_DIR/v$NODE_VERSION/bin:$PATH

# RUN node --version
# RUN npm --version