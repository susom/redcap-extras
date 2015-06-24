#!/bin/bash
# travis-before-install.sh
set -ev

# Decrypt the private key
openssl aes-256-cbc -K $encrypted_278829cc3907_key -iv $encrypted_278829cc3907_iv -in id_rsa.enc -out ~/.ssh/id_rsa.github -d

# Make the private key the default for ssh authentication for request to github.com
chmod 600 ~/.ssh/id_rsa.github
cat <<EOF>> ~/.ssh/config
Host *
    StrictHostKeyChecking  no

Host    github.com
    Hostname        github.com
    IdentityFile    ~/.ssh/id_rsa.github
    IdentitiesOnly  yes
    StrictHostKeyChecking  no
EOF

chmod 600 ~/.ssh/config

sudo mkdir -p /root/.ssh
sudo chmod 700 /root/.ssh
sudo cp ~travis/.ssh/config /root/.ssh/
sudo cp ~travis/.ssh/id_rsa.github /root/.ssh/
sudo chmod 600 /root/.ssh/id_rsa.github

# Destination of redcap*.zip
: ${SHARED_FOLDER:=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )}

# Download a REDCap.zip to install
OLDWD=`pwd`
MYTEMP=`mktemp -d`
cd $MYTEMP
git clone git@github.com:ctsit/redcap-lts.git
cd redcap-lts
REDCAP_FOLDER=`pwd`

# Verify that the zip file exists specified by Environment Variable exists
if [ ! -e $CI_REDCAP_ZIP ]; then
	echo "The requested zip file, $CI_REDCAP_ZIP, does not exist at $REDCAP_FOLDER"
	exit 1
fi

# copy the REDCap zip file to where the install_redcap function expects it
echo "Using $REDCAP_FOLDER/$CI_REDCAP_ZIP"
cp $REDCAP_FOLDER/$CI_REDCAP_ZIP $SHARED_FOLDER/

# restore the old working directory
cd $OLDWD
