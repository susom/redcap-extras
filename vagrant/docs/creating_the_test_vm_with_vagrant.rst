Creating the Test VM With Vagrant
=================================

Purpose
-------

The "vagrant" folder was created with the goal of making testing `REDCap <http
://project-redcap.org/>`__ and REDCap extensions as easy as possible.  The
"vagrant" folder contains the `Vagrantfile <../vagrant/Vagrantfile>`__ which
allows you to start a virtual machine capable of running the `REDCap software
<http://http://www.project-redcap.org>`__.  This virtual machine will install
Apache and MySQL software without any user intervention.  If provided with a
the REDCap binaries in the form of a redcap*.zip file Vagrant will install and
configure REDCap as well.

There are a few important things to note before proceeding with running
RED-I to import data into a sample REDCap project:

-  You have to obtain the REDCap software from http://project-redcap.org/
-  You have to install the **Vagrant** software
-  You have to install the **Virtual Box** software or another virtual machine provider.  For this discussion we will assume Virtual Box is the virtual machine provider for Vagrant.

Steps
-----

1. Install Vagrant and Virtual Box
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

On a Linux machine run:

-  sudo apt-get install vagrant
-  sudo apt-get install virtualbox

On a Mac OSX machine:

-  Download and install vagrant from
   https://www.vagrantup.com/downloads.html
-  Download and install the latest virtual box from
   http://download.virtualbox.org/virtualbox/

On Mac OSX users using brew can install these packages using brew cask:

- brew cask install virtualbox
- brew cask install vagrant

For more details about Vagrant software you can go to
`why-vagrant <https://docs.vagrantup.com/v2/why-vagrant/>`__ page.

2. Get your REDCap zip file
~~~~~~~~~~~~~~~~~~~

As mentioned above you have to obtain a copy of the REDCap software from http
://project-redcap.org/.  Save the file with its default name to ./vagrant
folder.  This ensures that in the provisioning script `bootstrap.sh
<../vagrant/bootstrap.sh>`__ script can extract the files to the virtual
machine path "**/var/www/redcap**\ ".

If you put multiple redcap*.zip files in the vagrant folder, the provisioning
script will use the one with the highest version number.

3. Start the VM
~~~~~~~~~~~~~~~

Follow this procedure to start the REDCap VM:

.. raw:: html

   <pre>
   # must be in the redi/vagrant/ directory
   cd ./vagrant
   vagrant up
   </pre>

Vagrant will instantiate and provision the new VM. The initial download of the
box file is slow, but this need happen only occur once.  Vagrant will cache
the box file indefinitely.  With the box file downloaded, the REDCap VM can be
built from scratch in 2-3 minutes.  If it completes successfully you should a
message like this at the end of the log:

.. raw:: html

   <pre>
    ==> default: Checking if redcap application is running...
    ==> default:      <b>Welcome to REDCap!</b>
    ==> default: Please try to login to REDCap as user 'admin' and password: 'password'
   </pre>

The REDCap web application should be accessible in the browser at

http://localhost:8080/redcap/

If port 8080 is already in use on your computer Vagrant will choose a
different port automatically. Read the log of "vagrant up" and note the port
to be used.

The REDCap instance will be setup with the sample projects that shipped with
that REDCap zip file.


4. Verify the VM is running via the console
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

You can also verify the virtual machine is working properly by accessing it
at the console using:

.. raw:: html

   <pre>
   vagrant ssh
   </pre>

This will connect you to a shell on the virtual machine.

You can check the REDCap server from the console with the command ``check_redcap``.  You will see output like this if it is running correctly:

.. raw:: html

   <pre>
      vagrant@redcap:~$ check_redcap
            <b>Welcome to REDCap!</b>
   </pre>

As with any ssh session, type ``exit`` when you are done at the shell.

