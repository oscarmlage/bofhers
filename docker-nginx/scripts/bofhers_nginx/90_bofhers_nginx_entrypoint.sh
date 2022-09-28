#!/usr/bin/env bash
set -e

attempts=0
max_attempts=15
readonly OK_ATTEMPTS=990
readonly POLL_TIME=30

function try_connect()
{
  wait_for_it.sh "${NGINX_FASTCGI_PASS}" -s -t "${POLL_TIME}" -- /bin/true
}

while [[ "${attempts}" -lt "${max_attempts}" ]];
do
  attempts=$((attempts + 1))

  echo "Trying to connect to ${NGINX_FASTCGI_PASS}..."

  if try_connect >& /dev/null;
  then
    attempts="${OK_ATTEMPTS}"
  else
    echo "Connection attempt to ${NGINX_FASTCGI_PASS} failed (#${attempts} of ${max_attempts})."
  fi
done

if [[ ! "${attempts}" -eq "${OK_ATTEMPTS}" ]];
then
  echo "Aborting connection attempt to ${NGINX_FASTCGI_PASS} after ${max_attempts} tries."
  exit 1
fi

echo "Connection to ${NGINX_FASTCGI_PASS} successful. Booting up container..."
