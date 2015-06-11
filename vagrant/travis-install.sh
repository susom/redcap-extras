#!/bin/bash
set -ev

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

# Note where this script lives
SHARED_FOLDER=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

# Download a REDCap.zip to install
OLDWD=`pwd`
MYTEMP=`mktemp -d`
cd $MYTEMP
git clone git@github.com:ctsit/redcap-lts.git
cd redcap-lts
REDCAP_FOLDER=`pwd`

# Use the latest redcap*.zip file in the current folder
REDCAP_ZIP=`ls redcap*.zip | grep "redcap[0-9]\{1,2\}\.[0-9]\{1,2\}\.[0-9]\{1,2\}\.zip" | sort -n | tail -n 1`

# unless the Travis CI Environment variables ask for a zip file that exists
if [ -n "$CI_REDCAP_ZIP" ]; then
    if [ -e $CI_REDCAP_ZIP ]; then
        REDCAP_ZIP=$CI_REDCAP_ZIP
    fi
fi

# copy the REDCap Zip to the location the install_redcap function expects it
cp $REDCAP_FOLDER/$REDCAP_ZIP $SHARED_FOLDER/

# restore the old working directory
cd $OLDWD

# import helper functions
. $SHARED_FOLDER/bootstrap_functions.sh

install_prereqs
install_redcap
check_redcap_status
