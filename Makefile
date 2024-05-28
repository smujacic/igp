build: 
	docker-compose build

start:
	docker-compose up -d

stop:
	docker-compose down

build-db:
	docker-compose exec db bash -c 'mysql -u igp -pigp igp < /docker-entrypoint-initdb.d/init.sql'

env:
	cd app && cp .env_dev .env

