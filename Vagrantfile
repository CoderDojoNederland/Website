Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"

  config.vm.network "forwarded_port", guest: 80, host: 8080
  config.vm.network "forwarded_port", guest: 3306, host: 33306

  config.vm.network "private_network", type: "dhcp"
  config.vm.network "public_network"

  config.vm.provider "virtualbox" do |vb|
    vb.memory = "1024"
    vb.name = "coderdojo-website"
  end

  config.vm.synced_folder ".", "/vagrant", type: "nfs", :nfs => { :mount_options => ["dmode=777","fmode=777"] }

  config.vm.provision "shell", inline: <<-SHELL
    apt-get update

    echo mysql-server-5.5 mysql-server/root_password password supersafe | debconf-set-selections
    echo mysql-server-5.5 mysql-server/root_password_again password supersafe | debconf-set-selections

    apt-get install -y mysql-common mysql-server mysql-client git

    apt-get install -y apache2

    apt-get install -y php5 libapache2-mod-php5
    apt-get install -y php5-mysql php5-curl php5-gd php5-intl php-pear php5-imagick php5-imap php5-mcrypt php5-memcache php5-ming php5-ps php5-pspell php5-recode php5-sqlite php5-tidy php5-xmlrpc php5-xsl

    /etc/init.d/apache2 restart

    rm -rf /var/www/html
    ln -fs /vagrant/web /var/www/html

    apt-get install -y g++
    curl -sL https://deb.nodesource.com/setup_0.12 | sh
    apt-get install -y nodejs

    npm install -g less

    curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

    cd /vagrant

    composer install

    php bin/console doctrine:database:create
    php bin/console doctrine:schema:update --force
    yes | php bin/console doctrine:fixtures:load
    php bin/console assets:install --symlink
    php bin/console cache:clear
  SHELL
end
