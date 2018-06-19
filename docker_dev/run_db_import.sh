#!/bin/bash
docker exec -it siep-mysql bash -c "cd /home && mysql -u root -p siep < siep.sql"
