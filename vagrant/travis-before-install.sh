#!/bin/bash
# travis-before-install.sh

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

