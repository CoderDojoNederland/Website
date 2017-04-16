# todo: rename to coderdojo.nl after dns switch
server 'beta.coderdojo.nl',
    user: 'coderdojo',
    roles: %w{web app db},
    ssh_options: {
        forward_agent: false,
        auth_methods: ["publickey"],
        keys: ["config/coderdojo_deploy"]
    }


set :pty, true

set :deploy_to, '/coderdojo/website'
