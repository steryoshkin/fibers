# fibers

Итак....

Ubuntu Server

отсюда http://www.ubuntu.com/download/server качаем 14.04.3 LTS 64 битный

ставим

    apt-get install openssh-server
    apt-get install apache2 git curl php5-curl
    wget -qO- https://apt.boundlessgeo.com/gpg.key | apt-key add -
    echo "deb https://apt.boundlessgeo.com/suite/latest/ubuntu/ trusty main" > /etc/apt/sources.list.d/opengeo.list
    apt-get update
    apt-get install opengeo-server
    apt-get install php5-pgsql
    mv /var/www/html /var/www/html_old
    git clone https://github.com/steryoshkin/fibers /var/www/html
    cd /var/www/html
    chown -R www-data. *


добавить в /etc/postgresql/9.3/main/pg_hba.conf

    host    all             postgres        127.0.0.1/32            trust
    host    all             opengeo         127.0.0.1/32            trust
    host    all             all             внешний_ip/32         password

редактировать /etc/postgresql/9.3/main/postgresql.conf

    listen_addresses = '*'

редактировать /etc/php5/apache2/php.ini

    short_open_tag = On

Создаём базу

    sudo -u postgres psql < create_user.sql
    sudo -u postgres psql < create_fib.sql
    sudo -u postgres psql -d fib -с "CREATE EXTENSION postgis;"
    sudo -u postgres psql -d fib < sql/fib/create_fibers.sql
    
Создаём таблицы, админские логин/пасс в create_users.sql
    
    cat sql/fib/fibers/* | sudo -u postgres psql -d fib

редактировать fibers/engine/setup.php

    $host=$_SERVER['SERVER_ADDR'];
    $addr_obl='Кемеровская область';

где $host - ip адрес или хост веб сервера, $addr_obl - область, город, которые подставляются в nominatim, можно проверить https://nominatim.openstreetmap.org/

редактировать fibers/js/action.js

    var host = '172.17.2.249';

где host аналогично $host

редактировать fibers/geomap.php

    $lon=(@$_GET['lon']?$_GET['lon']:'73.43708');
    $lat=(@$_GET['lat']?$_GET['lat']:'61.257358');

где $lon - долгота, $lat - широта, править цифры 73.43708 и 61.257358

по дефолту логин пасс в опенгео admin/geoserver

заходим http://ip_адрес_либо_хост:8080/geoserver/web/

добавляем стили

Добавить новый стиль:
наименование: cable
копируем стиль из файка cable.sld

аналогично cable_reserve и node

хранилища -> добавить новое хранилище -> PostGis:

        наименование источника: postgis
        база: fib
        схема: fibers
        логин: st
        пасс: pass

добавим слои

        Слои -> Новый слой -> cable -> опубликовать
        охваты -> Вычислить из родного охвата
        публикации -> Стили по умолчанию -> cable
        Сохранить

аналогично cable_reserve и node

Делаем группу слоёв

Группы слоёв -> Добавить новую группу слоёв:

        название: map
        слои -> добавить слой -> cable, cable_reserve, node
        Контрольная система координат -> EPSG:4326 -> Генерировать охват
        Сохранить

для возможности редактировать карту делаем полный доступ по wfs

        Сервисы -> wfs -> Уровень обслуживания -> Полный (с блокировками)
        Отправить

Заходим в Редактор карты на главной странице программы, если всё сделали правильно - всё показывает.

Изначально центр непойми где, его можно изменить в файле /usr/share/opengeo/geoexplorer/WEB-INF/app/templates/composer.html -> center: [0, 0] и zoom: 2

логинпасс как в геосервер

        выделяем слой node, редактировать -> создать
        выбираем точку -> сохранить

после чего можно добавлять в программе

Удалать в редакторе карты ничего нельзя, можно только добавлять узлы и редактировать кабель

перед работой необходимо заполнить справочники адресов

пс: Начал накидывать полноценную инструкцию, но не сохранил, в итоге набросал как попало. позже поправл.

skype: sergey_teryoshkin
