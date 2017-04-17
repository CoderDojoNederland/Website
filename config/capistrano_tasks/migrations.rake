namespace :coderdojo do
    task :migrate do
        on roles(:all) do
            within release_path do
                info 'Doing migrations'

                execute :php, fetch(:symfony_console_path), 'doctrine:migrations:migrate', '--no-interaction', fetch(:symfony_console_flags)
            end
        end
    end
end