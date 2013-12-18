# ==============================================================================
# Custom Heroku Boot script
#
# The PHP buildpack moves everything in root to the `www` directory so we need 
# move everything that does not belong in public back to the app's root 
# directory
# ==============================================================================


# ==============================================================================
# FUNCTIONS
# ==============================================================================
function keep() {
    echo "----->     Attempt to move ${1}"
    if [ -f "www/${1}" ]; then
      mv -v --target-directory="${HOME}/" "www/${1}"
    elif [ -d "www/${1}" ]; then
      mv -v --target-directory="${HOME}/" "www/${1}"
    else
        echo "----->          !     FAILED TO MOVE ${1} to ${HOME}"
    fi
}
# ------------------------------------------------------------------------------


# ------------------------------------------------------------------------------
function cleanupAfterPhpBuildpack() {
    echo '-----> Cleaning up after PHP buildpack'

    # Make sure any 'hidden' files also get moved
    shopt -s dotglob

    keep lib
    keep .git
    keep vendor
}
# ------------------------------------------------------------------------------


# ------------------------------------------------------------------------------
function runCleanup() {
    echo '=====> Cleaning up after buildpacks'
    cleanupAfterPhpBuildpack
    echo '=====> Cleanup done.'
}
# ==============================================================================

runCleanup

#EOF
