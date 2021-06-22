# deploy to same server with deployment server
server '127.0.0.1', user: 'elearning', roles: %w{app db web}
set :deploy_to, '/home/elearning'