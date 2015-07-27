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

# Increase verbosity if running on CI
[ "true" == "$CI" ] && set -ev

export DEBIAN_FRONTEND=noninteractive

# Indicate where this folder is mounted in the guest file system
# This can also be overwritten to lo
: ${SHARED_FOLDER:=/vagrant}

# Finds the REDCap zip with the highest version number and returns its basename
function latest_redcap_zip()
{
	local zip=`ls $SHARED_FOLDER/redcap*.zip | grep "redcap[0-9]\{1,2\}\.[0-9]\{1,2\}\.[0-9]\{1,2\}\.zip" | sort -n | tail -n 1`
	echo `basename $zip`
}

# Use the latest redcap*.zip file in $SHARED_FOLDER unless REDCAP_ZIP is specified
REDCAP_ZIP=${REDCAP_ZIP:-$(latest_redcap_zip)}

# import helper functions
. $SHARED_FOLDER/bootstrap_functions.sh

install_prereqs
install_redcap
# Install utilities unless we are on the CI server
[ "true" != "$CI" ] && \
	install_utils
check_redcap_status
