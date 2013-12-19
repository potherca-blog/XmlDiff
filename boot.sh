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

    mv /app/www/* /app
    mv /app/web/* /app/www
}
# ------------------------------------------------------------------------------

# ------------------------------------------------------------------------------
function runComposer() {
    echo "-----> Installing Composer dependencies"
    COMPOSER_URL="http://getcomposer.org/composer.phar"
    curl --silent --max-time 60 --location "$COMPOSER_URL" > www/composer.phar
    www/composer.phar install --prefer-source
}
# ------------------------------------------------------------------------------
  
# ------------------------------------------------------------------------------
function runCleanup() {
    echo '=====> Running Boot Script'
    runComposer
    cleanupAfterPhpBuildpack
    echo '=====> done.'
}
# ==============================================================================

runCleanup

#EOF
