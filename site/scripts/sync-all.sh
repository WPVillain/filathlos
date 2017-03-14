#!/bin/sh

DEVDIR="web/app/uploads/"
DEVSITE="https://filathlos.dev"

PRODDIR="web@filathlos.imagewize.com:/srv/www/filathlos.imagewize.com/shared/uploads/"
PRODSITE="https://filathlos.imagewize.com"

STAGDIR="web@staging.filathlos.com:/srv/www/filathlos.imagewize.com/shared/uploads/"
STAGSITE="https://staging.filathlos.imagewize.com"

FROM=$1
TO=$2

case "$1-$2" in
  development-production) DIR="up";  FROMSITE=$DEVSITE;  FROMDIR=$DEVDIR;  TOSITE=$PRODSITE; TODIR=$PRODDIR; ;;
  development-staging)    DIR="up"   FROMSITE=$DEVSITE;  FROMDIR=$DEVDIR;  TOSITE=$STAGSITE; TODIR=$STAGDIR; ;;
  production-development) DIR="down" FROMSITE=$PRODSITE; FROMDIR=$PRODDIR; TOSITE=$DEVSITE;  TODIR=$DEVDIR; ;;
  staging-development)    DIR="down" FROMSITE=$STAGSITE; FROMDIR=$STAGDIR; TOSITE=$DEVSITE;  TODIR=$DEVDIR; ;;
  *) echo "usage: $0 development production | development staging | production development | production staging" && exit 1 ;;
esac

read -r -p "Would you really like to reset the $TO database and sync $DIR from $FROM? [y/N] " response

if [[ "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
  cd ../ &&
  wp "@$TO" db export &&
  wp "@$FROM" db export - | wp "@$TO" db import - &&
  wp "@$TO" search-replace "$FROMSITE" "$TOSITE" --skip-columns=guid &&
  rsync -az --progress "$FROMDIR" "$TODIR"
fi
