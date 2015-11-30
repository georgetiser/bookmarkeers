# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

	# A fix for a weird "echoed text" problem
	config.ssh.insert_key = false
	config.ssh.username = "vagrant"
	config.ssh.password = "vagrant"

    config.vm.box = "scotch/box"
    config.vm.network "private_network", ip: "192.168.44.14"
    config.vm.hostname = "FortyfourFourteen"
    config.vm.synced_folder ".", "/var/www", :mount_options => ["dmode=777", "fmode=666"]

    #config.vm.provision "shell", inline: <<-SHELL
    #SHELL
end
