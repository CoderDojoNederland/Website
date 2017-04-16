if [[ $TRAVIS_PULL_REQUEST = "false" ]] && [[ $TRAVIS_BRANCH = 'master' ]]; then

    source ~/.rvm/scripts/rvm
    rvm install 2.2
    rvm use 2.2

    ruby -v

    echo 'decrypting deploy key'
    openssl aes-256-cbc -k ${DEPLOY_KEY} -in './config/coderdojo_deploy_enc_travis' -d -a -out '~/.ssh/coderdojo_deploy'
    chmod 600 '~/.ssh/coderdojo_deploy'
    ls -als ~/.ssh

    echo 'preparing Capistrano'
    bundle install

    echo 'Deploying!'
    bundle exec cap production deploy
fi