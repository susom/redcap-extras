#!/bin/bash

# Contributors:
#    Christopher P. Barnes <senrabc@gmail.com>
#    Andrei Sura: github.com/indera
#    Mohan Das Katragadda <mohan.das142@gmail.com>
#    Philip Chase <philipbchase@gmail.com>
#    Ruchi Vivek Desai <ruchivdesai@gmail.com>
#    Taeber Rapczak <taeber@ufl.edu>
#    Josh Hanna <josh@hanna.io>

# Copyright (c) 2015, University of Florida
# All rights reserved.
#
# Distributed under the BSD 3-Clause License
# For full text of the BSD 3-Clause License see http://opensource.org/licenses/BSD-3-Clause

export DEBIAN_FRONTEND=noninteractive

# Indicate where the vagrant folder is mounted in the guest file system
SHARED_FOLDER=/vagrant

# Use the latest redcap*.zip file in $SHARED_FOLDER
REDCAP_ZIP=`ls $SHARED_FOLDER/redcap*.zip | grep "redcap[0-9]\{1,2\}\.[0-9]\{1,2\}\.[0-9]\{1,2\}\.zip" | sort -n | tail -n 1`

# import helper functions
. $SHARED_FOLDER/bootstrap_functions.sh

install_prereqs
install_redcap
install_utils
check_redcap_status

