APP = "bofhers_php"
ARGS = $(filter-out $@,$(MAKECMDGOALS))

all: dev

start: dev
dev:
ifdef ${ARGS}
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml up -d ${ARGS}
	$(MAKE) logs
else
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml up -d app
	$(MAKE) logs
endif

stop:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml stop

restart:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml restart

down:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml down

build:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml build

build-nocache:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml build --no-cache

ps:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml ps

bash:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml exec ${APP} bash

attach:
	docker attach docker-compose -f docker-compose.yml -f docker-compose.databases.yml ps -q ${APP}

logs:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml logs --tail 250 -f ${ARGS}

rmimages:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml down --rmi local

backup:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml run backup

restore:
	docker-compose -f docker-compose.yml -f docker-compose.databases.yml run restore

%:
	@:

.PHONY: %
