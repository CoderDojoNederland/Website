namespace :coderdojo do
    task :bower_symlink do
        on roles(:all) do
            within release_path do
                info 'Creating bower symlink'
                puts "#{release_path}"
                puts "#{current_path}"

                execute :ln, "-fs", "#{release_path}/node_modules/@bower_components", "#{release_path}/web/vendors"
            end
        end
    end
end