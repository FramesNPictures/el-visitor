#!/bin/zsh

cd data

if [ ! -d ip-location-db ]
then
  git clone https://github.com/sapics/ip-location-db.git
else
  cd ip-location-db
  git pull
  cd ..
fi

cp -f ip-location-db/dbip-city-mmdb/dbip-city-ipv4.mmdb ./
cp -f ip-location-db/dbip-city-mmdb/dbip-city-ipv6.mmdb ./
cp -f ip-location-db/geolite2-city-mmdb/geolite2-city-ipv4.mmdb ./
cp -f ip-location-db/geolite2-city-mmdb/geolite2-city-ipv6.mmdb ./
cp -f ip-location-db/asn-mmdb/*.mmdb ./