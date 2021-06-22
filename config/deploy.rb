# config valid only for current version of Capistrano
lock '3.6.1'

set :application, 'elearning'
set :repo_url, 'git@github.com:dtnsolutions/elearning'

# use rsync, comment out to disable
set :scm, :rsync

# bypass rsync to local
set :bypass_git_clone, true

set :rsync_stage, '.'
set :rsync_options, %w[-av --copy-links --recursive --delete --delete-excluded --exclude .git* --exclude .idea* --exclude log/capistrano.log]

set :linked_files, ['config.php']
set :linked_dirs, ['moodledata']

# Default value for keep_releases is 5
set :keep_releases, 1