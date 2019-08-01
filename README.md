Test for ETPGPB
---------------

Use Nested Set implementation for  hierarchical data support (you may find Materialized Path implementation on 
other git branch).

Go to `http://localhost:8080/` after cloning and running Docker containers with service to see API endpoints. 

Install
-------

`git clone https://github.com/unitedt/etpgpb-test.git`

`cd etpgpb-test`

`docker-compose pull`

`docker-compose up -d`

This launches several Docker containers with PostgreSQL, nginx, php-frm and the rest stuff. At first you should load 
`data.xml` file from `https://data.mos.ru/classifier/7710168515-obshcherossiyskiy-klassifikator-produktsii-po-vidam-ekonomicheskoy-deyatelnosti-okpd-2-ok-034-2014-kpes-2008?versionNumber=1&releaseNumber=1&pageNumber=1731&countPerPage=10` 
(it's available in root of git working copy) to API endpoint `/entries/load-xml` 
(possibly using browser from webpage) to start work.

To see the container's logs run:

`docker-compose logs -f # follow the logs`

Tests
-----

First of all, you need to upload data.xml file to database through http://localhost:8080/

Then run:

`docker-compose exec php bin/phpunit`

Credits
-------

Created by Denis Chuprunov denis@chuprunov.name
